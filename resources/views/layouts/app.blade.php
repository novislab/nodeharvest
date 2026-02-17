@include('partials.head')

<body class="min-h-screen bg-white dark:bg-zinc-950 antialiased">
    @include('partials.sidebar')
    @include('partials.header')

    <flux:main>
        {{ $slot }}
    </flux:main>

    @include('partials.toast')

    @fluxScripts
</body>
</html>
