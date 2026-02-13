<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

        <title>{{ $title ?? config('app.name') }}</title>

        @include('partials.favicon')
        @include('partials.metadata')

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @fluxAppearance
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-950 antialiased">
        <div class="flex min-h-screen">
            <div class="flex-1 flex justify-center items-center">
                <div class="w-80 max-w-80 space-y-6">
                    {{ $slot }}
                </div>
            </div>

            <x-auth.testimonial />
        </div>

        @include('partials.toast')

        @fluxScripts
    </body>
</html>