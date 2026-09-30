<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Notulensi Rapat</h2>
            <p class="text-sm text-gray-500">
                Lihat notulensi rapat internal. Gunakan pencarian untuk menemukan notulensi dengan cepat.
            </p>
        </div>
    </x-slot>

    @php
        $kedLabel = [
            'dpp_inti' => 'DPP Harian',
            'bgkp' => 'BGKP',
            'lingkungan' => 'Lingkungan',
            'sekretariat' => 'Sekretariat',
        ];

        $niceKed = fn($k) => $kedLabel[$k] ?? \Illuminate\Support\Str::headline(str_replace('_',' ',$k));
        $niceRole = fn($r) => \Illuminate\Support\Str::headline(str_replace('_',' ',$r));
    @endphp

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            {{-- TOP BAR --}}
            <div class="bg-white shadow sm:rounded-lg p-4">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    <div class="text-sm text-gray-600">
                        Total tampil:
                        <span class="font-semibold text-gray-900" id="minuteTotal">{{ $minutes->count() }}</span>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-2 sm:items-center">
                        <div class="relative">
                            <input
                                id="minuteSearch"
                                type="text"
                                value="{{ $q ?? '' }}"
                                placeholder="Cari judul / lokasi / pembuat / target..."
                                class="w-full sm:w-80 pl-10 pr-3 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-200 focus:border-blue-400"
                            >
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 21l-4.3-4.3"/>
                                    <circle cx="11" cy="11" r="7"/>
                                </svg>
                            </span>
                        </div>

                        @can('create', \App\Models\MeetingMinute::class)
                            <a href="{{ route('minutes.create') }}"
                               class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700 text-sm font-medium">
                                <span class="text-lg leading-none">＋</span>
                                Buat Notulensi
                            </a>
                        @endcan
                    </div>
                </div>
            </div>

            {{-- LIST MINIMAL --}}
            <div class="space-y-3">
                @if($minutes->isEmpty())
                    <div class="bg-white shadow sm:rounded-lg p-6 text-center">
                        <div class="text-gray-900 font-semibold">Belum ada notulensi</div>
                        <div class="text-sm text-gray-600 mt-1">Notulensi rapat akan muncul di sini.</div>
                    </div>
                @else
                    @foreach($minutes as $m)
                        @php
                            $tk = $m->target_kedudukan ?? [];
                            $tr = $m->target_roles ?? [];

                            $tkNice = collect($tk)->map(fn($k) => $niceKed($k))->values();
                            $trNice = collect($tr)->map(fn($r) => $niceRole($r))->values();

                            $targetText = ($tkNice->isEmpty() && $trNice->isEmpty())
                                ? 'semua'
                                : ('kedudukan '.$tkNice->implode(' ').' roles '.$trNice->implode(' '));

                            $hay = strtolower(trim(
                                ($m->title ?? '').' '.
                                ($m->location ?? '').' '.
                                (($m->creator?->name) ?? '').' '.
                                ($m->status ?? '').' '.
                                ($targetText)
                            ));
                        @endphp

                        <div class="bg-white shadow sm:rounded-lg p-5 minute-row" data-search="{{ $hay }}">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

                                {{-- LEFT --}}
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <div class="text-lg font-semibold text-gray-900 truncate">
                                            {{ $m->title }}
                                        </div>

                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs
                                            {{ $m->status === 'published' ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                            {{ $m->status === 'published' ? 'Published' : 'Draft' }}
                                        </span>
                                    </div>

                                    <div class="text-xs text-gray-500 mt-2">
                                        {{ optional($m->meeting_at)->format('d-m-Y | H:i') }}
                                        @if($m->location) • {{ $m->location }} @endif
                                        • Oleh: {{ $m->creator?->name ?? '-' }}
                                    </div>
                                </div>

                                {{-- RIGHT BUTTONS --}}
                                <div class="flex items-center gap-2 flex-wrap justify-end shrink-0">
                                    <a href="{{ route('minutes.show', $m) }}"
                                       class="px-4 py-2 rounded-lg bg-gray-900 text-white hover:bg-black text-sm font-semibold">
                                        Detail
                                    </a>

                                    @can('update', $m)
                                        <a href="{{ route('minutes.edit', $m) }}"
                                           class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 text-sm font-semibold">
                                            Edit
                                        </a>
                                    @endcan

                                    @can('delete', $m)
                                        <form id="delete-minute-{{ $m->id }}" method="POST"
                                              action="{{ route('minutes.destroy', $m) }}" class="hidden">
                                            @csrf
                                            @method('DELETE')
                                        </form>

                                        <button type="button"
                                                onclick="confirmDeleteMinute({{ $m->id }})"
                                                class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 text-sm font-semibold">
                                            Hapus
                                        </button>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <div id="minuteNoResult" class="hidden bg-white shadow sm:rounded-lg p-6 text-center">
                        <div class="text-gray-900 font-semibold">Notulensi tidak ditemukan</div>
                        <div class="text-sm text-gray-600 mt-1">Coba kata kunci lain.</div>
                    </div>
                @endif
            </div>

        </div>
    </div>

    @once
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            const minuteSearch = document.getElementById('minuteSearch');
            const minuteRows = document.querySelectorAll('.minute-row');
            const minuteNoResult = document.getElementById('minuteNoResult');
            const minuteTotal = document.getElementById('minuteTotal');

            function runMinuteFilter() {
                const q = (minuteSearch?.value || '').toLowerCase().trim();
                let visible = 0;

                minuteRows.forEach(row => {
                    const hay = (row.getAttribute('data-search') || '').toLowerCase();
                    const show = hay.includes(q);
                    row.classList.toggle('hidden', !show);
                    if (show) visible++;
                });

                if (minuteNoResult) minuteNoResult.classList.toggle('hidden', visible !== 0);
                if (minuteTotal) minuteTotal.textContent = visible;
            }

            if (minuteSearch) {
                minuteSearch.addEventListener('input', runMinuteFilter);
                if ((minuteSearch.value || '').trim() !== '') runMinuteFilter();
            }

            function confirmDeleteMinute(id) {
                Swal.fire({
                    title: 'Hapus notulensi?',
                    text: "Notulensi yang dihapus tidak bisa dikembalikan.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('delete-minute-' + id)?.submit();
                    }
                });
            }
            window.confirmDeleteMinute = confirmDeleteMinute;
        </script>
    @endonce
</x-app-layout>
