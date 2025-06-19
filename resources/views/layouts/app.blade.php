<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pdfTitle ?? ''}}</title>

    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
            padding: 0;
        }
        .text-center {
            text-align: center;
        }
        .text-muted {
            color: #6c757d;
        }
        .py-4 {
            padding-top: 1.5rem;
            padding-bottom: 1.5rem;
        }
        small {
            font-size: 80%;
        }
    </style>
</head>
<body>
    @yield('content')

    <footer class="text-center text-muted py-4">
        <small>&copy; {{ date('Y') }} {{ $companyName }}. All rights reserved.</small>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
