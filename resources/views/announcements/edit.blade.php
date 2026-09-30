<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Pengumuman</h2>
            <p class="text-sm text-gray-500">Perbarui informasi pengumuman.</p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-4">

            <a href="{{ route('announcements.show', $announcement) }}"
               class="inline-flex items-center px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">
                ← Kembali
            </a>

            <div class="bg-white shadow sm:rounded-lg p-6">
                <form method="POST" action="{{ route('announcements.update', $announcement) }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label value="Judul" />
                        <x-text-input name="title" class="mt-1 block w-full" required
                                      value="{{ old('title', $announcement->title) }}" />
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label value="Isi Pengumuman" />
                        <textarea name="body" rows="8"
                                  class="mt-1 block w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-200 focus:border-blue-400"
                                  required>{{ old('body', $announcement->body) }}</textarea>
                        <x-input-error :messages="$errors->get('body')" class="mt-2" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-input-label value="Mulai tampil (opsional)" />
                            <input type="datetime-local" name="starts_at"
                                   class="mt-1 block w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-200 focus:border-blue-400"
                                   value="{{ old('starts_at', optional($announcement->starts_at)->format('Y-m-d\TH:i')) }}">
                            <x-input-error :messages="$errors->get('starts_at')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label value="Berakhir (opsional)" />
                            <input type="datetime-local" name="ends_at"
                                   class="mt-1 block w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-200 focus:border-blue-400"
                                   value="{{ old('ends_at', optional($announcement->ends_at)->format('Y-m-d\TH:i')) }}">
                            <x-input-error :messages="$errors->get('ends_at')" class="mt-2" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-input-label value="Status" />
                            <select name="status"
                                    class="mt-1 block w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-200 focus:border-blue-400"
                                    required>
                                <option value="published" {{ old('status', $announcement->status) === 'published' ? 'selected' : '' }}>Published</option>
                                <option value="draft" {{ old('status', $announcement->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>

                        <div class="flex items-center gap-2 pt-6">
                            <input type="hidden" name="is_pinned" value="0">
                            <input type="checkbox" name="is_pinned" value="1"
                                {{ old('is_pinned', $announcement->is_pinned) ? 'checked' : '' }}>
                            <span class="text-sm text-gray-800">Pin pengumuman</span>
                        </div>
                    </div>

                    @php
                        $selectedK = old('target_kedudukan', $announcement->target_kedudukan ?? []);
                        $selectedR = old('target_roles', $announcement->target_roles ?? []);
                    @endphp

                    <div class="bg-gray-50 border rounded-lg p-4">
                        <div class="font-semibold text-gray-900">Target (opsional)</div>
                        <div class="text-xs text-gray-500 mt-1">
                            Jika tidak memilih apapun, pengumuman akan tampil untuk semua user.
                        </div>

                        <div class="mt-4 space-y-3">
                            @foreach($kedudukanList as $k => $kLabel)
                                @php
                                    $rolesThis = $roleOptionsByKedudukan[$k] ?? [];
                                @endphp

                                <div class="border rounded-lg overflow-hidden">
                                    <button type="button"
                                            class="w-full flex items-center justify-between px-4 py-3 bg-gray-100 hover:bg-gray-200"
                                            data-acc-toggle="acc-edit-{{ $k }}">
                                        <span class="font-medium text-gray-800">{{ $kLabel }}</span>
                                        <span class="text-xs text-gray-500">klik</span>
                                    </button>

                                    <div id="acc-edit-{{ $k }}" class="hidden px-4 py-4 space-y-3">
                                        {{-- Kedudukan --}}
                                        <label class="flex items-center gap-2 text-sm font-semibold">
                                            <input type="checkbox" name="target_kedudukan[]" value="{{ $k }}"
                                                {{ in_array($k, $selectedK, true) ? 'checked' : '' }}>
                                            <span>Semua {{ $kLabel }}</span>
                                        </label>

                                        {{-- Roles scoped --}}
                                        @if(!empty($rolesThis))
                                            <div class="grid grid-cols-2 gap-2 pl-6">
                                                @foreach($rolesThis as $r)
                                                    <label class="flex items-center gap-2 text-sm">
                                                        <input type="checkbox" name="target_roles[]" value="{{ $r }}"
                                                            {{ in_array($r, $selectedR, true) ? 'checked' : '' }}>
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
                        <a href="{{ route('announcements.show', $announcement) }}"
                           class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300">
                            Batal
                        </a>
                        <button type="submit"
                                class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700">
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