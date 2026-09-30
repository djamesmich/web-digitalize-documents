<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Notulensi</h2>
            <p class="text-sm text-gray-500">Perbarui informasi rapat, isi notulensi, dan target share.</p>
        </div>
    </x-slot>

    @php
        $kedLabel = $kedudukanOptions ?? [
            'dpp_inti' => 'DPP Harian',
            'bgkp' => 'BGKP',
            'lingkungan' => 'Lingkungan',
            'sekretariat' => 'Sekretariat',
        ];

        $selectedK = old('target_kedudukan', $minute->target_kedudukan ?? []);
        $selectedR = old('target_roles', $minute->target_roles ?? []);
    @endphp

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">

            <div class="flex items-center justify-between gap-3 flex-wrap">
                <a href="{{ route('minutes.show', $minute) }}"
                   class="inline-flex items-center px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300">
                    ← Kembali
                </a>

                <a href="{{ route('minutes.index') }}"
                   class="inline-flex items-center px-4 py-2 rounded-lg bg-white border hover:bg-gray-50">
                    Lihat Daftar
                </a>
            </div>

            <div class="bg-white shadow sm:rounded-lg p-6">
                <form method="POST" action="{{ route('minutes.update', $minute) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    {{-- Info Rapat --}}
                    <div class="border rounded-lg p-4">
                        <div class="font-semibold text-gray-900 mb-4">Informasi Rapat</div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="sm:col-span-2">
                                <x-input-label value="Judul Rapat" />
                                <x-text-input name="title" class="mt-1 block w-full" required value="{{ old('title', $minute->title) }}" />
                                <x-input-error :messages="$errors->get('title')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label value="Tanggal & Jam Rapat" />
                                <input type="datetime-local" name="meeting_at"
                                       class="mt-1 block w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-200 focus:border-blue-400"
                                       required
                                       value="{{ old('meeting_at', optional($minute->meeting_at)->format('Y-m-d\TH:i')) }}">
                                <x-input-error :messages="$errors->get('meeting_at')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label value="Lokasi (opsional)" />
                                <x-text-input name="location" class="mt-1 block w-full" value="{{ old('location', $minute->location) }}" />
                                <x-input-error :messages="$errors->get('location')" class="mt-2" />
                            </div>

                            <div class="sm:col-span-2">
                                <x-input-label value="Agenda (opsional)" />
                                <textarea name="agenda" rows="3"
                                          class="mt-1 block w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-200 focus:border-blue-400">{{ old('agenda', $minute->agenda) }}</textarea>
                                <x-input-error :messages="$errors->get('agenda')" class="mt-2" />
                            </div>
                        </div>
                    </div>

                    {{-- Isi --}}
                    <div class="border rounded-lg p-4">
                        <div class="font-semibold text-gray-900 mb-4">Isi Notulensi</div>

                        <x-input-label value="Catatan Notulensi" />
                        <textarea name="content" rows="10"
                                  class="mt-1 block w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-200 focus:border-blue-400"
                                  required>{{ old('content', $minute->content) }}</textarea>
                        <x-input-error :messages="$errors->get('content')" class="mt-2" />
                    </div>

                    {{-- Status + Target --}}
                    <div class="border rounded-lg p-4 space-y-4">
                        <div class="font-semibold text-gray-900">Publikasi & Share</div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label value="Status" />
                                <select name="status"
                                        class="mt-1 block w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-200 focus:border-blue-400"
                                        required>
                                    <option value="draft" {{ old('status', $minute->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="published" {{ old('status', $minute->status) === 'published' ? 'selected' : '' }}>Published</option>
                                </select>
                                <x-input-error :messages="$errors->get('status')" class="mt-2" />
                            </div>

                            <div class="bg-gray-50 border rounded-lg p-3">
                                <div class="text-sm font-semibold text-gray-900">Target (Opsional)</div>
                                <div class="text-xs text-gray-500 mt-1">
                                    Jika kosong, notulensi tampil untuk semua user yang punya izin view.
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            <div class="bg-gray-50 border rounded-lg p-4">
                                <div class="text-sm font-semibold text-gray-900 mb-2">Target Kedudukan</div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    @foreach($kedLabel as $val => $label)
                                        <label class="flex items-center gap-3 text-sm p-2 rounded-lg hover:bg-white">
                                            <input type="checkbox" class="w-4 h-4"
                                                   name="target_kedudukan[]" value="{{ $val }}"
                                                   {{ in_array($val, $selectedK) ? 'checked' : '' }}>
                                            <span class="text-gray-800">{{ $label }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <div class="bg-gray-50 border rounded-lg p-4">
                                <div class="font-semibold text-gray-900">Target (opsional)</div>
                                <div class="text-xs text-gray-500 mt-1">
                                    Jika tidak memilih apapun, notulensi akan tampil untuk semua user yang punya akses view.
                                </div>

                                <div class="mt-4 space-y-3">
                                    @foreach($kedudukanList as $k => $kLabel)
                                        @php $rolesThis = $roleOptionsByKedudukan[$k] ?? []; @endphp

                                        <div class="border rounded-lg overflow-hidden bg-white">
                                            <button type="button"
                                                    class="w-full flex items-center justify-between px-4 py-3 bg-gray-100 hover:bg-gray-200"
                                                    data-acc-toggle="acc-min-edit-{{ $k }}">
                                                <span class="font-medium text-gray-800">{{ $kLabel }}</span>
                                                <span class="text-xs text-gray-500">klik</span>
                                            </button>

                                            <div id="acc-min-edit-{{ $k }}" class="hidden px-4 py-4 space-y-3">
                                                <label class="flex items-center gap-2 text-sm font-semibold">
                                                    <input type="checkbox" name="target_kedudukan[]" value="{{ $k }}"
                                                        {{ in_array($k, $selectedK) ? 'checked' : '' }}>
                                                    <span>Semua {{ $kLabel }}</span>
                                                </label>

                                                @if(!empty($rolesThis))
                                                    <div class="grid grid-cols-2 gap-2 pl-6">
                                                        @foreach($rolesThis as $r)
                                                            <label class="flex items-center gap-2 text-sm">
                                                                <input type="checkbox" name="target_roles[]" value="{{ $r }}"
                                                                    {{ in_array($r, $selectedR) ? 'checked' : '' }}>
                                                                <span>{{ \Illuminate\Support\Str::headline(str_replace('_',' ',$r)) }}</span>
                                                            </label>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <div class="text-xs text-gray-500 pl-6">
                                                        Tidak ada role aktif di kedudukan ini.
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center justify-end gap-2 pt-2">
                        <a href="{{ route('minutes.show', $minute) }}"
                           class="px-5 py-3 rounded-lg bg-gray-200 hover:bg-gray-300 font-semibold">
                            Batal
                        </a>

                        <button type="submit"
                                class="px-6 py-3 rounded-lg bg-blue-600 text-white hover:bg-blue-700 font-semibold">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
    @once
    <script>
        document.querySelectorAll('[data-acc-toggle]').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.getAttribute('data-acc-toggle');
                document.getElementById(id)?.classList.toggle('hidden');
            });
        });
    </script>
    @endonce
</x-app-layout>
