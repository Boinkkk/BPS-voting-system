@php
    $startOfMonth = \Carbon\Carbon::create($selectedYear, $selectedMonth, 1)->startOfDay();
    $daysInMonth = $startOfMonth->daysInMonth;
    $startDayOfWeek = $startOfMonth->dayOfWeekIso; // 1 (Mon) to 7 (Sun)
    
    // Indonesian Month Name
    $monthsId = [1=>'Januari', 2=>'Februari', 3=>'Maret', 4=>'April', 5=>'Mei', 6=>'Juni', 7=>'Juli', 8=>'Agustus', 9=>'September', 10=>'Oktober', 11=>'November', 12=>'Desember'];
    $monthName = $monthsId[$startOfMonth->month] . ' ' . $startOfMonth->year;
    
    $today = \Carbon\Carbon::now()->startOfDay();

    // Setup Prev/Next Month Links
    $prevMonth = $selectedMonth - 1;
    $prevYear = $selectedYear;
    if($prevMonth < 1) { $prevMonth = 12; $prevYear--; }

    $nextMonth = $selectedMonth + 1;
    $nextYear = $selectedYear;
    if($nextMonth > 12) { $nextMonth = 1; $nextYear++; }

    $currentUrl = url()->current();
@endphp

<div class="mt-4 mb-4">
    <!-- Legend -->
    <div class="flex flex-wrap gap-4 mb-4 justify-center md:justify-start">
        <div class="flex items-center"><span class="w-3 h-3 rounded bg-blue-100 border border-blue-400 mr-2"></span><span class="text-xs text-gray-600 font-semibold uppercase tracking-wider">Persiapan</span></div>
        <div class="flex items-center"><span class="w-3 h-3 rounded bg-orange-100 border border-orange-400 mr-2"></span><span class="text-xs text-gray-600 font-semibold uppercase tracking-wider">Voting</span></div>
        <div class="flex items-center"><span class="w-3 h-3 rounded bg-purple-100 border border-purple-400 mr-2"></span><span class="text-xs text-gray-600 font-semibold uppercase tracking-wider">Pemilihan Kepala</span></div>
        <div class="flex items-center"><span class="w-3 h-3 rounded bg-green-100 border border-green-400 mr-2"></span><span class="text-xs text-gray-600 font-semibold uppercase tracking-wider">Pengumuman</span></div>
    </div>

    <div class="border border-gray-200 rounded-xl overflow-hidden shadow-sm">
        <div class="bg-bps-bg py-3 px-4 border-b border-gray-200 flex justify-between items-center">
            <a href="{{ $currentUrl }}?month={{ $prevMonth }}&year={{ $prevYear }}" class="p-2 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-100 transition-colors">
                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </a>
            <span class="font-black text-gray-700 tracking-widest uppercase">
                {{ $monthName }}
            </span>
            <a href="{{ $currentUrl }}?month={{ $nextMonth }}&year={{ $nextYear }}" class="p-2 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-100 transition-colors">
                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </a>
        </div>
        <div class="grid grid-cols-7 bg-gray-100 border-b border-gray-200 text-xs font-bold text-gray-500 text-center">
            <div class="py-2">Sen</div>
            <div class="py-2">Sel</div>
            <div class="py-2">Rab</div>
            <div class="py-2">Kam</div>
            <div class="py-2">Jum</div>
            <div class="py-2">Sab</div>
            <div class="py-2">Min</div>
        </div>
        <div class="grid grid-cols-7 gap-px bg-gray-200 text-sm">
            @for($i = 1; $i < $startDayOfWeek; $i++)
                <div class="bg-white min-h-[5rem] sm:min-h-[6rem]"></div>
            @endfor
            
            @for($day = 1; $day <= $daysInMonth; $day++)
                @php
                    $currentDate = $startOfMonth->copy()->addDays($day - 1);
                    $bgColor = 'bg-white';
                    $textColor = 'text-gray-700';
                    $borderColor = '';
                    $label = '';
                    $periodeName = '';
                    
                    // Cek apakah tanggal ini berada di masa periode mana pun di bulan ini
                    foreach($periodesInMonth as $periode) {
                        $pMulai = \Carbon\Carbon::parse($periode->tanggal_mulai)->startOfDay();
                        $pSelesaiPersiapan = $periode->tanggal_selesai_persiapan ? \Carbon\Carbon::parse($periode->tanggal_selesai_persiapan)->startOfDay() : $pMulai->copy()->addDays(4);
                        $pMulaiVoting = $periode->tanggal_mulai_voting ? \Carbon\Carbon::parse($periode->tanggal_mulai_voting)->startOfDay() : $pSelesaiPersiapan->copy()->addDay();
                        $pSelesaiVoting = $periode->tanggal_selesai_voting ? \Carbon\Carbon::parse($periode->tanggal_selesai_voting)->startOfDay() : $pMulaiVoting->copy()->addDays(2);
                        $pReview = $periode->tanggal_review_kepala ? \Carbon\Carbon::parse($periode->tanggal_review_kepala)->startOfDay() : $pSelesaiVoting->copy()->addDay();
                        $pSelesai = \Carbon\Carbon::parse($periode->tanggal_selesai)->startOfDay();

                        if ($currentDate->between($pMulai, $pSelesaiPersiapan)) {
                            $bgColor = 'bg-blue-50';
                            $borderColor = 'border-l-4 border-blue-400';
                            $textColor = 'text-blue-800 font-bold';
                            if($currentDate->equalTo($pMulai) || $day == 1) { $label = 'Masa Persiapan'; $periodeName = $periode->nama; }
                            break; // Stop loop if matched
                        } elseif ($currentDate->between($pMulaiVoting, $pSelesaiVoting)) {
                            $bgColor = 'bg-orange-50';
                            $borderColor = 'border-l-4 border-orange-400';
                            $textColor = 'text-orange-800 font-bold';
                            if($currentDate->equalTo($pMulaiVoting) || $day == 1) { $label = 'Masa Voting'; $periodeName = $periode->nama; }
                            break;
                        } elseif ($currentDate->equalTo($pReview)) {
                            $bgColor = 'bg-purple-50';
                            $borderColor = 'border-l-4 border-purple-400';
                            $textColor = 'text-purple-800 font-bold';
                            $label = 'Pemilihan Kepala';
                            $periodeName = $periode->nama;
                            break;
                        } elseif ($currentDate->equalTo($pSelesai)) {
                            $bgColor = 'bg-green-50';
                            $borderColor = 'border-l-4 border-green-400';
                            $textColor = 'text-green-800 font-bold';
                            $label = 'Pengumuman Pemenang';
                            $periodeName = $periode->nama;
                            break;
                        }
                    }
                    
                    $isToday = $currentDate->equalTo($today);
                @endphp
                <div class="{{ $bgColor }} min-h-[5rem] sm:min-h-[6rem] p-1 sm:p-2 transition-colors relative {{ $borderColor }}">
                    <div class="flex justify-between items-start">
                        <span class="inline-flex items-center justify-center w-6 h-6 sm:w-7 sm:h-7 rounded-full {{ $isToday ? 'bg-sky-600 text-white font-bold shadow-md' : $textColor }} text-xs sm:text-sm">{{ $day }}</span>
                    </div>
                    @if($label)
                        <div class="mt-1 sm:mt-2 text-[0.6rem] sm:text-xs leading-tight font-semibold {{ $textColor }} px-0.5 break-words" style="word-break: break-word;">
                            {{ $label }}
                        </div>
                        <div class="text-[0.55rem] sm:text-[0.65rem] {{ $textColor }} opacity-75 px-0.5 mt-0.5 font-medium leading-tight">
                            {{ $periodeName }}
                        </div>
                    @endif
                </div>
            @endfor
            
            @php
                $remainingCells = (7 - (($startDayOfWeek - 1 + $daysInMonth) % 7)) % 7;
            @endphp
            @for($i = 0; $i < $remainingCells; $i++)
                <div class="bg-white min-h-[5rem] sm:min-h-[6rem]"></div>
            @endfor
        </div>
    </div>
</div>
