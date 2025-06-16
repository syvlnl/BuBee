<x-filament-widgets::widget>
    <x-filament::card>
        {{-- Periksa apakah data target ada --}}
        @if ($target)
            <div class="space-y-2">
                <div class="flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                        {{ $target->name ?? 'Progress Target Anda' }}
                    </h2>
                    <a href="{{ $this->getTargets() }}"
                    class="text-sm font-medium text-primary-600 hover:underline dark:text-primary-500">View more</a>
                </div>

                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                    <span>Rp {{ number_format($target->amount_collected, 0, ',', '.') }}</span>
                    <span class="text-gray-400 dark:text-gray-500">/</span>
                    <span>Rp {{ number_format($target->amount_needed, 0, ',', '.') }}</span>
                </div>

                {{-- Progress Bar Container --}}
                <div class="w-full bg-gray-200 rounded-full h-4 dark:bg-gray-700">
                    {{-- Progress Bar Fill --}}
                    <div class="bg-primary-600 h-4 rounded-full" style="width: {{ $percentage }}%"></div>
                </div>
            </div>
        @else
            {{-- Tampilan jika user tidak memiliki data target --}}
            <div class="text-center">
                <p class="text-gray-500 dark:text-gray-400">Anda belum memiliki target.</p>
                {{-- Opsional: Tambahkan tombol untuk membuat target baru --}}
                {{-- <x-filament::button
                    tag="a"
                    href="/admin/targets/create"
                    class="mt-2"
                >
                    Buat Target Baru
                </x-filament::button> --}}
            </div>
        @endif
    </x-filament::card>
</x-filament-widgets::widget>