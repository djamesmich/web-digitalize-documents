<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Folder: {{ $folder->name }}
            </h2>
            <p class="text-sm text-gray-500">
                Dokumen yang tersimpan di folder ini bisa diatur menjadi private / shared dan dibagikan ke role/bidang.
            </p>
        </div>
    </x-slot>

    @php
        $roleLabel = fn ($r) => \Illuminate\Support\Str::headline(str_replace('_', ' ', $r));

        // Helper buat judul item yang ditampilkan (biar rapi & manusiawi)
        $itemTitle = function (string $sourceLabel, $source): array {
            $title = '(File hilang)';
            $sub   = '';

            if ($sourceLabel === 'Template') {
                $title = $source?->title ?? '(Template hilang)';
                $sub   = $source?->original_name ?? '';
            } elseif ($sourceLabel === 'ProposalFile') {
                $title = $source?->original_name ?? '(Lampiran proposal hilang)';
                $sub   = '';
            } elseif ($sourceLabel === 'Document') {
                $title = $source?->title ?? '(Dokumen hilang)';
                $sub   = $source?->filename ?? '';
            } elseif ($sourceLabel === 'LpjFile') {
                $title = $source?->original_name ?? '(Lampiran LPJ hilang)';
                $sub   = '';
            }

            $filenameForExt = $sub ?: ($source?->original_name ?? ($source?->filename ?? ''));
            $ext = strtolower(pathinfo($filenameForExt, PATHINFO_EXTENSION));
            $extLabel = $ext ? strtoupper($ext) : null;

            return compact('title','sub','extLabel');
        };
    @endphp

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-4">

            {{-- TOP BAR --}}
            <div class="bg-white shadow sm:rounded-lg p-4">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    <div class="flex items-center gap-2">
                        <a href="{{ route('folders.index') }}"
                           class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-sm text-gray-800">
                            ← Kembali
                        </a>
                        <div class="text-sm text-gray-600">
                            Total: <span class="font-semibold text-gray-900">{{ $folder->items->count() }}</span> item
                        </div>
                    </div>

                    <div class="relative">
                        <input
                            id="itemSearch"
                            type="text"
                            placeholder="Cari judul / nama file..."
                            class="w-full sm:w-80 pl-10 pr-3 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-200 focus:border-blue-400"
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

            {{-- LIST --}}
            <div class="bg-white shadow sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Dokumen di Folder</h3>

                    @if($folder->items->isEmpty())
                        <div class="text-center py-10">
                            <div class="text-gray-900 font-semibold">Belum ada dokumen di folder ini</div>
                            <div class="text-sm text-gray-600 mt-1">
                                Tambahkan item dari menu Dokumen / Template / Proposal (tombol “+ Folder”).
                            </div>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-gray-50 text-gray-700 text-xs uppercase tracking-wide">
                                        <th class="px-4 py-3">Sumber</th>
                                        <th class="px-4 py-3">Judul / Nama</th>
                                        <th class="px-4 py-3">Mode</th>
                                        <th class="px-4 py-3 text-center">Aksi</th>
                                    </tr>
                                </thead>

                                <tbody id="itemTableBody" class="divide-y">
                                    @foreach($folder->items as $item)
                                        @php
                                            $sourceLabel = class_basename($item->source_type);
                                            $source = $item->source;

                                            // search string
                                            $titleForSearch = '';
                                            if ($sourceLabel === 'Template') {
                                                $titleForSearch = ($source?->title ?? '').' '.($source?->original_name ?? '');
                                            } elseif ($sourceLabel === 'ProposalFile') {
                                                $titleForSearch = ($source?->original_name ?? '');
                                            } elseif ($sourceLabel === 'Document') {
                                                $titleForSearch = ($source?->title ?? '').' '.($source?->filename ?? '');
                                            } elseif ($sourceLabel === 'LpjFile') {
                                                $lpjName = $source?->lpj?->lpj_name ?? '';
                                                $proposalNo = $source?->lpj?->proposal?->proposal_no ?? '';
                                                $titleForSearch = ($source?->original_name ?? '').' '.$lpjName.' '.$proposalNo;
                                            }

                                            $meta = $itemTitle($sourceLabel, $source);
                                            $search = strtolower($sourceLabel.' '.$titleForSearch.' '.$meta['title'].' '.$meta['sub']);
                                        @endphp

                                        <tr class="hover:bg-gray-50 align-top item-row" data-search="{{ $search }}">
                                            {{-- Sumber --}}
                                            <td class="px-4 py-3">
                                                <x-source-badge :sourceLabel="$sourceLabel" />
                                            </td>

                                            {{-- Judul / Nama --}}
                                            <td class="px-4 py-3">
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    @if($meta['extLabel'])
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-700">
                                                            {{ $meta['extLabel'] }}
                                                        </span>
                                                    @endif

                                                    @if(!$source)
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-red-50 text-red-700">
                                                            Sumber sudah tidak ada
                                                        </span>
                                                    @endif
                                                </div>

                                                <div class="mt-2 font-semibold text-gray-900">
                                                    {{ $meta['title'] }}
                                                </div>

                                                @if(!empty($meta['sub']))
                                                    <div class="text-xs text-gray-500 mt-1">
                                                        {{ $meta['sub'] }}
                                                    </div>
                                                @else
                                                    <div class="text-xs text-gray-500 mt-1">
                                                        Klik <b>Preview</b> untuk melihat isi, atau <b>Download</b> untuk mengunduh.
                                                    </div>
                                                @endif
                                            </td>

                                            {{-- Mode --}}
                                            <td class="px-4 py-3">
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs
                                                    {{ $item->is_private ? 'bg-gray-100 text-gray-700' : 'bg-green-50 text-green-700' }}">
                                                    {{ $item->is_private ? 'Private' : 'Shared' }}
                                                </span>
                                            </td>

                                            {{-- Aksi --}}
                                            <td class="px-4 py-3">
                                                <div class="flex items-center justify-center gap-2 flex-wrap">

                                                    {{-- TEMPLATE --}}
                                                    @if($sourceLabel === 'Template' && $source)
                                                        @if(strtolower($source->file_type) === 'pdf')
                                                            <a href="{{ route('templates.stream', $source) }}" target="_blank"
                                                               class="px-3 py-2 rounded-lg bg-yellow-500 text-white hover:bg-yellow-600 text-sm">
                                                                Preview
                                                            </a>
                                                        @else
                                                            <a href="{{ route('templates.view', $source) }}" target="_blank"
                                                               class="px-3 py-2 rounded-lg bg-yellow-500 text-white hover:bg-yellow-600 text-sm">
                                                                Preview
                                                            </a>
                                                        @endif

                                                        <a href="{{ route('templates.download', $source) }}"
                                                           class="px-3 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 text-sm">
                                                            Download
                                                        </a>
                                                    @endif

                                                    {{-- PROPOSAL FILE --}}
                                                    @if($sourceLabel === 'ProposalFile' && $source)
                                                        @php
                                                            $source->loadMissing('proposal');
                                                            $proposalId = $source->proposal_id;
                                                            $fileId = $source->id;
                                                        @endphp

                                                        @if($proposalId && $fileId)
                                                            <a href="{{ route('proposals.files.preview', ['proposal' => $proposalId, 'file' => $fileId]) }}" target="_blank"
                                                               class="px-3 py-2 rounded-lg bg-yellow-500 text-white hover:bg-yellow-600 text-sm">
                                                                Preview
                                                            </a>

                                                            <a href="{{ route('proposals.files.download', ['proposal' => $proposalId, 'file' => $fileId]) }}"
                                                               class="px-3 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 text-sm">
                                                                Download
                                                            </a>
                                                        @endif
                                                    @endif

                                                    {{-- DOCUMENT --}}
                                                    @if($sourceLabel === 'Document' && $source)
                                                        <a href="{{ route('documents.preview', $source) }}" target="_blank"
                                                           class="px-3 py-2 rounded-lg bg-yellow-500 text-white hover:bg-yellow-600 text-sm">
                                                            Preview
                                                        </a>

                                                        <a href="{{ route('documents.download', $source) }}"
                                                           class="px-3 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 text-sm">
                                                            Download
                                                        </a>
                                                    @endif

                                                    {{-- LPJ FILE --}}
                                                    @if($sourceLabel === 'LpjFile' && $source)
                                                        @php
                                                            $source->loadMissing('lpj.proposal');
                                                            $lpj = $source->lpj;
                                                            $proposalId = $lpj?->proposal_id;
                                                            $lpjId = $lpj?->id;
                                                            $fileId = $source->id;
                                                        @endphp

                                                        @if($proposalId && $lpjId && $fileId)
                                                            <a href="{{ route('lpj.files.preview', ['proposal' => $proposalId, 'lpj' => $lpjId, 'lpjFile' => $fileId]) }}" target="_blank"
                                                               class="px-3 py-2 rounded-lg bg-yellow-500 text-white hover:bg-yellow-600 text-sm">
                                                                Preview
                                                            </a>

                                                            <a href="{{ route('lpj.files.download', ['proposal' => $proposalId, 'lpj' => $lpjId, 'lpjFile' => $fileId]) }}"
                                                               class="px-3 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 text-sm">
                                                                Download
                                                            </a>
                                                        @endif
                                                    @endif

                                                    {{-- Setting --}}
                                                    <button type="button"
                                                            class="px-3 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 text-sm"
                                                            data-modal-open="settingItem{{ $item->id }}">
                                                        Setting
                                                    </button>

                                                    {{-- Remove from folder (icon trash) --}}
                                                    <form method="POST"
                                                          action="{{ route('folders.items.destroy', $item) }}"
                                                          class="delete-item-form">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                                class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-red-600 text-white hover:bg-red-700"
                                                                aria-label="Hapus dari folder"
                                                                title="Hapus dari folder">
                                                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                                <path d="M3 6h18"/>
                                                                <path d="M8 6V4h8v2"/>
                                                                <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                                                <path d="M10 11v6"/>
                                                                <path d="M14 11v6"/>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </div>

                                                {{-- MODAL: SETTING --}}
                                                <div id="settingItem{{ $item->id }}"
                                                     class="hidden fixed inset-0 z-50 bg-black/50 p-4">
                                                    <div class="bg-white w-full max-w-4xl rounded-xl shadow-lg mx-auto my-6 max-h-[85vh] flex flex-col overflow-hidden">

                                                        <div class="flex items-center justify-between px-5 py-4 border-b">
                                                            <h4 class="font-semibold text-gray-900">Setting Dokumen</h4>
                                                            <button type="button"
                                                                    class="text-gray-500 hover:text-gray-700"
                                                                    data-modal-close="settingItem{{ $item->id }}"
                                                                    aria-label="Tutup">
                                                                ✕
                                                            </button>
                                                        </div>

                                                        <div class="p-5 overflow-y-auto space-y-4">
                                                            <div class="text-sm text-gray-700">
                                                                <div class="font-medium">
                                                                    {{ $meta['title'] }}
                                                                </div>
                                                                <div class="text-xs text-gray-500 mt-1">
                                                                    Jika <b>Private</b> dicentang, dokumen tidak akan muncul di menu <b>Dokumen Dibagikan</b>.
                                                                </div>
                                                            </div>

                                                            <form method="POST"
                                                                  action="{{ route('folders.items.update', $item) }}"
                                                                  class="space-y-4">
                                                                @csrf
                                                                @method('PUT')

                                                                <label class="flex items-center gap-2 text-sm">
                                                                    <input type="hidden" name="is_private" value="0">
                                                                    <input type="checkbox"
                                                                           name="is_private"
                                                                           value="1"
                                                                           {{ $item->is_private ? 'checked' : '' }}>
                                                                    <span>Private (tidak dibagikan)</span>
                                                                </label>

                                                                {{-- ======= ACCORDION per Kedudukan ======= --}}
                                                                <div class="space-y-3">
                                                                    @php
                                                                        $selectedRoles = $item->shared_roles ?? [];
                                                                        $selectedKedudukan = $item->shared_kedudukan ?? [];
                                                                        $selectedBidangs = $item->shared_bidang_ids ?? [];
                                                                        $selectedSies = $item->shared_sie_ids ?? [];
                                                                    @endphp

                                                                    @foreach($kedudukanList as $k => $kLabel)
                                                                        @php
                                                                            $rolesThis = $roleOptionsByKedudukan[$k] ?? [];
                                                                            $bidangsThis = $bidangsByKedudukan[$k] ?? collect();
                                                                            $kedudukanRoleChecked = in_array($k, $selectedKedudukan, true);
                                                                        @endphp

                                                                        <div class="border rounded-lg overflow-hidden">
                                                                            <button type="button"
                                                                                    class="w-full flex items-center justify-between px-4 py-3 bg-gray-50 hover:bg-gray-100"
                                                                                    data-acc-toggle="acc-{{ $item->id }}-{{ $k }}">
                                                                                <div class="text-sm font-semibold text-gray-900">
                                                                                    {{ $kLabel }}
                                                                                </div>
                                                                                <div class="text-xs text-gray-500">
                                                                                    klik untuk buka/tutup
                                                                                </div>
                                                                            </button>

                                                                            <div id="acc-{{ $item->id }}-{{ $k }}" class="hidden px-4 py-4 space-y-5">
                                                                                {{-- ROLES --}}
                                                                                <div>
                                                                                    <div class="text-sm font-medium text-gray-800 mb-2">
                                                                                        Share ke Roles ({{ $kLabel }})
                                                                                    </div>

                                                                                    @if(empty($rolesThis))
                                                                                        <div class="text-xs text-gray-500">
                                                                                            Belum ada role untuk kedudukan ini.
                                                                                        </div>
                                                                                    @else
                                                                                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                                                                                            @foreach($rolesThis as $r)
                                                                                                @php
                                                                                                    $checked = in_array($r, $selectedRoles, true) && $kedudukanRoleChecked;
                                                                                                @endphp

                                                                                                <label class="flex items-center gap-2 text-sm">
                                                                                                    <input type="checkbox"
                                                                                                           name="shared_roles_scoped[{{ $k }}][]"
                                                                                                           value="{{ $r }}"
                                                                                                           {{ $checked ? 'checked' : '' }}>
                                                                                                    <span>{{ $roleLabel($r) }}</span>
                                                                                                </label>
                                                                                            @endforeach
                                                                                        </div>
                                                                                    @endif
                                                                                </div>

                                                                                {{-- BIDANG + SIE --}}
                                                                                <div>
                                                                                    <div class="text-sm font-medium text-gray-800 mb-2">
                                                                                        Share ke Bidang & Sie ({{ $kLabel }})
                                                                                    </div>

                                                                                    @if($bidangsThis->isEmpty())
                                                                                        <div class="text-xs text-gray-500">
                                                                                            Tidak ada Bidang/Sie untuk kedudukan ini.
                                                                                        </div>
                                                                                    @else
                                                                                        <div class="space-y-3">
                                                                                            @foreach($bidangsThis as $b)
                                                                                                <div class="border rounded-lg p-3">
                                                                                                    <label class="flex items-center gap-2 text-sm font-semibold text-gray-900">
                                                                                                        <input type="checkbox"
                                                                                                               class="bidang-checkbox"
                                                                                                               data-bidang="{{ $b->id }}"
                                                                                                               name="shared_bidang_ids[]"
                                                                                                               value="{{ $b->id }}"
                                                                                                               {{ in_array($b->id, $selectedBidangs, true) ? 'checked' : '' }}>
                                                                                                        <span>{{ $b->nama_bidang }}</span>
                                                                                                    </label>

                                                                                                    @php $sies = $b->sies ?? collect(); @endphp

                                                                                                    @if($sies->isNotEmpty())
                                                                                                        <div class="mt-3 pl-6">
                                                                                                            <div class="text-xs text-gray-500 mb-2">Sie di bawah bidang ini</div>

                                                                                                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                                                                                                                @foreach($sies as $s)
                                                                                                                    <label class="flex items-center gap-2 text-sm">
                                                                                                                        <input type="checkbox"
                                                                                                                               class="sie-checkbox"
                                                                                                                               data-bidang="{{ $b->id }}"
                                                                                                                               name="shared_sie_ids[]"
                                                                                                                               value="{{ $s->id }}"
                                                                                                                               {{ in_array($s->id, $selectedSies, true) ? 'checked' : '' }}>
                                                                                                                        <span>{{ $s->nama_sie }}</span>
                                                                                                                    </label>
                                                                                                                @endforeach
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    @endif
                                                                                                </div>
                                                                                            @endforeach
                                                                                        </div>
                                                                                    @endif
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    @endforeach
                                                                </div>

                                                                <div class="flex justify-end gap-2 pt-2">
                                                                    <button type="button"
                                                                            class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-sm"
                                                                            data-modal-close="settingItem{{ $item->id }}">
                                                                        Tutup
                                                                    </button>

                                                                    <button type="submit"
                                                                            class="px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 text-sm font-medium">
                                                                        Simpan
                                                                    </button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                                {{-- END MODAL --}}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div id="noItemResult" class="hidden text-center py-10">
                            <div class="text-gray-900 font-semibold">Item tidak ditemukan</div>
                            <div class="text-sm text-gray-600 mt-1">Coba kata kunci lain.</div>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

    @once
        <script>
            // Search items (client-side)
            const itemSearch = document.getElementById('itemSearch');
            const itemRows = document.querySelectorAll('.item-row');
            const noItemResult = document.getElementById('noItemResult');

            if (itemSearch) {
                itemSearch.addEventListener('input', () => {
                    const q = (itemSearch.value || '').toLowerCase().trim();
                    let visible = 0;

                    itemRows.forEach(row => {
                        const hay = (row.getAttribute('data-search') || '').toLowerCase();
                        const show = hay.includes(q);
                        row.classList.toggle('hidden', !show);
                        if (show) visible++;
                    });

                    if (noItemResult) noItemResult.classList.toggle('hidden', visible !== 0);
                });
            }

            // Modal open/close (fix: jangan cuma putih, pastikan body kebuka & backdrop bisa klik)
            document.querySelectorAll('[data-modal-open]').forEach(btn => {
                btn.addEventListener('click', () => {
                    const id = btn.getAttribute('data-modal-open');
                    const modal = document.getElementById(id);
                    if (!modal) return;
                    modal.classList.remove('hidden');

                    // prevent body scroll
                    document.documentElement.classList.add('overflow-hidden');
                    document.body.classList.add('overflow-hidden');
                });
            });

            document.querySelectorAll('[data-modal-close]').forEach(btn => {
                btn.addEventListener('click', () => {
                    const id = btn.getAttribute('data-modal-close');
                    const modal = document.getElementById(id);
                    if (!modal) return;
                    modal.classList.add('hidden');

                    document.documentElement.classList.remove('overflow-hidden');
                    document.body.classList.remove('overflow-hidden');
                });
            });

            // click backdrop close
            document.querySelectorAll('[id^="settingItem"]').forEach(modal => {
                modal.addEventListener('click', (e) => {
                    if (e.target === modal) {
                        modal.classList.add('hidden');
                        document.documentElement.classList.remove('overflow-hidden');
                        document.body.classList.remove('overflow-hidden');
                    }
                });
            });

            // SweetAlert2 confirm delete item
            document.querySelectorAll('.delete-item-form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    Swal.fire({
                        title: 'Hapus dokumen dari folder?',
                        text: 'Dokumen aslinya tidak terhapus, hanya dilepas dari folder.',
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

            // Bidang -> toggle semua Sie di bawahnya
            document.addEventListener('change', function (e) {
                if (e.target.matches('.bidang-checkbox')) {
                    const bidangId = e.target.getAttribute('data-bidang');
                    const modal = e.target.closest('[id^="settingItem"]') || document;

                    modal.querySelectorAll('.sie-checkbox[data-bidang="'+bidangId+'"]').forEach(cb => {
                        cb.checked = e.target.checked;
                    });

                    e.target.indeterminate = false;
                    return;
                }

                if (e.target.matches('.sie-checkbox')) {
                    const bidangId = e.target.getAttribute('data-bidang');
                    const modal = e.target.closest('[id^="settingItem"]') || document;

                    const sies = Array.from(modal.querySelectorAll('.sie-checkbox[data-bidang="'+bidangId+'"]'));
                    const bidang = modal.querySelector('.bidang-checkbox[data-bidang="'+bidangId+'"]');
                    if (!bidang || sies.length === 0) return;

                    const allChecked = sies.every(cb => cb.checked);
                    const noneChecked = sies.every(cb => !cb.checked);

                    bidang.checked = allChecked;
                    bidang.indeterminate = !allChecked && !noneChecked;
                }
            });

            // Accordion toggle (dalam modal masing-masing)
            document.addEventListener('click', function (e) {
                const btn = e.target.closest('[data-acc-toggle]');
                if (!btn) return;

                const id = btn.getAttribute('data-acc-toggle');
                const panel = document.getElementById(id);
                if (!panel) return;

                const modal = btn.closest('[id^="settingItem"]');
                if (!modal) return;

                // tutup panel lain di modal yang sama
                modal.querySelectorAll('[id^="acc-"]').forEach(el => {
                    if (el.id !== id) el.classList.add('hidden');
                });

                panel.classList.toggle('hidden');
            });
        </script>
    @endonce
</x-app-layout>
