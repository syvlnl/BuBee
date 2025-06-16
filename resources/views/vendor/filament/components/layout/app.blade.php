@php
    use Filament\Facades\Filament;
@endphp

<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    @class(['dark' => filament()->hasDarkMode()])
>
<head>
    {{ filament()->renderHook('head.start') }}

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ filament()->getTitle() }}</title>

    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @filamentStyles

    {{-- ✅ Jangan pakai Tailwind CDN jika sudah pakai Vite --}}
    {{-- <script src="https://cdn.tailwindcss.com"></script> --}}

    {{ filament()->renderHook('head.end') }}
</head>

<body class="filament-body flex flex-col min-h-screen">
    {{ filament()->renderHook('body.start') }}

    {{-- ✅ Tidak perlu <div> tambahan karena <body> sudah flex-col & min-h-screen --}}
    <main class="flex-1">
        {{ $slot }}
    </main>

    @include('footer') {{-- pastikan file ini ada di resources/views/footer.blade.php --}}

    @livewireScripts
    @filamentScripts

    {{ filament()->renderHook('scripts.end') }}
    {{ filament()->renderHook('body.end') }}
</body>
</html>