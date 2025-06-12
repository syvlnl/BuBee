<x-filament::page>
    <div class="flex items-center justify-center min-h-[60vh]">
        <div class="bg-cream text-center rounded-2xl p-10 shadow-lg w-[350px]">
            <div class="flex justify-center mb-4">
                <svg xmlns="x-heroicon-m-chat-bubble-oval-left-ellipsis" class="h-12 w-12 text-black" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422A12.083 12.083 0 0121 13.477V19l-9 5-9-5v-5.523a12.083 12.083 0 012.84-2.899L12 14z" />
                </svg>
            </div>

            <h2 class="text-2xl font-extrabold text-black mb-2">Contact BuBee</h2>
            <p class="text-gray-600 mb-6">We're happy to help!</p>
            <a href="{{ $whatsappLink }}" target="_blank"
               class="bg-black text-white font-semibold py-2 px-6 rounded-full hover:bg-gray-800 transition duration-300">
                Chat me!
            </a>
        </div>
    </div>
</x-filament::page>
