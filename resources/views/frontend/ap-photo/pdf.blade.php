<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $item->apPhoto->category->name ?? 'AP Photo' }}</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            text-align: center;
            font-family: 'Helvetica', sans-serif;
        }
        .container {
            width: 100%;
            height: 100%;
        }
        img {
            max-width: 100%;
            height: auto;
            display: block;
            margin: 0 auto;
        }
        .info {
            margin-top: 20px;
            color: #666;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <img src="data:image/jpeg;base64,{{ base64_encode(Storage::disk('public')->get($item->file_path)) }}">
        
        <div class="info">
            <h2>{{ $item->apPhoto->category->name ?? 'AP Photo' }}</h2>
            <p>{{ $item->apPhoto->description }}</p>
            <p>Source: United News of Bangladesh (UNB)</p>
            <p>Date: {{ $item->created_at->format('M d, Y') }}</p>
        </div>
    </div>
</body>
</html>
