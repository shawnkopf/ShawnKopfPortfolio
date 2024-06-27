<?php

namespace App\Http\Controllers;

use App\Mail\QuiltUpdateEmail;
use App\Models\Quilt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class QuiltUpdateController extends Controller
{

    public function sendUpdateEmail(Request $request)
    {
        try {
            // Extract base64 data
            $data = substr($request->photo, strpos($request->photo, ',') + 1);
            $base64Photo = 'data:image/png;base64,' . $data;
            $data = base64_decode($data);

            $fileName = 'quilt_update_' . time() . '.png';
            $filePath = 'public/' . $fileName;
            Storage::disk('local')->put($filePath, $data);

            // $imageUrl = Storage::url($filePath);

            $email = $request->quilt['email'];
            $quilt = Quilt::find($request->quilt['id']);
            $status = $request->status ?? 'Update!';
            $customerName = $quilt->customerName;

            // Log before sending email
            Log::info('Attempting to send update email.', [
                'email' => $email,
                'quilt' => $quilt,
                'status' => $status,
                'customerName' => $customerName,
                'imagePath' => $filePath,
                // 'imageUrl' => $imageUrl,
            ]);

            Mail::to($email)->send(new QuiltUpdateEmail($quilt, $request->copy['email_copy'], $status, $base64Photo, $customerName));

            Log::info('Update email sent.');

            return response()->json(['message' => 'Email sent successfully']);
        } catch (\Exception $e) {
            Log::error('Error sending update email: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['error' => 'Error sending update email'], 500);
        }
    }
}
