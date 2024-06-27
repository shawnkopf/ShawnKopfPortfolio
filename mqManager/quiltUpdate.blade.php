<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100 antialiased">
    <div class="max-w-2xl mx-auto p-6 bg-white rounded-lg shadow-md">
        <div class="text-center py-4">
            <img src="{{ asset('path/to/your/logo.png') }}" alt="Company Logo" class="mx-auto h-24">
        </div>
        <div class="p-6 text-gray-800">
            <h1 class="text-2xl font-bold mb-6">New Quilt Update!</h1>
            <p class="mb-4">Dear {{ $customerName }},</p>
            <p class="mb-4">We wanted to update you on the status of your quilt! Here are the latest details:</p>
            <p class="mb-4">{{ $copy }}</p>
            <p class="mb-4">Here is what your quilt looks like as of today:</p>
            <img src="{{ $base64Photo }}" alt="Quilt Photo" class="mb-4 w-full h-auto rounded-md shadow-md">
            <p class="mb-4">If you have any questions, feel free to reply to this email or contact our support team.</p>
            <p class="mb-4">Thank you for choosing our service!</p>
            <p class="mb-4">Best regards,</p>
            <p class="mb-4">{{ config('app.name') }} Team</p>
        </div>
        <div class="text-center py-4 border-t border-gray-200 text-gray-600">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>

</html>