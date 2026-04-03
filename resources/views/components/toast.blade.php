@php
    $type = session()->has('success') ? 'success' : (session()->has('error') ? 'error' : 'warning');

    $message = session($type);

    $styles = match($type) {
        'success' => 'bg-green-100 border-green-400 text-green-700',
        'error' => 'bg-red-100 border-red-400 text-red-700',
        'warning' => 'bg-yellow-100 border-yellow-400 text-yellow-700',
    };
@endphp

{{-- TOAST (sempre renderizado, mas escondido) --}}
<div id="toast"
    class="hidden pointer-events-none absolute top-28 left-1/2 -translate-x-1/2 sm:left-auto sm:translate-x-0 sm:right-20 border-2 p-3 mb-4 flex gap-2 items-center transition-opacity duration-500 opacity-0">

    {{-- ICON --}}
    <div id="toast-icon"></div>

    {{-- MESSAGE --}}
    <p id="toast-message"></p>
</div>

{{-- Se tiver session, dispara automaticamente --}}
@if (session()->has('success') || session()->has('error') || session()->has('warning'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        mostrarToast("{{ $type }}", "{{ $message }}");
    });
</script>
@endif