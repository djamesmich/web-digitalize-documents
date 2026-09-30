<section>
    <header class="flex items-start justify-between gap-4">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">
                Keamanan Password
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Gunakan password yang kuat agar akun tetap aman.
            </p>
        </div>
        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs bg-emerald-50 text-emerald-700">
            Security
        </span>
    </header>

    <div class="mt-6 border-t pt-6">
        <form method="post" action="{{ route('password.update') }}" class="space-y-5">
            @csrf
            @method('put')

            <div>
                <x-input-label for="update_password_current_password" :value="__('Password Saat Ini')" />
                <x-text-input
                    id="update_password_current_password"
                    name="current_password"
                    type="password"
                    class="mt-1 block w-full"
                    autocomplete="current-password"
                />
                <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="update_password_password" :value="__('Password Baru')" />
                <x-text-input
                    id="update_password_password"
                    name="password"
                    type="password"
                    class="mt-1 block w-full"
                    autocomplete="new-password"
                />
                <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="update_password_password_confirmation" :value="__('Konfirmasi Password')" />
                <x-text-input
                    id="update_password_password_confirmation"
                    name="password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    autocomplete="new-password"
                />
                <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <x-primary-button>
                    Update Password
                </x-primary-button>

                @if (session('status') === 'password-updated')
                    <p
                        x-data="{ show: true }"
                        x-show="show"
                        x-transition
                        x-init="setTimeout(() => show = false, 2000)"
                        class="text-sm text-gray-500"
                    >
                        Tersimpan.
                    </p>
                @endif
            </div>
        </form>

        <div class="mt-5 rounded-xl border border-gray-200 bg-gray-50 p-4">
            <div class="text-sm font-semibold text-gray-900">Tips password aman</div>
            <ul class="mt-2 text-sm text-gray-600 list-disc pl-5 space-y-1">
                <li>Minimal 8–12 karakter</li>
                <li>Gabungkan huruf besar, kecil, angka, dan simbol</li>
                <li>Jangan gunakan password yang sama untuk akun lain</li>
            </ul>
        </div>
    </div>
</section>
