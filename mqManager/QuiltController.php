<?php

namespace App\Http\Controllers;

use App\Models\Quilt;
use App\Services\EmailCopyService;
use App\Services\QuiltService;
use App\Models\QuiltUpdate;
use App\Jobs\GenerateQrCodePdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Milon\Barcode\DNS1D;
use Milon\Barcode\DNS2D;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;


class QuiltController extends Controller
{
    public function __construct(
        public QuiltService $quiltService,
        public EmailCopyService $emailCopyService
    ) {
    }
    public function getQuilt(Quilt $quilt)
    {
        $emailCopies = $this->emailCopyService->getEmailCopies();
        $quiltProp = $quilt;
        return Inertia::render('ViewQuilt', compact('quiltProp', 'emailCopies'));
    }

    public function getCheckedInQuilts()
    {
        $checkedInQuilts = QuiltUpdate::where('checked_in', true)
            ->with('quilt')
            ->get()
            ->pluck('quilt')
            ->unique('id')
            ->values();

        $count = $checkedInQuilts->count();
        return response()->json([
            'quilts' => $checkedInQuilts,
            'count' => $count
        ], 200);
    }

    public function getQuilts()
    {
        $quiltLists = $this->quiltService->getQuiltLists();
        return Inertia::render('ViewQuilts', compact('quiltLists'));
    }

    public function AddQuiltUpdate(Request $request, Quilt $quilt)
    {
        Log::info('update request', [$request]);
        $this->quiltService->newQuiltUpdate($request, $quilt);
        $quilt = $quilt->fresh();
        return response($quilt);
    }

    public function printQuiltTag($id)
    {
        ini_set('max_execution_time', 120);


        $quilt = Quilt::find($id);

        if (!$quilt) {
            Log::error("Quilt not found with ID: $id");
            abort(404, 'Quilt not found');
        }

        $dns1d = new DNS1D();
        $dns2d = new DNS2D();

        $barcode = $dns1d->getBarcodePNG($quilt->orderNumber, 'C39');

        $quiltUrl = url("/quilts/{$id}");
        $qrCode = $dns2d->getBarcodeHTML($quiltUrl, 'QRCODE', 3, 3);

        $data = [
            'quilt' => $quilt,
            'barcode' => $barcode,
            'qrcode' => $qrCode,
            // 'phone' => $quilt->order->phone_number ?? 'N/A'
        ];

        $pdf = FacadePdf::loadView('quiltTag', $data);
        $pdf->setPaper([0, 0, 216, 360]);

        return $pdf->stream('tag_' . $quilt->orderNumber . '.pdf');
    }

    public function printQueue(Request $request)
    {
        ini_set('max_execution_time', 30);

        $quiltIds = $request->input('ids', []);

        if (empty($quiltIds)) {
            Log::error("No quilt IDs provided");
            abort(400, 'No quilt IDs provided');
        }

        $quilts = Quilt::whereIn('id', $quiltIds)
            ->with(['order'])
            ->get(['id', 'shopify_order_id', 'pattern', 'has_binding', 'binding_notes', 'thread_color', 'length', 'width']);

        if ($quilts->isEmpty()) {
            Log::error("No quilts found with the provided IDs");
            abort(404, 'No quilts found');
        }

        $orderNumbers = $quilts->pluck('order.shopify_id')->unique();
        if ($orderNumbers->count() !== $quilts->count()) {
            $duplicateOrders = $quilts->groupBy('order.name')
                ->filter(function ($group) {
                    return $group->count() > 1;
                })
                ->keys()
                ->implode(', ');

            Log::error("Quilts with duplicate order numbers found: $duplicateOrders");
            abort(400, "Quilts with duplicate order numbers found: $duplicateOrders");
        }

        $dns1d = new DNS1D();
        $dns2d = new DNS2D();

        $quiltData = $quilts->map(function ($quilt) use ($dns1d, $dns2d) {
            $barcode = $dns1d->getBarcodePNG($quilt->orderNumber, 'C39');
            $quiltUrl = url("/quilts/{$quilt->id}");
            $qrCode = $dns2d->getBarcodeHTML($quiltUrl, 'QRCODE', 3, 3);


            $customerName = $quilt->order->first_name . ' ' . $quilt->order->last_name;
            $phone = $quilt->order->phone_number ?? 'N/A';


            return [
                'quilt' => $quilt,
                'barcode' => $barcode,
                'qrcode' => $qrCode,
                'customerName' => $customerName,
                'phone' => $phone
            ];
        });

        $pdf = FacadePdf::loadView('printQueue', ['quilts' => $quiltData]);
        $pdf->setPaper([0, 0, 216, 360]);

        return $pdf->stream('print_queue.pdf');
    }



    public function search(Request $request)
    {
        $searchQuery = $request->input('query');

        $results = Quilt::whereHas('order', function ($query) use ($searchQuery) {
            $query->where('first_name', 'like', "%$searchQuery%")
                ->orWhere('last_name', 'like', "%$searchQuery%")
                ->orWhere('name', 'like', "%$searchQuery%");
        })
            ->orWhere('id', $searchQuery)
            ->get();

        return response()->json($results);
    }
}
