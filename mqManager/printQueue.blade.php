<!DOCTYPE html>
<html>

<head>
    <title>Print Queue</title>
    <style>
        @page {
            margin: 0;
            size: 3in 5in;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: white;
        }

        .page {
            width: 3in;
            height: 5in;
            padding: 10px;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            page-break-before: always;
            background-color: white;
        }

        .quilt-info {
            width: 100%;
            text-align: left;
        }

        h2 {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 4px;
        }

        p {
            margin: 2px 0;
            font-size: 10px;
        }

        .qrcode {
            margin-top: 8px;
            display: flex;
            justify-content: center;
            align-items: center;
            width: 60px;
            height: 60px;
        }

        .text-xs {
            font-size: 8px;
        }

        .space-y-2>*+* {
            margin-top: 4px;
        }
    </style>
</head>

<body>
    @foreach ($quilts as $data)
    <div class="page">
        <div class="quilt-info">
            <h2>Quilt ID: {{ $data['quilt']->id }}</h2>
            <div class="space-y-2 text-xs">
                <p><strong>Order ID:</strong> {{ $data['quilt']->shopify_order_id }}</p>
                <p><strong>Customer Name:</strong> {{ $data['customerName'] ?? 'N/A' }}</p>
                <p><strong>Phone:</strong> {{ $data['phone'] ?? 'N/A' }}</p>
                <p><strong>Due Date:</strong> {{ $data['quilt']->dueDate ?? 'N/A' }}</p>
                <p><strong>Received Date:</strong> {{ $data['quilt']->receivedDate ?? 'N/A' }}</p>
                <p><strong>Location:</strong> {{ $data['quilt']->location ?? 'Awaiting Receipt' }}</p>
                <p><strong>Expedited:</strong> {{ $data['quilt']->expedited ? 'True' : 'False' }}</p>
                <p><strong>Pattern:</strong> {{ $data['quilt']->pattern }}</p>
                <p><strong>Thread Color:</strong> {{ $data['quilt']->thread_color }}</p>
                <p><strong>Binding:</strong> {{ $data['quilt']->binding_notes }} {{ $data['quilt']->has_binding === 1 ? "(Binding Included)" : "(Not Included)" }}</p>
                <p><strong>Backing:</strong> {{ $data['quilt']->backing_included }}</p>
                <p><strong>Size:</strong> {{ $data['quilt']->length }}x{{ $data['quilt']->width }}</p>
            </div>
        </div>
        <div class="qrcode">
            {!! $data['qrcode'] !!}
        </div>
    </div>
    @endforeach
</body>

</html>