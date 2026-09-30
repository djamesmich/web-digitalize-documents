<section>
    <header class="flex items-start justify-between gap-4">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">
                Informasi Profile
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Perbarui nama dan email akun Anda.
            </p>
        </div>
        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs bg-blue-50 text-blue-700">
            Akun
        </span>
    </header>

    <div class="mt-6 border-t pt-6">
        <form id="send-verification" method="post" action="{{ route('verification.send') }}">
            @csrf
        </form>

        <form method="post" action="{{ route('profile.update') }}" class="space-y-5">
            @csrf
            @method('patch')

            <div>
                <x-input-label for="name" :value="__('Nama')" />
                <x-text-input
                    id="name"
                    name="name"
                    type="text"
                    class="mt-1 block w-full"
                    :value="old('name', $user->name)"
                    required
                    autofocus
                    autocomplete="name"
                />
                <x-input-error class="mt-2" :messages="$errors->get('name')" />
            </div>

            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input
                    id="email"
                    name="email"
                    type="email"
                    class="mt-1 block w-full"
                    :value="old('email', $user->email)"
                    required
                    autocomplete="username"
                />
                <x-input-error class="mt-2" :messages="$errors->get('email')" />

                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <div class="mt-3 rounded-xl border border-amber-200 bg-amber-50 p-4">
                        <div class="text-sm text-amber-900 font-medium">Email belum terverifikasi</div>
                        <p class="text-sm text-amber-800 mt-1">
                            Silakan verifikasi email Anda agar akun lebih aman.
                        </p>

                        <button
                            form="send-verification"
                            class="mt-3 inline-flex items-center justify-center px-4 py-2 rounded-lg bg-amber-600 text-white hover:bg-amber-700 text-sm font-semibold"
                        >
                            Kirim ulang email verifikasi
                        </button>

                        @if (session('status') === 'verification-link-sent')
                            <p class="mt-2 text-sm text-green-700 font-medium">
                                Link verifikasi baru sudah dikirim ke email Anda.
                            </p>
                        @endif
                    </div>
                @endif
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <x-primary-button>
                    Simpan Perubahan
                </x-primary-button>

                @if (session('status') === 'profile-updated')
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
    </div>
</section>
