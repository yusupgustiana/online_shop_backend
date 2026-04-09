<!DOCTYPE html>
<html lang="id">
<head>
    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'App')</title>

    <!-- GOOGLE FONT -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

    <!-- TAILWIND -->
    @vite('resources/css/app.css')
    <!-- GLOBAL STYLE -->
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: #f5f7f9;
            color: #333;
        }

        a {
            text-decoration: none;
        }

        /* MAIN WRAPPER */
        .app-wrapper {
            width: 100%;
            min-height: 100vh;
        }

        /* NAVBAR (optional future use) */
        .navbar {
            height: 60px;
            background: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        }

        /* CONTENT */
        .content {
            padding: 0;
        }

        /* FOOTER */
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

    <!-- OPTIONAL NAVBAR -->
    {{-- 
    <div class="navbar">
        <div>Logo</div>
        <div>Menu</div>
    </div>
    --}}

    <!-- CONTENT -->
    <div class="content">
        @yield('content')
    </div>

    <!-- FOOTER -->
    <div class="footer">
        © {{ date('Y') }} Y.G Store
    </div>

</div>

@stack('scripts')
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>