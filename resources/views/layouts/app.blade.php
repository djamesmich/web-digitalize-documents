<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>[x-cloak]{display:none!important}</style>
</head>

<body
    class="font-sans antialiased bg-gray-100"
    x-data="{
        // desktop collapse state
        sidebarOpen: JSON.parse(localStorage.getItem('sidebarOpen') ?? 'true'),

        // mobile drawer state
        mobileOpen: false,

        toggleSidebar() {
            const isMobile = window.matchMedia('(max-width: 1023px)').matches;

            if (isMobile) {
                this.mobileOpen = !this.mobileOpen;
                return;
            }

            this.sidebarOpen = !this.sidebarOpen;
            localStorage.setItem('sidebarOpen', JSON.stringify(this.sidebarOpen));
        },

        closeMobile() {
            this.mobileOpen = false;
        }
    }"
    x-cloak
>
    {{-- OVERLAY (mobile) --}}
    <div
        class="fixed inset-0 bg-black/40 z-40 lg:hidden"
        x-show="mobileOpen"
        x-transition.opacity.duration.150ms
        @click="closeMobile()"
    ></div>

    @include('layouts.navigation')

    {{-- CONTENT WRAPPER --}}
    <div
        class="min-h-screen flex flex-col transition-[margin-left] duration-200 ease-in-out"
        :class="sidebarOpen ? 'lg:ml-64' : 'lg:ml-20'"
    >
        @isset($header)
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-5 px-4 sm:px-6 lg:px-8 flex items-center gap-3">
                    {{-- tombol burger --}}
                    <button type="button"
                        class="inline-flex items-center justify-center w-10 h-10 rounded-xl border bg-white hover:bg-gray-50"
                        @click="toggleSidebar()"
                        aria-label="Toggle sidebar"
                    >
                        ☰
                    </button>

                    <div class="flex-1">
                        {{ $header }}
                    </div>
                </div>
            </header>
        @endisset

        <main class="flex-1 overflow-y-auto p-6">
            {{ $slot }}
        </main>
    </div>

    {{-- MODAL TOGGLE (yang kamu pakai di Bidang/Sie) --}}
    <script>
        document.querySelectorAll("[data-modal-toggle]").forEach(btn => {
            btn.addEventListener("click", function () {
                const id = this.getAttribute("data-modal-target");
                document.getElementById(id)?.classList.remove("hidden");
            });
        });

        document.querySelectorAll("[data-modal-hide]").forEach(btn => {
            btn.addEventListener("click", function () {
                const id = this.getAttribute("data-modal-hide");
                document.getElementById(id)?.classList.add("hidden");
            });
        });
    </script>

    {{-- SweetAlert Notif --}}
    @if(session('status'))
        <script>
            Swal.fire({
                icon: 'success',
                title: @json(session('status')),
                timer: 2000,
                showConfirmButton: false
            });
        </script>
    @endif

    @if(session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: "Terjadi Kesalahan",
                text: @json(session('error')),
            });
        </script>
    @endif

    <x-flash-swal />
</body>
</html>
