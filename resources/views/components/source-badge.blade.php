@props(['sourceLabel'])

@php
    $ui = match ($sourceLabel) {
        'Template'     => ['label' => 'Template',          'cls' => 'bg-violet-50 text-violet-700'],
        'ProposalFile' => ['label' => 'Lampiran Proposal', 'cls' => 'bg-sky-50 text-sky-700'],
        'Document'     => ['label' => 'Dokumen',           'cls' => 'bg-emerald-50 text-emerald-700'],
        'LpjFile'      => ['label' => 'Lampiran LPJ',      'cls' => 'bg-amber-50 text-amber-700'],
        default        => ['label' => 'File',              'cls' => 'bg-gray-100 text-gray-700'],
    };
@endphp

<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold {{ $ui['cls'] }}">
    {{ $ui['label'] }}
</span>
