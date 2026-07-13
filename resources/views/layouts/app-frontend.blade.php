<!DOCTYPE html>
<html lang="id">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Jualin')</title>

    <!-- GOOGLE FONT -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
 <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@600;700;800&display=swap" rel="stylesheet">
    <!-- SWIPER CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">

    <!-- TAILWIND -->

@vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- GLOBAL STYLE -->
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f5f7f9;
            color: #333;
        }

        .app-wrapper {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .content {
            flex: 1;
        }

        .footer {
            text-align: center;
            padding: 15px;
            font-size: 12px;
            color: #999;
        }
    </style>

    @stack('styles')
</head>

<body>

<div class="app-wrapper">

    <!-- CONTENT -->
    <div class="content">
        @yield('content')
    </div>

    <!-- FOOTER -->
    <div class="footer">
        © {{ date('Y') }} Jualin
    </div>

</div>

<!-- ================= JS ================= -->

<!-- ALPINE -->
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

<!-- SWIPER JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

@stack('scripts')

</body>
</html>

