<div class="flex-1 p-4 max-lg:hidden">
    <div class="text-white relative rounded-lg h-full w-full bg-zinc-900 flex flex-col items-start justify-end p-16" style="background: linear-gradient(135deg, #18181b 0%, #059669 100%);">
        <div class="flex gap-2 mb-4">
            <flux:icon.star variant="solid" class="text-yellow-400" />
            <flux:icon.star variant="solid" class="text-yellow-400" />
            <flux:icon.star variant="solid" class="text-yellow-400" />
            <flux:icon.star variant="solid" class="text-yellow-400" />
            <flux:icon.star variant="solid" class="text-yellow-400" />
        </div>

        <div class="mb-6 italic font-base text-3xl xl:text-4xl">
            {{ $quote ?? 'NodeHarvest transformed my node management completely. Automated earnings tracking and auto-creation of new nodes saved me countless hours every week.' }}
        </div>

        <div class="flex gap-4">
            @if(isset($avatar))
                {{ $avatar }}
            @else
                <flux:avatar src="https://avatars.githubusercontent.com/novislab" size="xl" />
            @endif

            <div class="flex flex-col justify-center font-medium">
                <div class="text-lg">{{ $name ?? 'NovisLab' }}</div>
                <div class="text-zinc-300">{{ $title ?? 'Creator of NodeHarvest' }}</div>
            </div>
        </div>
    </div>
</div>