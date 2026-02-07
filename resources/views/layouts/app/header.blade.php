<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
        <link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.8/dist/trix.css">
        <script type="text/javascript" src="https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js"></script>
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <livewire:navbar />

        {{ $slot }}

        @fluxScripts
    </body>
</html>
