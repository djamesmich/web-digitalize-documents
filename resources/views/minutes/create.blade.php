<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Buat Notulensi</h2>
            <p class="text-sm text-gray-500">Isi informasi rapat dan catatan notulensi dengan jelas.</p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-4">

            <a href="{{ route('minutes.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">
                ← Kembali
            </a>

            <div class="bg-white shadow sm:rounded-lg p-6">
                <form method="POST" action="{{ route('minutes.store') }}" class="space-y-4" novalidate>
                    @csrf

                    <div>
                        <x-input-label value="Judul Rapat" />
                        <x-text-input name="title" class="mt-1 block w-full" required value="{{ old('title') }}" />
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-input-label value="Tanggal & Jam Rapat" />
                            <input type="datetime-local" name="meeting_at"
                                   class="mt-1 block w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-200 focus:border-blue-400"
                                   required value="{{ old('meeting_at') }}">
                            <x-input-error :messages="$errors->get('meeting_at')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label value="Lokasi (opsional)" />
                            <x-text-input name="location" class="mt-1 block w-full" value="{{ old('location') }}" />
                            <x-input-error :messages="$errors->get('location')" class="mt-2" />
                        </div>
                    </div>

                    <div>
                        <x-input-label value="Agenda (opsional)" />
                        <textarea name="agenda" rows="3"
                                  class="mt-1 block w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-200 focus:border-blue-400"
                                  placeholder="Contoh: Evaluasi kegiatan, rencana jadwal, pembagian tugas...">{{ old('agenda') }}</textarea>
                        <x-input-error :messages="$errors->get('agenda')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label value="Isi Notulensi" />
                        <textarea name="content" rows="10"
                                  class="mt-1 block w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-200 focus:border-blue-400"
                                  placeholder="Tulis poin-poin rapat secara ringkas dan jelas..."
                                  required>{{ old('content') }}</textarea>
                        <x-input-error :messages="$errors->get('content')" class="mt-2" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-input-label value="Status" />
                            <select name="status"
                                    class="mt-1 block w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-200 focus:border-blue-400"
                                    required>
                                <option value="draft" {{ old('status','draft') === 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="published" {{ old('status') === 'published' ? 'selected' : '' }}>Publish</option>
                            </select>
                            <div class="text-xs text-gray-500 mt-1">
                                Draft = disiapkan dulu. Publish = siap dibaca user sesuai target.
                            </div>
                        </div>

                        <div class="flex items-center gap-2 pt-6">
                            <span class="text-xs text-gray-500">
                                Tip: kalau ingin untuk semua, biarkan target kosong.
                            </span>
                        </div>
                    </div>

                    {{-- TARGET / SHARE (SAMAKAN DENGAN PENGUMUMAN) --}}
                    @php
                        $selectedK = old('target_kedudukan', []);
                        $selectedR = old('target_roles', []);
                    @endphp

                    <div class="bg-gray-50 border rounded-lg p-4">
                        <div class="font-semibold text-gray-900">Target (opsional)</div>
                        <div class="text-xs text-gray-500 mt-1">
                            Jika tidak memilih apapun, notulensi tampil untuk semua user (yang punya akses view).
                        </div>

                        <div class="mt-4 space-y-3">
                            @foreach($kedudukanList as $k => $kLabel)
                                @php
                                    $rolesThis = $roleOptionsByKedudukan[$k] ?? [];
                                @endphp

                                <div class="border rounded-lg overflow-hidden bg-white">
                                    <button type="button"
                                            class="w-full flex items-center justify-between px-4 py-3 bg-gray-100 hover:bg-gray-200"
                                            data-acc-toggle="acc-minute-{{ $k }}">
                                        <span class="font-medium text-gray-800">{{ $kLabel }}</span>
                                        <span class="text-xs text-gray-500">klik</span>
                                    </button>

                                    <div id="acc-minute-{{ $k }}" class="hidden px-4 py-4 space-y-3">
                                        {{-- Kedudukan (Semua) --}}
                                        <label class="flex items-center gap-2 text-sm font-semibold">
                                            <input type="checkbox"
                                                class="mm-ked-all"
                                                name="target_kedudukan[]"
                                                value="{{ $k }}"
                                                data-ked="{{ $k }}"
                                                {{ in_array($k, $selectedK) ? 'checked' : '' }}>
                                            <span>Semua {{ $kLabel }}</span>
                                        </label>

                                        {{-- Roles scoped --}}
                                        @if(!empty($rolesThis))
                                            <div class="grid grid-cols-2 gap-2 pl-6">
                                                @foreach($rolesThis as $r)
                                                    <label class="flex items-center gap-2 text-sm">
                                                        <input type="checkbox"
                                                            class="mm-role"
                                                            name="target_roles[]"
                                                            value="{{ $r }}"
                                                            data-ked="{{ $k }}"
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

                    <div class="flex justify-end gap-2 pt-2">
                        <a href="{{ route('minutes.index') }}"
                           class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300">
                            Batal
                        </a>
                        <button type="submit"
                                class="px-4 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @once
    <script>
        // Accordion toggle
        document.querySelectorAll('[data-acc-toggle]').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.getAttribute('data-acc-toggle');
                document.getElementById(id)?.classList.toggle('hidden');
            });
        });

        // ===== Auto-check logic =====
        // 1) Jika "Semua Kedudukan" dicentang => centang semua role pada kedudukan tsb
        // 2) Jika "Semua Kedudukan" di-uncheck => uncheck semua role pada kedudukan tsb
        // 3) Jika semua role kedudukan dicentang manual => "Semua Kedudukan" ikut checked
        // 4) Jika ada role yang di-uncheck => "Semua Kedudukan" ikut uncheck

        function getRolesByKed(ked) {
            return Array.from(document.querySelectorAll('.mm-role[data-ked="'+ked+'"]'));
        }
        function getKedAll(ked) {
            return document.querySelector('.mm-ked-all[data-ked="'+ked+'"]');
        }
        function syncKedAllFromRoles(ked) {
            const roles = getRolesByKed(ked);
            const kedAll = getKedAll(ked);
            if (!kedAll) return;

            if (roles.length === 0) return; // tidak ada role
            const allChecked = roles.every(cb => cb.checked);
            kedAll.checked = allChecked;
        }

        // Kedudukan "Semua"
        document.querySelectorAll('.mm-ked-all').forEach(kedAll => {
            kedAll.addEventListener('change', () => {
                const ked = kedAll.getAttribute('data-ked');
                const roles = getRolesByKed(ked);
                roles.forEach(cb => cb.checked = kedAll.checked);
            });
        });

        // Roles
        document.querySelectorAll('.mm-role').forEach(roleCb => {
            roleCb.addEventListener('change', () => {
                const ked = roleCb.getAttribute('data-ked');
                syncKedAllFromRoles(ked);
            });
        });

        // Saat load: kalau "Semua Kedudukan" sudah checked dari old(), pastikan roles ikut checked
        document.querySelectorAll('.mm-ked-all').forEach(kedAll => {
            const ked = kedAll.getAttribute('data-ked');
            if (kedAll.checked) {
                const roles = getRolesByKed(ked);
                roles.forEach(cb => cb.checked = true);
            } else {
                // kalau tidak checked, cek apakah roles sudah semua checked -> set kedAll
                syncKedAllFromRoles(ked);
            }
        });
    </script>
    @endonce
</x-app-layout>