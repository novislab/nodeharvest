<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

        <title>{{ $title ?? config('app.name') }}</title>

        @include('partials.favicon')
        @include('partials.metadata')

        <script>window.instrucktEnabled = @json(config('instruckt.enabled'));</script>

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @fluxAppearance
    </head>
