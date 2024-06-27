<?php

namespace App\Services;


use App\Models\Quilt;
use App\Models\QuiltUpdate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class QuiltService
{
    public function createQuiltFromWebhook($webhook)
    {
        $attributes = [
            'shopify_order_id' => $webhook['id'],
        ];

        $values = [
            'email' => $webhook['contact_email'] ?? '-',
            'shopify_order_item_id' => 0,
            'pattern' => '',
            'has_binding' => 0,
            'binding_notes' => '',
            'thread_color' => 'Quilters choice, be a wise one.',
            'expedited' => 0,
            'length' => 0,
            'width' => 0,
            'backing_included' => 0,
            'backing_notes' => '',
            'order_note' => $webhook['note'] ?? '',
        ];

        foreach ($webhook['line_items'] as $item) {
            $this->processLineItem($item, $values);
        }

        $quilt = Quilt::updateOrCreate($attributes, $values);
        $quilt = $quilt->fresh();

        $quiltStatus = QuiltUpdate::where('quilt_id', $quilt->id);
        Log::info('quilt status', ['body' => $quiltStatus]);

        if (!$quiltStatus) {
            QuiltUpdate::create([
                'status' => 'Order Placed Date',
                'location' => 'in transit',
                'notes' => $webhook['note'] ?? '',
                'quilt_id' => $quilt->id,
            ]);
        }
    }

    private function processLineItem($item, &$values)
    {
        switch ($item['name']) {
            case 'Quilting Charge':
                $this->processQuiltingCharge($item, $values);
                break;
            case 'Fabric Item':
                $values['backing_notes'] = $item['properties'][0]['value'];
                break;
            case 'My own backing in multiple pieces':
                $values['backing_included'] = 1;
                $values['backing_notes'] = 'My own backing in multiple pieces';
                break;
            case 'My own backing in one piece':
                $values['backing_included'] = 1;
                $values['backing_notes'] = 'My own backing in one piece';
                break;
            case 'Trim and bind my quilt':
                $values['has_binding'] = 1;
                $values['binding_notes'] = 'Trim and bind my quilt';
                break;
            case 'Trim my quilt but do not bind':
                $values['has_binding'] = 1;
                $values['binding_notes'] = 'Trim my quilt but do not bind';
                break;
            case 'Priority Quilting':
                $values['expedited'] = 1;
                break;
        }

        if ($item['sku'] == 'pattern') {
            $values['pattern'] = $item['name'];
        }

        if ($item['sku'] == 'thread') {
            $values['thread_color'] = $item['name'];
        }
    }

    private function processQuiltingCharge($item, &$values)
    {
        $widthKey = array_search('Width', array_column($item['properties'], 'name'));
        if (array_key_exists($widthKey, $item['properties'])) {
            $values['width'] = (int) filter_var($item['properties'][$widthKey]['value'], FILTER_SANITIZE_NUMBER_INT);
        }

        $lengthKey = array_search('Height', array_column($item['properties'], 'name'));
        if (array_key_exists($lengthKey, $item['properties'])) {
            $values['length'] = (int) filter_var($item['properties'][$lengthKey]['value'], FILTER_SANITIZE_NUMBER_INT);
        }
    }

    public function updateQuilt($quilt, $updates)
    {
        $quilt->fill($updates);
        $quilt->save();
        return $quilt->fresh();
    }

    private function getFileName($image, $namePrefix)
    {
        list($type, $file) = explode(';', $image);
        list(, $extension) = explode('/', $type);
        list(, $file) = explode(',', $file);
        $result['name'] = $namePrefix . '.' . $extension;
        $result['file'] = $file;
        return $result;
    }

    public function newQuiltUpdate(Request $request, Quilt $quilt): void
    {
        DB::transaction(function () use ($request, $quilt) {
            if ($request->photo) {
                $image = $request->photo;
                $slug = time() . $quilt->id;
                $quiltImg = $this->getFileName($image, $slug);
                $disk = config('filesystems.default');

                Storage::disk($disk)->put('public/quilts/' . $quiltImg['name'], base64_decode($quiltImg['file']), 'public');

                $quiltImgName = asset('storage/quilts/' . $quiltImg['name']);
            }


            $update = new QuiltUpdate;
            $update->status = $request->status;
            $update->location = $request->location;
            $update->notes = $request->internalNotes ?? null;
            $update->quilt_id = $quilt->id;
            $update->admin_id = $request->updatedBy;
            $update->update_date = $request->statusChangeDate;
            $update->img = $quiltImgName ?? null;
            $update->email_sent = $request->sendEmail ? $request->emailCopy['email_copy'] : 'no';
            $update->checked_in = $request->checkedIn ?? false;

            $update->save();

            $quilt->last_update = $update->status;
            $quilt->last_update_id = $update->id;
            if ($update->status === 'received') {
                $quilt->received = $request->statusChangeDate;
            }
            $quilt->save();
        });
    }


    public function getQuiltLists()
    {
        $awaitingQuilts = Quilt::whereNull('received')->with('order')->paginate(15, ['*'], 'awaiting_page');
        $receivedQuilts = Quilt::whereNotNull('received')->where('last_update', '!=', 'shipped')->with('order')->paginate(15, ['*'], 'received_page');
        $stagedQuilts = Quilt::where('last_update', 'staged')->with('order')->paginate(15, ['*'], 'staged_page');
        $quiltedQuilts = Quilt::where('last_update', 'quilted')->with('order')->paginate(15, ['*'], 'quilted_page');
        $trimmedQuilts = Quilt::where('last_update', 'trimmed')->with('order')->paginate(15, ['*'], 'trimmed_page');
        $boundQuilts = Quilt::where('last_update', 'bound')->with('order')->paginate(15, ['*'], 'bound_page');
        $shippedQuilts = Quilt::where('last_update', 'shipped')->with('order')->paginate(15, ['*'], 'shipped_page');

        return [
            'notReceivedQuilts' => $awaitingQuilts,
            'receivedQuilts' => $receivedQuilts,
            'stagedQuilts' => $stagedQuilts,
            'quiltedQuilts' => $quiltedQuilts,
            'trimmedQuilts' => $trimmedQuilts,
            'boundQuilts' => $boundQuilts,
            'shippedQuilts' => $shippedQuilts,
        ];
    }


    public function getQueueQuilts()
    {
        $nextWeekMonday = date('Y-m-d', strToTime('monday next week'));
        $thirdMonday = date('Y-m-d', strtotime($nextWeekMonday . '+ 7 days'));
        $thisWeekQuilts = [];
        $nextWeekQuilts = [];
        $dangerZoneQuilts = [];
        $potentialQuilts = Quilt::whereNotNull('received')->where('last_update', '!=', 'shipped')->get();

        foreach ($potentialQuilts as $quilt) {
            $receivedDate = $quilt->received;
            if ($quilt->dueDate <= $nextWeekMonday) {
                $thisWeekQuilts[] = $quilt;
            }

            if ($quilt->dueDate > $nextWeekMonday && $quilt->dueDate < $thirdMonday) {
                $nextWeekQuilts[] = $quilt;
            }

            if (strtotime($quilt->dueDate) - strtotime($receivedDate) < (24 * 60 * 60)) {
                $dangerZoneQuilts[] = $quilt;
            }
        }


        return [
            'thisWeek' => $thisWeekQuilts,
            'nextWeek' => $nextWeekQuilts,
            'dangerZone' => $dangerZoneQuilts
        ];
    }
}
