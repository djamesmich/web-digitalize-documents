<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Profile</h2>
            <p class="text-sm text-gray-500">
                Kelola informasi akun dan keamanan login Anda.
            </p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- HERO CARD --}}
            <div class="bg-white shadow sm:rounded-2xl p-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-blue-50 text-blue-700 flex items-center justify-center text-xl font-bold">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <div class="font-semibold text-gray-900 truncate">{{ Auth::user()->name }}</div>
                            <div class="text-sm text-gray-500 truncate">
                                {{ Auth::user()->email }}
                            </div>
                            <div class="text-xs text-gray-500 mt-1 truncate">
                                {{ Auth::user()->jabatan_label }}
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        @if (session('status') === 'profile-updated')
                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs bg-green-50 text-green-700">
                                Profile berhasil diperbarui
                            </span>
                        @elseif (session('status') === 'password-updated')
                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs bg-green-50 text-green-700">
                                Password berhasil diperbarui
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- CONTENT GRID --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Profile Info --}}
                <div class="bg-white shadow sm:rounded-2xl p-6">
                    @include('profile.partials.update-profile-information-form')
                </div>

                {{-- Password --}}
                <div class="bg-white shadow sm:rounded-2xl p-6">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
