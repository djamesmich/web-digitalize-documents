{{-- resources/views/proposals/show.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Detail Proposal</h2>
            <p class="text-sm text-gray-500">Lihat informasi lengkap proposal dan lakukan aksi sesuai tahapan.</p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg">

                @php
                    $statusLabel = match($proposal->status) {
                        'menunggu_ketua_bidang' => 'Menunggu Ketua Bidang',
                        'menunggu_romo'         => 'Menunggu Persetujuan DPP',
                        'approved'              => 'Disetujui',
                        'revisi'                => 'Perlu Perbaikan',
                        default                 => ucfirst(str_replace('_',' ', $proposal->status ?? '-')),
                    };

                    $stageLabel = match($proposal->stage) {
                        'sie'          => 'Sie (Pengaju)',
                        'ketua_bidang' => 'Ketua Bidang',
                        'romo'         => 'DPP (Ketua / Sekretaris 1-2)',
                        'bendahara'    => 'Bendahara',
                        default        => ucfirst(str_replace('_',' ', $proposal->stage ?? '-')),
                    };

                    $statusClass = match($proposal->status) {
                        'approved'              => 'bg-green-50 text-green-700 border-green-200',
                        'revisi'                => 'bg-red-50 text-red-700 border-red-200',
                        'menunggu_romo'         => 'bg-yellow-50 text-yellow-800 border-yellow-200',
                        'menunggu_ketua_bidang' => 'bg-gray-50 text-gray-700 border-gray-200',
                        default                 => 'bg-gray-50 text-gray-700 border-gray-200',
                    };

                    $isBendahara = auth()->check() && auth()->user()->hasRole('bendahara');
                @endphp

                {{-- HEADER CARD --}}
                <div class="p-6 border-b bg-gray-50">
                    <div class="flex items-start justify-between gap-4 flex-wrap">
                        <a href="{{ route('proposals.index') }}"
                           class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 rounded-lg hover:bg-gray-50">
                            ← Kembali
                        </a>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs border {{ $statusClass }}">
                            {{ $statusLabel }}
                        </span>
                    </div>

                    <div class="mt-5">
                        <div class="text-sm text-gray-500">Judul</div>
                        <div class="text-2xl font-semibold text-gray-900">{{ $proposal->judul }}</div>
                        <div class="text-xs text-gray-500 mt-2">
                            Dibuat: {{ optional($proposal->created_at)->format('d-m-Y | H:i') }}
                        </div>
                    </div>
                </div>

                <div class="p-6 space-y-6">

                    {{-- INFO GRID --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div class="rounded-lg border bg-white p-4">
                            <div class="text-gray-500">Nomor Proposal</div>
                            <div class="text-gray-900 font-semibold mt-1">{{ $proposal->proposal_no ?? '-' }}</div>
                        </div>
                        <div class="rounded-lg border bg-white p-4">
                            <div class="text-gray-500">Tahap</div>
                            <div class="text-gray-900 font-semibold mt-1">{{ $stageLabel }}</div>
                        </div>
                        <div class="rounded-lg border bg-white p-4">
                            <div class="text-gray-500">Bidang</div>
                            <div class="text-gray-900 font-semibold mt-1">{{ $proposal->bidang?->nama_bidang ?? '-' }}</div>
                        </div>
                        <div class="rounded-lg border bg-white p-4">
                            <div class="text-gray-500">Sie</div>
                            <div class="text-gray-900 font-semibold mt-1">{{ $proposal->sie?->nama_sie ?? '-' }}</div>
                        </div>
                        <div class="rounded-lg border bg-white p-4 sm:col-span-2">
                            <div class="text-gray-500">Pengaju</div>
                            <div class="text-gray-900 font-semibold mt-1">{{ $proposal->pengaju?->name ?? '-' }}</div>
                            <div class="text-xs text-gray-500 mt-1">{{ $proposal->pengaju?->email ?? '-' }}</div>
                        </div>
                        <div class="rounded-lg border bg-white p-4 sm:col-span-2">
                            <div class="text-gray-500">Lampiran</div>
                            <div class="text-gray-900 font-semibold mt-1">{{ $proposal->files?->count() ?? 0 }} file</div>
                        </div>
                    </div>

                    {{-- TUJUAN --}}
                    <div class="rounded-lg border bg-white p-4">
                        <div class="text-gray-500">Tujuan / Keterangan</div>
                        <div class="mt-2 text-gray-900 whitespace-pre-line leading-relaxed">{{ $proposal->tujuan }}</div>
                    </div>

                    {{-- NOTES --}}
                    @if(!empty($proposal->notes))
                        <div class="rounded-lg bg-yellow-50 border border-yellow-200 p-4">
                            <div class="text-sm font-semibold text-yellow-800">Notes</div>
                            <div class="text-sm text-gray-900 whitespace-pre-line mt-2">{{ $proposal->notes }}</div>
                        </div>
                    @endif

                    {{-- CATATAN REVISI --}}
                    @if($proposal->status === 'revisi' && !empty($proposal->reject_reason))
                        <div class="rounded-lg bg-red-50 border border-red-200 p-4">
                            <div class="text-sm font-semibold text-red-700">Catatan Revisi</div>
                            <div class="text-sm text-gray-900 whitespace-pre-line mt-2">{{ $proposal->reject_reason }}</div>
                        </div>
                    @endif

                    {{-- LAMPIRAN --}}
                    <div class="rounded-lg border bg-white p-4 space-y-3">
                        <div class="text-sm font-semibold text-gray-900">Lampiran Proposal</div>
                        @if(($proposal->files?->count() ?? 0) === 0)
                            <div class="text-sm text-gray-600">Belum ada file.</div>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-full text-left border-collapse">
                                    <thead>
                                        <tr class="bg-gray-50 text-gray-700 text-xs uppercase tracking-wide">
                                            <th class="px-4 py-3 w-14">No</th>
                                            <th class="px-4 py-3">Nama File</th>
                                            <th class="px-4 py-3 w-28">Ukuran</th>
                                            <th class="px-4 py-3 text-center w-56">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y">
                                        @foreach($proposal->files as $idx => $f)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-4 py-3 text-sm text-gray-700 font-semibold">{{ $idx + 1 }}</td>
                                                <td class="px-4 py-3">
                                                    <div class="text-sm text-gray-900 font-medium">{{ $f->original_name }}</div>
                                                </td>
                                                <td class="px-4 py-3 text-sm text-gray-700">
                                                    @php $kb = $f->file_size ? number_format($f->file_size / 1024, 2) : null; @endphp
                                                    {{ $kb ? $kb.' KB' : '-' }}
                                                </td>
                                                <td class="px-4 py-3">
                                                    <div class="flex items-center justify-center gap-2 flex-wrap">
                                                        <a href="{{ route('proposals.files.preview', [$proposal, $f]) }}" target="_blank"
                                                           class="px-3 py-2 rounded-lg bg-yellow-500 text-white hover:bg-yellow-600 text-sm">
                                                            Preview
                                                        </a>
                                                        @can('downloadFile', $proposal)
                                                            <a href="{{ route('proposals.files.download', [$proposal, $f]) }}"
                                                               class="px-3 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 text-sm">
                                                                Download
                                                            </a>
                                                        @endcan
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

                    {{-- CATATAN BENDAHARA --}}
                    @if($isBendahara && $proposal->status === 'menunggu_romo' && !Gate::allows('downloadFile', $proposal))
                        <div class="mt-3 rounded-lg bg-yellow-50 border border-yellow-200 p-3 text-sm text-yellow-900">
                            <div class="font-semibold">Catatan</div>
                            <div class="mt-1">File hanya bisa diunduh ketika sudah disetujui oleh Ketua DPP / Sekretaris DPP.</div>
                        </div>
                    @endif

                    {{-- RECEIPT --}}
                    @can('viewReceipt', $proposal)
                        @if($proposal->status === 'approved')
                            <div class="rounded-lg bg-green-50 border border-green-200 p-4">
                                <div class="text-sm font-semibold text-green-800">Bukti Penerimaan Proposal</div>
                                <div class="text-sm text-gray-700 mt-1">
                                    Bukti penerimaan dapat diunduh/preview untuk kemudian di-print dan diserahkan ke Bendahara.
                                </div>
                                <div class="mt-3 flex gap-2 flex-wrap">
                                    <a href="{{ route('proposals.receipt.preview', $proposal) }}" target="_blank"
                                       class="px-3 py-2 rounded-lg bg-gray-800 text-white hover:bg-gray-900 text-sm">
                                        Preview Bukti
                                    </a>
                                    @can('downloadReceipt', $proposal)
                                        <a href="{{ route('proposals.receipt.download', $proposal) }}"
                                           class="px-3 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 text-sm">
                                            Download Bukti
                                        </a>
                                    @endcan
                                </div>
                            </div>
                        @endif
                    @endcan

                    {{-- AKSI --}}
                    <div class="rounded-lg border bg-white p-4 space-y-4">
                        <div class="text-sm font-semibold text-gray-900">Aksi</div>

                        {{-- 1) Ketua Bidang --}}
                        @can('approveKetuaBidang', $proposal)
                            @if($proposal->stage === 'ketua_bidang' && $proposal->status === 'menunggu_ketua_bidang')
                                <div class="flex flex-wrap gap-2">
                                    <form method="POST" action="{{ route('proposals.approve_ketua_bidang', $proposal) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="inline-flex items-center px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 text-sm font-medium">
                                            Setujui
                                        </button>
                                    </form>
                                </div>
                            @endif
                        @endcan

                        {{-- 2) DPP Final --}}
                        @if($proposal->stage === 'romo' && $proposal->status === 'menunggu_romo')
                            <div class="rounded-lg border bg-gray-50 p-4 space-y-3">
                                <div class="text-sm font-semibold text-gray-900">Persetujuan DPP</div>
                                <div class="flex flex-wrap gap-2">
                                    @can('approveRomo', $proposal)
                                        <form method="POST" action="{{ route('proposals.approve_romo', $proposal) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    class="px-4 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700 text-sm font-medium">
                                                ✅ Setujui
                                            </button>
                                        </form>
                                    @endcan

                                    @can('rejectRomo', $proposal)
                                        <button type="button"
                                                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 text-sm font-medium"
                                                onclick="document.getElementById('reject-modal').classList.remove('hidden')">
                                            ✍ Perbaiki
                                        </button>
                                    @endcan
                                </div>
                            </div>
                        @endif

                        {{-- 3) Notes (Sekretaris 1/2) --}}
                        @can('addNotes', $proposal)
                            @if($proposal->stage === 'romo')
                                <div class="rounded-lg border bg-gray-50 p-4">
                                    <div class="text-sm font-semibold text-gray-900">Tambahkan Notes</div>
                                    <form method="POST" action="{{ route('proposals.add_notes', $proposal) }}" class="mt-3 space-y-2">
                                        @csrf
                                        @method('PATCH')
                                        <textarea name="notes" rows="4"
                                                  class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-200 focus:border-blue-400"
                                                  placeholder="Tulis notes yang jelas...">{{ old('notes') }}</textarea>
                                        <button type="submit"
                                                class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 text-sm font-medium">
                                            Simpan Notes
                                        </button>
                                    </form>
                                </div>
                            @endif
                        @endcan

                        {{-- 4) Delete --}}
                        @can('delete', $proposal)
                            <form method="POST" action="{{ route('proposals.destroy', $proposal) }}"
                                  class="proposal-delete-form"
                                  data-title="{{ $proposal->judul }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 text-sm font-medium">
                                    Hapus Proposal
                                </button>
                            </form>
                        @endcan
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- MODAL PERBAIKI --}}
    @can('rejectRomo', $proposal)
        <div id="reject-modal" class="hidden fixed inset-0 z-50">
            <div class="absolute inset-0 bg-black/40"
                 onclick="document.getElementById('reject-modal').classList.add('hidden')"></div>
            <div class="relative max-w-xl mx-auto mt-24 bg-white rounded-lg shadow p-6">
                <div class="text-lg font-semibold text-gray-900">Perbaiki Proposal</div>
                <p class="text-sm text-gray-600 mt-1">Isi catatan revisi agar pengaju bisa memperbaiki proposal.</p>
                <form method="POST" action="{{ route('proposals.reject_romo', $proposal) }}" class="mt-4 space-y-3">
                    @csrf
                    @method('PATCH')
                    <textarea name="reject_reason" rows="5"
                              class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-200 focus:border-blue-400"
                              placeholder="Contoh: Tujuan proposal belum jelas, mohon jelaskan anggaran yang dibutuhkan...">{{ old('reject_reason') }}</textarea>
                    @error('reject_reason')
                        <div class="text-sm text-red-600">{{ $message }}</div>
                    @enderror
                    <div class="flex justify-end gap-2">
                        <button type="button"
                                class="px-4 py-2 rounded-lg border bg-white hover:bg-gray-50 text-sm"
                                onclick="document.getElementById('reject-modal').classList.add('hidden')">
                            Batal
                        </button>
                        <button type="submit"
                                class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 text-sm font-medium">
                            Kirim Perbaikan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endcan
    {{-- END MODAL PERBAIKI --}}

    <script>
        document.querySelectorAll('.proposal-delete-form').forEach(form => {
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const title = form.getAttribute('data-title') || 'proposal ini';
                const res = await Swal.fire({
                    icon: 'warning',
                    title: 'Hapus Proposal?',
                    text: `Yakin ingin menghapus "${title}"? Semua lampiran & bukti penerimaan ikut terhapus.`,
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus',
                    cancelButtonText: 'Batal'
                });
                if (res.isConfirmed) form.submit();
            });
        });
    </script>
</x-app-layout>
