<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Detail Pengumuman</h2>
            <p class="text-sm text-gray-500">Baca informasi lengkap pengumuman.</p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">

            <div class="flex items-center justify-between gap-3 flex-wrap">
                <a href="{{ route('announcements.index') }}"
                class="inline-flex items-center px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">
                    ← Kembali
                </a>

                <div class="flex gap-2 flex-wrap">
                    @can('update', $announcement)
                        <a href="{{ route('announcements.edit', $announcement) }}"
                        class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 text-sm">
                            Edit
                        </a>
                    @endcan

                    @can('delete', $announcement)
                        <form id="delete-ann-{{ $announcement->id }}"
                            method="POST"
                            action="{{ route('announcements.destroy', $announcement) }}"
                            class="hidden">
                            @csrf
                            @method('DELETE')
                        </form>

                        <button type="button"
                                onclick="confirmDeleteAnn({{ $announcement->id }})"
                                class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 text-sm">
                            Hapus
                        </button>
                    @endcan
                </div>
            </div>

            <div class="bg-white shadow sm:rounded-lg p-6 space-y-5">

                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                    <div>
                        <div class="text-sm text-gray-500">Judul</div>
                        <div class="text-2xl font-semibold text-gray-900">{{ $announcement->title }}</div>
                        <div class="text-xs text-gray-500 mt-2">
                            Oleh: {{ $announcement->creator?->name ?? '-' }} • {{ optional($announcement->created_at)->format('d-m-Y | H:i') }}
                        </div>
                    </div>

                    <div class="flex items-center gap-2 flex-wrap">
                        @if($announcement->is_pinned)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs bg-yellow-50 text-yellow-800">
                                Dipin
                            </span>
                        @endif

                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs
                            {{ $announcement->status === 'published' ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                            {{ $announcement->status === 'published' ? 'Published' : 'Draft' }}
                        </span>
                    </div>
                </div>

                <hr>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <div class="text-gray-500">Mulai tampil</div>
                        <div class="text-gray-900 font-medium">
                            {{ $announcement->starts_at ? $announcement->starts_at->format('d-m-Y | H:i') : 'Sekarang' }}
                        </div>
                    </div>

                    <div>
                        <div class="text-gray-500">Berakhir</div>
                        <div class="text-gray-900 font-medium">
                            {{ $announcement->ends_at ? $announcement->ends_at->format('d-m-Y | H:i') : 'Tidak ada' }}
                        </div>
                    </div>

                    <div class="sm:col-span-2">
                        @php
                            $kedLabel = [
                                'dpp_inti' => 'DPP Harian',
                                'bgkp' => 'BGKP',
                                'lingkungan' => 'Lingkungan',
                                'sekretariat' => 'Sekretariat',
                            ];

                            $tk = $announcement->target_kedudukan ?? [];
                            $tr = $announcement->target_roles ?? [];

                            $tkNice = collect($tk)->map(fn($k) => $kedLabel[$k] ?? \Illuminate\Support\Str::headline(str_replace('_',' ',$k)))->values();
                            $trNice = collect($tr)->map(fn($r) => \Illuminate\Support\Str::headline(str_replace('_',' ',$r)))->values();
                        @endphp
                        <div class="text-gray-500">Target</div>
                        <div class="text-gray-900 font-medium mt-1">
                            @if($tkNice->isEmpty() && $trNice->isEmpty())
                                Semua user
                            @else
                                @if(!$tkNice->isEmpty())
                                    <div>Kedudukan: {{ $tkNice->implode(', ') }}</div>
                                @endif
                                @if(!$trNice->isEmpty())
                                    <div>Roles: {{ $trNice->implode(', ') }}</div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>

                <div>
                    <div class="text-sm text-gray-500">Isi Pengumuman</div>
                    <div class="mt-2 rounded-lg border bg-gray-50 p-4">
                        <div class="text-gray-900 whitespace-pre-line leading-relaxed">
                            {{ $announcement->body }}
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>

    @once
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            function confirmDeleteAnn(id) {
                Swal.fire({
                    title: 'Hapus pengumuman?',
                    text: "Pengumuman yang dihapus tidak bisa dikembalikan.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e3342f',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.getElementById('delete-ann-' + id);
                        if (form) form.submit();
                    }
                });
            }
            window.confirmDeleteAnn = confirmDeleteAnn;
        </script>
    @endonce
</x-app-layout>