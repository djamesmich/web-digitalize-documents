<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Pengumuman</h2>
            <p class="text-sm text-gray-500">Informasi rapat, kegiatan, dan berita internal gereja.</p>
        </div>
    </x-slot>

    @php
        $kedLabel = [
            'dpp_inti' => 'DPP Harian',
            'bgkp' => 'BGKP',
            'lingkungan' => 'Lingkungan',
            'sekretariat' => 'Sekretariat',
        ];
    @endphp

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            <div class="bg-white shadow sm:rounded-lg p-4">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    <div class="text-sm text-gray-600">
                        Total tampil: <span class="font-semibold text-gray-900" id="annTotal">{{ $items->count() }}</span>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-2 sm:items-center">
                        <div class="relative">
                            <input
                                id="annSearch"
                                type="text"
                                value="{{ $q }}"
                                placeholder="Cari judul / isi / pembuat / target..."
                                class="w-full sm:w-80 pl-10 pr-3 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-200 focus:border-blue-400"
                            >
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 21l-4.3-4.3"/>
                                    <circle cx="11" cy="11" r="7"/>
                                </svg>
                            </span>
                        </div>

                        @can('create', \App\Models\Announcement::class)
                            <a href="{{ route('announcements.create') }}"
                               class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700 text-sm font-medium">
                                <span class="text-lg leading-none">＋</span>
                                Buat Pengumuman
                            </a>
                        @endcan
                    </div>
                </div>
            </div>

            <div class="space-y-3">
                @if($items->isEmpty())
                    <div class="bg-white shadow sm:rounded-lg p-6 text-center">
                        <div class="text-gray-900 font-semibold">Belum ada pengumuman</div>
                        <div class="text-sm text-gray-600 mt-1">Jika ada rapat/kegiatan, pengumuman akan muncul di sini.</div>
                    </div>
                @else
                    @foreach($items as $a)
                        @php
                            $hay = strtolower(trim(
                                ($a->title ?? '').' '.
                                ($a->body ?? '').' '.
                                (($a->creator?->name) ?? '')
                            ));

                            $statusLabel = $a->status === 'published' ? 'Published' : 'Draft';
                            $statusCls   = $a->status === 'published'
                                ? 'bg-green-50 text-green-700'
                                : 'bg-gray-100 text-gray-700';

                            $dateText = optional($a->created_at)->format('d-m-Y | H:i');
                        @endphp

                        <div
                            class="ann-row bg-white shadow sm:rounded-lg p-5 border border-transparent hover:border-blue-200 hover:ring-2 hover:ring-blue-100 transition cursor-pointer"
                            data-search="{{ $hay }}"
                            data-href="{{ route('announcements.show', $a) }}"
                            role="button"
                            tabindex="0"
                            aria-label="Buka detail pengumuman: {{ $a->title }}"
                        >
                            <div class="flex items-center justify-between gap-4">
                                {{-- LEFT: Title + meta --}}
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <div class="text-lg font-semibold text-gray-900 truncate">
                                            {{ $a->title }}
                                        </div>

                                        @if($a->is_pinned)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-yellow-50 text-yellow-800">
                                                Dipin
                                            </span>
                                        @endif

                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs {{ $statusCls }}">
                                            {{ $statusLabel }}
                                        </span>
                                    </div>

                                    <div class="mt-2 text-sm text-gray-600">
                                        <span class="text-gray-500">Tanggal:</span>
                                        <span class="font-medium text-gray-800">{{ $dateText }}</span>
                                        <span class="text-gray-400">•</span>
                                        <span class="text-gray-500">Oleh:</span>
                                        <span class="font-medium text-gray-800">{{ $a->creator?->name ?? '-' }}</span>
                                    </div>
                                </div>

                                {{-- RIGHT: Button Detail (jelas untuk lansia) --}}
                                <div class="shrink-0">
                                    <a
                                        href="{{ route('announcements.show', $a) }}"
                                        class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-blue-600 text-white hover:bg-blue-700 text-base font-bold shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-300"
                                        data-no-card-nav="1"
                                        aria-label="Lihat detail pengumuman"
                                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M9 18l6-6-6-6"/>
                                        </svg>
                                        Detail
                                    </a>
                                </div>
                            </div>

                            {{-- helper kecil biar user sadar card bisa diklik juga --}}
                            <div class="mt-3 text-xs text-gray-400">
                                Klik kartu atau tombol <b>Detail</b> untuk membaca lengkap.
                            </div>
                        </div>
                    @endforeach

                    <div id="annNoResult" class="hidden bg-white shadow sm:rounded-lg p-6 text-center">
                        <div class="text-gray-900 font-semibold">Pengumuman tidak ditemukan</div>
                        <div class="text-sm text-gray-600 mt-1">Coba kata kunci lain.</div>
                    </div>
                @endif
            </div>

        </div>
    </div>

    @once
        <script>
            const annSearch = document.getElementById('annSearch');
            const annRows = document.querySelectorAll('.ann-row');
            const annNoResult = document.getElementById('annNoResult');
            const annTotal = document.getElementById('annTotal');

            function runAnnFilter() {
                const q = (annSearch?.value || '').toLowerCase().trim();
                let visible = 0;

                annRows.forEach(row => {
                    const hay = (row.getAttribute('data-search') || '').toLowerCase();
                    const show = hay.includes(q);
                    row.classList.toggle('hidden', !show);
                    if (show) visible++;
                });

                if (annNoResult) annNoResult.classList.toggle('hidden', visible !== 0);
                if (annTotal) annTotal.textContent = visible;
            }

            if (annSearch) {
                annSearch.addEventListener('input', runAnnFilter);
                if ((annSearch.value || '').trim() !== '') runAnnFilter();
            }

            annRows.forEach(card => {
                const href = card.getAttribute('data-href');
                if (!href) return;

                card.addEventListener('click', (e) => {
                    const noNav = e.target.closest('[data-no-card-nav="1"], a, button, input, label, select, textarea');
                    if (noNav) return;
                    window.location.href = href;
                });

                card.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        window.location.href = href;
                    }
                });
            });
        </script>
    @endonce
</x-app-layout>
