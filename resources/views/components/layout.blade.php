<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>
        {{  config('app.name') }}
    </title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;1,100;1,200;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite('resources/css/app.css')
</head>
<body class="bg-[#ffedd5] font-mono relative habit-bg min-h-screen flex flex-col justify-between">

    {{-- HEADER --}}
    <x-header />
    

    {{ $slot }}

    {{-- FOOTER --}}
    <x-footer />

    {{-- TOAST --}}
    <x-toast />

    @stack('scripts')
    <script type="module" src="{{ Vite::asset('resources/js/app.js') }}"></script>
</body>
</html>