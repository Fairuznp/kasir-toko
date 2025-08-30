<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Cetak Laporan' }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <style>
        body { background: #fff; color: #000; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        @yield('content')
        <div class="no-print mt-4">
            <button class="btn btn-primary" onclick="window.print()">Print</button>
            <a href="javascript:window.close()" class="btn btn-secondary">Tutup</a>
        </div>
    </div>
</body>
</html>
