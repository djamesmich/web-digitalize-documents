<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dokumen yang Dibagikan
            </h2>
            <p class="text-sm text-gray-500">
                Daftar dokumen yang dapat Anda akses. Gunakan pencarian untuk menemukan dokumen dengan cepat.
            </p>
        </div>
    </x-slot>

    @php
        // Ikon simple (SVG)
        $typeIcon = function (): string {
            return <<<SVG
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <path d="M14 2v6h6"/>
                </svg>
SVG;
        };
    @endphp

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-4">

            {{-- TOP BAR --}}
            <div class="bg-white shadow sm:rounded-lg p-4">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    <div class="text-sm text-gray-600">
                        Total: <span class="font-semibold text-gray-900">{{ $items->count() }}</span> item
                        <span class="text-gray-400">•</span>
                        <span class="text-gray-500">Yang tidak tampil berarti belum dibagikan ke Anda.</span>
                    </div>

                    <div class="relative w-full md:w-auto">
                        <input
                            id="sharedSearch"
                            type="text"
                            placeholder="Cari judul / nama file..."
                            class="w-full md:w-[28rem] pl-10 pr-3 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-200 focus:border-blue-400"
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
                    @if($items->isEmpty())
                        <div class="text-center py-10">
                            <div class="text-gray-900 font-semibold">Belum ada dokumen yang dibagikan</div>
                            <div class="text-sm text-gray-600 mt-1">Jika ada dokumen dibagikan, akan muncul di sini.</div>
                        </div>
                    @else
                        <div class="space-y-3" id="sharedList">
                            @foreach($items as $item)
                                @php
                                    $sourceLabel = class_basename($item->source_type);
                                    $source = $item->source;

                                    // Judul utama + subjudul yang bersih (tanpa mime & proposal id)
                                    $title = '(File hilang)';
                                    $sub   = '';

                                    if ($sourceLabel === 'Template') {
                                        $title = $source?->title ?? '(Template hilang)';
                                        $sub   = $source?->original_name ?? '';
                                    } elseif ($sourceLabel === 'ProposalFile') {
                                        $title = $source?->original_name ?? '(File proposal hilang)';
                                        $sub   = '';
                                    } elseif ($sourceLabel === 'Document') {
                                        $title = $source?->title ?? '(Dokumen hilang)';
                                        $sub   = $source?->filename ?? '';
                                    } elseif ($sourceLabel === 'LpjFile') {
                                        $title = $source?->original_name ?? '(File LPJ hilang)';
                                        $sub   = '';
                                    }

                                    // Badge ekstensi file (PDF/DOCX/dll)
                                    $filenameForExt = $sub ?: ($source?->original_name ?? ($source?->filename ?? ''));
                                    $ext = strtolower(pathinfo($filenameForExt, PATHINFO_EXTENSION));
                                    $extLabel = $ext ? strtoupper($ext) : null;

                                    // Text untuk search (tetap kaya, tapi tidak ditampilkan)
                                    $searchText = strtolower($sourceLabel.' '.$title.' '.$sub);
                                @endphp

                                <div class="shared-row border rounded-xl p-4 hover:bg-gray-50 transition"
                                     data-search="{{ $searchText }}">

                                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                        {{-- LEFT --}}
                                        <div class="flex items-start gap-3 min-w-0">
                                            <div class="mt-1 text-gray-500 shrink-0">
                                                {!! $typeIcon() !!}
                                            </div>

                                            <div class="min-w-0">
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    {{-- ✅ REPLACE: Badge sumber pakai komponen --}}
                                                    <x-source-badge :source-label="$sourceLabel" />

                                                    @if($extLabel)
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-700">
                                                            {{ $extLabel }}
                                                        </span>
                                                    @endif

                                                    @if(!$source)
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-red-50 text-red-700">
                                                            Sumber sudah tidak ada
                                                        </span>
                                                    @endif
                                                </div>

                                                <div class="mt-2 font-semibold text-gray-900 truncate">
                                                    {{ $title }}
                                                </div>

                                                @if(!empty($sub))
                                                    <div class="text-xs text-gray-500 mt-1 truncate">
                                                        {{ $sub }}
                                                    </div>
                                                @else
                                                    <div class="text-xs text-gray-500 mt-1">
                                                        Klik <b>Preview</b> untuk melihat isi, atau <b>Download</b> untuk mengunduh.
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- RIGHT (actions) --}}
                                        <div class="flex items-center gap-2 flex-wrap md:justify-end shrink-0">
                                            {{-- Template --}}
                                            @if($sourceLabel === 'Template' && $source)
                                                @if(strtolower($source->file_type) === 'pdf')
                                                    <a href="{{ route('templates.stream', $source) }}" target="_blank"
                                                       class="px-4 py-2 rounded-lg bg-yellow-500 text-white hover:bg-yellow-600 text-sm font-medium">
                                                        Preview
                                                    </a>
                                                @else
                                                    <a href="{{ route('templates.view', $source) }}" target="_blank"
                                                       class="px-4 py-2 rounded-lg bg-yellow-500 text-white hover:bg-yellow-600 text-sm font-medium">
                                                        Preview
                                                    </a>
                                                @endif

                                                <a href="{{ route('templates.download', $source) }}"
                                                   class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 text-sm font-medium">
                                                    Download
                                                </a>
                                            @endif

                                            {{-- ProposalFile --}}
                                            @if($sourceLabel === 'ProposalFile' && $source)
                                                @php
                                                    $source->loadMissing('proposal');
                                                    $proposalId = $source->proposal_id;
                                                    $fileId = $source->id;
                                                @endphp

                                                @if($proposalId && $fileId)
                                                    <a href="{{ route('proposals.files.preview', ['proposal' => $proposalId, 'file' => $fileId]) }}" target="_blank"
                                                       class="px-4 py-2 rounded-lg bg-yellow-500 text-white hover:bg-yellow-600 text-sm font-medium">
                                                        Preview
                                                    </a>

                                                    <a href="{{ route('proposals.files.download', ['proposal' => $proposalId, 'file' => $fileId]) }}"
                                                       class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 text-sm font-medium">
                                                        Download
                                                    </a>
                                                @endif
                                            @endif

                                            {{-- Document --}}
                                            @if($sourceLabel === 'Document' && $source)
                                                <a href="{{ route('documents.preview', $source) }}" target="_blank"
                                                   class="px-4 py-2 rounded-lg bg-yellow-500 text-white hover:bg-yellow-600 text-sm font-medium">
                                                    Preview
                                                </a>

                                                <a href="{{ route('documents.download', $source) }}"
                                                   class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 text-sm font-medium">
                                                    Download
                                                </a>
                                            @endif

                                            {{-- LPJ File --}}
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
                                                       class="px-4 py-2 rounded-lg bg-yellow-500 text-white hover:bg-yellow-600 text-sm font-medium">
                                                        Preview
                                                    </a>

                                                    <a href="{{ route('lpj.files.download', ['proposal' => $proposalId, 'lpj' => $lpjId, 'lpjFile' => $fileId]) }}"
                                                       class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 text-sm font-medium">
                                                        Download
                                                    </a>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div id="noSharedResult" class="hidden text-center py-10">
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
            const sharedSearch = document.getElementById('sharedSearch');
            const sharedRows = document.querySelectorAll('.shared-row');
            const noSharedResult = document.getElementById('noSharedResult');

            if (sharedSearch) {
                sharedSearch.addEventListener('input', () => {
                    const q = (sharedSearch.value || '').toLowerCase().trim();
                    let visible = 0;

                    sharedRows.forEach(row => {
                        const hay = (row.getAttribute('data-search') || '').toLowerCase();
                        const show = hay.includes(q);
                        row.classList.toggle('hidden', !show);
                        if (show) visible++;
                    });

                    if (noSharedResult) noSharedResult.classList.toggle('hidden', visible !== 0);
                });
            }
        </script>
    @endonce
</x-app-layout>
