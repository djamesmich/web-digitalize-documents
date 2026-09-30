<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Detail Notulensi</h2>
            <p class="text-sm text-gray-500">Informasi rapat dan catatan notulensi.</p>
        </div>
    </x-slot>

    @php
        // Label kedudukan yang rapi
        $kedLabel = [
            'dpp_inti'      => 'DPP Harian',
            'bgkp'          => 'BGKP',
            'lingkungan'    => 'Lingkungan',
            'sekretariat'   => 'Sekretariat',
        ];

        // Data target
        $tk = $minute->target_kedudukan ?? [];
        $tr = $minute->target_roles ?? [];

        // Convert ke label manusiawi
        $tkNice = collect($tk)
            ->map(fn($k) => $kedLabel[$k] ?? $k)
            ->filter()
            ->values();

        $trNice = collect($tr)
            ->map(fn($r) => \Illuminate\Support\Str::headline(str_replace('_',' ', $r)))
            ->filter()
            ->values();
    @endphp

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-4">

            {{-- TOP ACTIONS --}}
            <div class="flex items-center justify-between gap-3 flex-wrap">
                <a href="{{ route('minutes.index') }}"
                   class="inline-flex items-center px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 font-medium">
                    ← Kembali
                </a>

                <div class="flex items-center gap-2 flex-wrap">
                    @can('update', $minute)
                        <a href="{{ route('minutes.edit', $minute) }}"
                           class="px-5 py-3 rounded-lg bg-blue-600 text-white hover:bg-blue-700 font-semibold">
                            Edit
                        </a>
                    @endcan

                    @can('delete', $minute)
                        <form id="delete-minute-{{ $minute->id }}"
                              method="POST"
                              action="{{ route('minutes.destroy', $minute) }}"
                              class="hidden">
                            @csrf
                            @method('DELETE')
                        </form>

                        <button type="button"
                                onclick="confirmDeleteMinute({{ $minute->id }})"
                                class="px-5 py-3 rounded-lg bg-red-600 text-white hover:bg-red-700 font-semibold">
                            Hapus
                        </button>
                    @endcan
                </div>
            </div>

            {{-- HEADER CARD --}}
            <div class="bg-white shadow sm:rounded-lg p-6">
                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                    <div class="min-w-0">
                        <div class="text-sm text-gray-500">Judul</div>
                        <div class="text-2xl font-semibold text-gray-900 break-words">
                            {{ $minute->title }}
                        </div>

                        <div class="text-sm text-gray-600 mt-2">
                            Oleh:
                            <span class="font-semibold">{{ $minute->creator?->name ?? '-' }}</span>
                            • {{ optional($minute->created_at)->format('d-m-Y | H:i') }}
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold
                            {{ $minute->status === 'published' ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                            {{ $minute->status === 'published' ? 'Published' : 'Draft' }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- INFO + TARGET --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

                {{-- Info Rapat --}}
                <div class="lg:col-span-2 bg-white shadow sm:rounded-lg p-6">
                    <div class="font-semibold text-gray-900 mb-4">Informasi Rapat</div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div>
                            <div class="text-gray-500">Tanggal & Jam Rapat</div>
                            <div class="text-gray-900 font-semibold">
                                {{ optional($minute->meeting_at)->format('d-m-Y | H:i') ?? '-' }}
                            </div>
                        </div>

                        <div>
                            <div class="text-gray-500">Lokasi</div>
                            <div class="text-gray-900 font-semibold">
                                {{ $minute->location ?: '-' }}
                            </div>
                        </div>

                        <div class="sm:col-span-2">
                            <div class="text-gray-500">Agenda</div>
                            <div class="text-gray-900 whitespace-pre-line mt-1">
                                {{ $minute->agenda ?: '-' }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Share / Target --}}
                <div class="bg-white shadow sm:rounded-lg p-6">
                    <div class="font-semibold text-gray-900 mb-3">Share / Target</div>

                    @if($tkNice->isEmpty() && $trNice->isEmpty())
                        <div class="text-sm text-gray-700">
                            Ditujukan untuk: <span class="font-semibold">Semua</span>
                        </div>
                    @else
                        @if($tkNice->isNotEmpty())
                            <div class="text-sm text-gray-500">Kedudukan</div>
                            <div class="mt-2 flex flex-wrap gap-2">
                                @foreach($tkNice as $x)
                                    <span class="px-3 py-1 rounded-full text-xs bg-blue-50 text-blue-700 font-semibold">
                                        {{ $x }}
                                    </span>
                                @endforeach
                            </div>
                        @endif

                        @if($trNice->isNotEmpty())
                            <div class="text-sm text-gray-500 mt-4">Role</div>
                            <div class="mt-2 flex flex-wrap gap-2">
                                @foreach($trNice as $x)
                                    <span class="px-3 py-1 rounded-full text-xs bg-gray-100 text-gray-800 font-semibold">
                                        {{ $x }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    @endif
                </div>
            </div>

            {{-- ISI NOTULENSI --}}
            <div class="bg-white shadow sm:rounded-lg p-6">
                <div class="font-semibold text-gray-900 mb-3">Isi Notulensi</div>

                <div class="rounded-lg border bg-gray-50 p-4">
                    <div class="text-gray-900 whitespace-pre-line leading-relaxed">
                        {{ $minute->content }}
                    </div>
                </div>
            </div>

        </div>
    </div>

    @once
        {{-- Pastikan SweetAlert ada --}}
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            function confirmDeleteMinute(id) {
                Swal.fire({
                    title: 'Hapus notulensi?',
                    text: "Notulensi yang dihapus tidak bisa dikembalikan.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e3342f',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.getElementById('delete-minute-' + id);
                        if (form) form.submit();
                    }
                });
            }
            window.confirmDeleteMinute = confirmDeleteMinute;
        </script>
    @endonce
</x-app-layout>
