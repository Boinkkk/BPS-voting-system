<div>
    <!-- Search Bar -->
    <div class="mb-6 relative">
        <div class="relative max-w-md">
            <input 
                type="text" 
                wire:model.live.debounce.300ms="search"
                placeholder="Cari pertanyaan atau bantuan..." 
                class="w-full py-2.5 pl-10 pr-10 bg-white border border-gray-300 rounded-lg text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-bps-secondary/50 focus:border-bps-secondary transition-all shadow-sm"
                style="padding-left: 2.5rem; padding-right: 2.5rem;"
            >
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            @if(!empty($search))
                <button 
                    wire:click="$set('search', '')" 
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 transition-colors"
                    title="Clear search"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            @endif
        </div>
        
        <div wire:loading wire:target="search" class="mt-2 text-xs text-blue-600 flex items-center font-medium">
            <svg class="animate-spin -ml-1 mr-2 h-3.5 w-3.5 text-blue-600" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Mencari...
        </div>
    </div>

    <!-- FAQ List -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm w-full">
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($faqs as $item)
                    <div 
                        onclick="openFaqModal('{{ htmlspecialchars($item->pertanyaan, ENT_QUOTES, 'UTF-8') }}', `{{ htmlspecialchars(nl2br(e($item->jawaban)), ENT_QUOTES, 'UTF-8') }}`)" 
                        class="bg-bps-bg p-5 rounded-lg border border-gray-100 hover:border-bps-secondary/50 hover:shadow-md cursor-pointer transition-all duration-200 flex flex-col h-full group"
                    >
                        <h3 class="text-lg font-semibold text-bps-secondary group-hover:text-blue-700 transition-colors mb-2">{{ $item->pertanyaan }}</h3>
                        <div class="text-sm text-gray-600 leading-relaxed line-clamp-3">
                            {{ \Illuminate\Support\Str::limit($item->jawaban, 150) }}
                        </div>
                        @if(strlen($item->jawaban) > 150)
                            <div class="mt-auto pt-4 text-xs font-medium text-blue-600 flex items-center">
                                Baca selengkapnya
                                <svg class="w-3 h-3 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </div>
                        @else
                            <div class="mt-auto pt-4"></div>
                        @endif
                    </div>
                @empty
                    <div class="col-span-full py-12 text-center text-gray-500">
                        <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-gray-600 font-medium">Tidak ada FAQ yang cocok.</p>
                        @if(!empty($search))
                            <p class="text-xs text-gray-400 mt-1">Coba gunakan kata kunci lain.</p>
                        @endif
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
