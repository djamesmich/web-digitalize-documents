<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Manajemen Folder</h2>
            <p class="text-sm text-gray-500">
                Buat folder untuk menyimpan dokumen/template. Folder hanya terlihat oleh pembuatnya (kecuali Super Admin).
            </p>
        </div>
    </x-slot>

    @php
        $trashIcon = function () {
            return <<<SVG
<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
  <path d="M3 6h18"/>
  <path d="M8 6V4h8v2"/>
  <path d="M6 6l1 16h10l1-16"/>
  <path d="M10 11v6"/>
  <path d="M14 11v6"/>
</svg>
SVG;
        };

        $folderIcon = function () {
            return <<<SVG
<svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
  <path d="M3 7a2 2 0 0 1 2-2h5l2 2h7a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
</svg>
SVG;
        };
    @endphp

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-4">

            {{-- TOP BAR --}}
            <div class="bg-white shadow sm:rounded-lg p-5">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="text-sm text-gray-600">
                            Total: <span class="font-semibold text-gray-900">{{ $folders->count() }}</span> folder
                        </div>
                        <span class="hidden md:inline text-gray-300">|</span>
                        <div class="text-xs text-gray-500">
                            Tips: gunakan pencarian untuk cepat menemukan folder.
                        </div>
                    </div>

                    <div class="relative w-full md:w-[28rem]">
                        <input
                            id="folderSearch"
                            type="text"
                            placeholder="Cari folder..."
                            class="w-full pl-10 pr-3 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-200 focus:border-blue-400"
                        >
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 21l-4.3-4.3"/>
                                <circle cx="11" cy="11" r="7"/>
                            </svg>
                        </span>
                    </div>
                </div>
            </div>

            {{-- BUAT FOLDER --}}
            <div id="buat-folder" class="bg-white shadow sm:rounded-lg p-6">
                <div class="flex items-start justify-between gap-4 flex-wrap">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Buat Folder</h3>
                        <p class="text-sm text-gray-500 mt-1">
                            Folder ini hanya terlihat oleh sekretaris yang membuatnya (kecuali Super Admin).
                        </p>
                    </div>
                </div>

                <form method="POST" action="{{ route('folders.store') }}"
                      class="mt-5 flex flex-col sm:flex-row gap-3 sm:items-end"
                      novalidate>
                    @csrf

                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700">Nama Folder</label>
                        <input
                            name="name"
                            value="{{ old('name') }}"
                            class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-200 focus:border-blue-400"
                            placeholder="Contoh: Dokumen Bidang Formatio">
                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                    </div>

                    <button
                        type="submit"
                        class="inline-flex items-center justify-center px-5 py-2.5 rounded-lg bg-green-600 text-white hover:bg-green-700 text-sm font-semibold"
                    >
                        Simpan
                    </button>
                </form>
            </div>

            {{-- DAFTAR FOLDER --}}
            <div class="bg-white shadow sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Daftar Folder</h3>
                            <div class="text-xs text-gray-500 mt-1">Klik folder untuk melihat isi.</div>
                        </div>
                    </div>

                    @if($folders->isEmpty())
                        <div class="text-center py-12">
                            <div class="text-gray-900 font-semibold">Belum ada folder</div>
                            <div class="text-sm text-gray-600 mt-1">Buat folder terlebih dahulu untuk menyimpan dokumen.</div>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3" id="folderList">
                            @foreach($folders as $f)
                                <div class="folder-row border rounded-xl p-4 hover:bg-gray-50 transition"
                                     data-search="{{ strtolower($f->name) }}">
                                    <div class="flex items-center justify-between gap-3">
                                        <a href="{{ route('folders.show', $f) }}"
                                           class="flex items-center gap-3 min-w-0">
                                            <span class="text-blue-700 shrink-0">{!! $folderIcon() !!}</span>
                                            <div class="min-w-0">
                                                <div class="font-semibold text-gray-900 truncate">{{ $f->name }}</div>
                                                <div class="text-xs text-gray-500">Buka folder</div>
                                            </div>
                                        </a>

                                        {{-- ✅ ACTIONS (Buka + Trash nempel & sejajar) --}}
                                        <div class="flex items-center gap-2 shrink-0">
                                            <a href="{{ route('folders.show', $f) }}"
                                               class="px-3 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 text-sm font-medium">
                                                Buka
                                            </a>

                                            <form method="POST"
                                                  action="{{ route('folders.destroy', $f) }}"
                                                  class="delete-folder-form"
                                                  data-folder-name="{{ $f->name }}">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                        class="inline-flex items-center justify-center w-10 h-10 rounded-lg border border-red-200 text-red-700 hover:bg-red-50"
                                                        title="Hapus permanen"
                                                        aria-label="Hapus folder permanen">
                                                    {!! $trashIcon() !!}
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div id="noFolderResult" class="hidden text-center py-12">
                            <div class="text-gray-900 font-semibold">Folder tidak ditemukan</div>
                            <div class="text-sm text-gray-600 mt-1">Coba kata kunci lain.</div>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

    @once
        <script>
            // Search folder (client-side)
            const folderSearch = document.getElementById('folderSearch');
            const folderRows = document.querySelectorAll('.folder-row');
            const noFolderResult = document.getElementById('noFolderResult');

            if (folderSearch) {
                folderSearch.addEventListener('input', () => {
                    const q = (folderSearch.value || '').toLowerCase().trim();
                    let visible = 0;

                    folderRows.forEach(row => {
                        const hay = row.getAttribute('data-search') || '';
                        const show = hay.includes(q);
                        row.classList.toggle('hidden', !show);
                        if (show) visible++;
                    });

                    if (noFolderResult) noFolderResult.classList.toggle('hidden', visible !== 0);
                });
            }

            // SweetAlert2 Confirm Delete Folder
            document.querySelectorAll('.delete-folder-form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const folderName = form.getAttribute('data-folder-name') || 'folder ini';

                    Swal.fire({
                        title: 'Hapus folder permanen?',
                        text: `Folder "${folderName}" akan dihapus. Item folder akan hilang dari folder ini.`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#e3342f',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, hapus',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) form.submit();
                    });
                });
            });
        </script>
    @endonce
</x-app-layout>
