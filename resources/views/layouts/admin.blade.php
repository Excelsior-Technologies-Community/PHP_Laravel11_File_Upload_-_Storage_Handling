<!-- resources/views/layouts/admin.blade.php -->

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Admin</title>

    <!-- Fonts / Bootstrap CSS -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- PAGE SPECIFIC STYLES (Select2 CSS etc.) -->
    @yield('styles')

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-light">

    {{-- TOP NAVIGATION --}}
    @include('layouts.navigation')

    {{-- PAGE CONTENT --}}
    <div class="container py-4">
        @yield('content')
    </div>

    <!-- jQuery REQUIRED for Select2 -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- PAGE SPECIFIC SCRIPTS (Select2 JS etc.) -->
    @stack('scripts')

</body>
</html>
