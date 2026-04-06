<!DOCTYPE html>
<html lang="en">

<head>
    <style>
        [x-cloak] { display: none !important; }
    </style>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>

    <!-- Optional Bootstrap (kalau masih dipakai) -->
    <link rel="stylesheet" href="{{ asset('library/bootstrap/dist/css/bootstrap.min.css') }}">

    @stack('style')
</head>

<body class="bg-gray-100">

    @yield('main')

    <!-- Tambahkan ini supaya script dari child view jalan -->
    @stack('scripts')
</body>
</html>