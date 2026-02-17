@include('partials.head')

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
