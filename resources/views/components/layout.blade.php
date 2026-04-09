<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite('resources/css/app.css')
</head>
<body style="background: var(--color-surface-secondary); min-height: 100vh; display: flex; flex-direction: column;">

    <x-header />

    {{ $slot }}

    <x-footer />

    <x-main-content.toast />

    @stack('scripts')
    <script type="module" src="{{ Vite::asset('resources/js/app.js') }}"></script>
</body>
</html>