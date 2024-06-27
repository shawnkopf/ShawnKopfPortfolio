<!DOCTYPE html>
<html>

<head>
    <title>Quilt Tag</title>
    <style>
        body,
        html {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
            width: 100%;
        }

        .container {
            background-color: white;
            padding: 8px;
            width: 3in;
            height: 5in;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: center;
        }

        .quilt-info {
            width: 100%;
            text-align: left;
        }

        h1 {
            font-size: 0.875rem;
            font-weight: bold;
            margin: 4px 0;
            text-align: center;
        }

        p {
            margin: 2px 0;
            font-size: 0.625rem;
        }

        .qrcode {
            margin-top: 8px;
            display: flex;
            justify-content: center;
        }

        .text-xs {
            font-size: 0.625rem;
        }

        .space-y-2>*+* {
            margin-top: 4px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Quilt ID: {{ $quilt->id }}</h1>
        <div class="quilt-info space-y-2 text-xs">
            <p><strong>Order ID:</strong> {{ $quilt->shopify_order_id }}</p>
            <p><strong>Customer Name:</strong> {{ $quilt->order->first_name }} {{ $quilt->order->last_name }}</p>
            <p><strong>Phone:</strong> {{ $quilt->order->phone_number }}</p>
            <p><strong>Due Date:</strong> {{ $quilt->dueDate }}</p>
            <p><strong>Received Date:</strong> {{ $quilt->receivedDate }}</p>
            <p><strong>Location:</strong> {{ $quilt->status->location ?? 'Awaiting Receipt' }}</p>
            <p><strong>Expedited:</strong> {{ $quilt->expedited ? 'True' : 'False' }}</p>
            <p><strong>Pattern:</strong> {{ $quilt->pattern }}</p>
            <p><strong>Thread Color:</strong> {{ $quilt->thread_color }}</p>
            <p><strong>Binding:</strong> {{ $quilt->binding_notes }} {{ $quilt->has_binding === 1 ? "(Binding Included)" : "(Not Included)" }}</p>
            <p><strong>Backing:</strong> {{ $quilt->backing_included }}</p>
            <p><strong>Size:</strong> {{ $quilt->length }}x{{ $quilt->width }}</p>
        </div>
        <div class="qrcode">
            <div>
                {!! $qrcode !!}
            </div>
        </div>
    </div>
</body>

</html>