@php
    $isActive = function ($patterns): bool {
        $patterns = is_array($patterns) ? $patterns : [$patterns];
        foreach ($patterns as $p) {
            if (request()->routeIs($p)) return true;
        }
        return false;
    };

    $ic = [
        'announcement' => '<svg viewBox="0 0 24 24" fill="none" class="w-5 h-5 shrink-0" stroke="currentColor" stroke-width="2"><path d="M4 4h16v12H5.5L4 17.5V4z"/><path d="M7 8h10M7 11h10"/></svg>',
        'minutes'      => '<svg viewBox="0 0 24 24" fill="none" class="w-5 h-5 shrink-0" stroke="currentColor" stroke-width="2"><path d="M9 5h6M9 3h6a2 2 0 0 1 2 2v2H7V5a2 2 0 0 1 2-2z"/><path d="M7 7h10v14H7z"/><path d="M9 11h6M9 15h6"/></svg>',
        'doc'          => '<svg viewBox="0 0 24 24" fill="none" class="w-5 h-5 shrink-0" stroke="currentColor" stroke-width="2"><path d="M4 4h10l6 6v10a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2z"/><path d="M14 4v6h6"/></svg>',
        'proposal'     => '<svg viewBox="0 0 24 24" fill="none" class="w-5 h-5 shrink-0" stroke="currentColor" stroke-width="2"><path d="M7 3h7l5 5v13a1 1 0 0 1-1 1H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z"/><path d="M14 3v5h5"/><path d="M9 14l2 2 4-4"/></svg>',
        'folder'       => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="w-5 h-5 shrink-0" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7a2 2 0 012-2h5l2 2h9a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V7z"/></svg>',
        'users'        => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="w-5 h-5 shrink-0" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H2v-2a4 4 0 014-4h1m6-4a4 4 0 11-8 0 4 4 0 018 0zm6 0a3 3 0 11-6 0 3 3 0 016 0z"/></svg>',
        'approval'     => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="w-5 h-5 shrink-0" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 11c1.66 0 3-1.34 3-3S13.66 5 12 5 9 6.34 9 8s1.34 3 3 3z"/><path stroke-linecap="round" stroke-linejoin="round" d="M20 21v-1a7 7 0 00-14 0v1"/></svg>',
        'org'          => '<svg viewBox="0 0 24 24" fill="none" class="w-5 h-5 shrink-0" stroke="currentColor" stroke-width="2"><path d="M12 3v6M5 21v-2m14 2v-2M5 19h14"/><path d="M12 9H5a2 2 0 0 0-2 2v4m9-6h7a2 2 0 0 1 2 2v4"/></svg>',
        'download'     => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="w-5 h-5 shrink-0" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 12v7a1 1 0 001 1h14a1 1 0 001-1v-7M12 3v12m0 0l-3-3m3 3l3-3"/></svg>',
        'profile'      => '<svg viewBox="0 0 24 24" fill="none" class="w-5 h-5 shrink-0" stroke="currentColor" stroke-width="2"><path d="M20 21v-1a7 7 0 0 0-14 0v1"/><path d="M12 11a4 4 0 1 0-4-4 4 4 0 0 0 4 4z"/></svg>',
        'logout'       => '<svg viewBox="0 0 24 24" fill="none" class="w-5 h-5 shrink-0" stroke="currentColor" stroke-width="2"><path d="M10 17l5-5-5-5"/><path d="M15 12H3"/><path d="M21 3v18"/></svg>',
    ];

    $dashActive        = $isActive(['dashboard', 'announcements.*']);
    $minutesActive     = $isActive('minutes.*');
    $docsActive        = $isActive('documents.*');
    $propActive        = $isActive('proposals.*');
    $tplActive         = $isActive('templates.*');
    $usersActive       = $isActive(['users.*']) && ! $isActive('users.pending');
    $pendingActive     = $isActive('users.pending');
    $orgActive         = $isActive(['bidangs.*', 'sies.*']);
    $filesManageActive = $isActive(['folders.*', 'saved-files.*']);
    $filesSharedActive = $isActive('files.shared.*');

    // Macro untuk nav item — kembalikan array data saja, render di Blade
    $nav = fn($active, $href, $label, $icon, $title) => compact('active','href','label','icon','title');

    $menuItems = [
        'Menu' => array_filter([
            $nav($dashActive,    route('dashboard'),       'Pengumuman',        $ic['announcement'], 'Pengumuman'),
            $nav($docsActive,    route('documents.index'), 'Dokumen',           $ic['doc'],          'Dokumen'),
            $nav($propActive,    route('proposals.index'), 'Pengajuan Proposal',$ic['proposal'],     'Pengajuan Proposal'),
            $nav($tplActive,     route('templates.index'), 'Manajemen Template',$ic['folder'],       'Manajemen Template'),
        ]),
    ];
@endphp

<nav
    class="fixed inset-y-0 left-0 bg-white border-r shadow-sm flex flex-col z-50
           transition-[width] duration-200 ease-in-out overflow-x-hidden overflow-y-auto"
    :class="[
        sidebarOpen ? 'w-64' : 'w-[72px]',
        mobileOpen  ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
    ]"
>
    {{-- ===================== BRAND ===================== --}}
    <div class="flex items-center gap-3 px-3 py-[14px] border-b shrink-0">
        <img src="{{ asset('images/logo-rm.png') }}"
             class="w-10 h-10 rounded-xl shrink-0"
             alt="Logo">
        <div x-show="sidebarOpen"
             x-transition:enter="transition-opacity duration-150"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity duration-100"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="overflow-hidden whitespace-nowrap">
            <div class="font-semibold text-gray-900 text-sm">WEB OFFICE</div>
            <div class="text-xs text-gray-500">Church Management</div>
        </div>
    </div>

    {{-- ===================== MENU ===================== --}}
    <div class="flex-1 py-2 px-2 space-y-0.5 overflow-y-auto">

        {{-- Section: Menu --}}
        <div class="px-1 pt-3 pb-1">
            <span x-show="sidebarOpen" class="text-[10px] uppercase tracking-widest text-gray-400 font-semibold">Menu</span>
            <div x-show="!sidebarOpen" class="h-px bg-gray-200 mx-1"></div>
        </div>

        @php $items = [
            ['active'=>$dashActive,    'href'=>route('dashboard'),       'label'=>'Pengumuman',         'icon'=>$ic['announcement'], 'title'=>'Pengumuman'],
            ['active'=>$docsActive,    'href'=>route('documents.index'), 'label'=>'Dokumen',            'icon'=>$ic['doc'],          'title'=>'Dokumen'],
            ['active'=>$propActive,    'href'=>route('proposals.index'), 'label'=>'Pengajuan Proposal', 'icon'=>$ic['proposal'],     'title'=>'Pengajuan Proposal'],
            ['active'=>$tplActive,     'href'=>route('templates.index'), 'label'=>'Manajemen Template', 'icon'=>$ic['folder'],       'title'=>'Manajemen Template'],
        ]; @endphp

        @can('notulensi.view')
            @php array_splice($items, 1, 0, [[
                'active'=>$minutesActive, 'href'=>route('minutes.index'),
                'label'=>'Notulensi Rapat', 'icon'=>$ic['minutes'], 'title'=>'Notulensi Rapat'
            ]]); @endphp
        @endcan

        @foreach($items as $item)
            <a href="{{ $item['href'] }}"
               title="{{ $item['title'] }}"
               class="group flex items-center rounded-xl text-sm font-medium transition-colors duration-150 overflow-hidden
                      {{ $item['active'] ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">

                {{-- Icon wrapper --}}
                <span class="flex items-center justify-center w-[48px] h-[44px] shrink-0
                             {{ $item['active'] ? 'text-blue-700' : 'text-gray-500 group-hover:text-gray-900' }}">
                    {!! $item['icon'] !!}
                </span>

                {{-- Label (hanya saat open) --}}
                <span x-show="sidebarOpen"
                      x-transition:enter="transition-opacity duration-150"
                      x-transition:enter-start="opacity-0"
                      x-transition:enter-end="opacity-100"
                      x-transition:leave="transition-opacity duration-100"
                      x-transition:leave-start="opacity-100"
                      x-transition:leave-end="opacity-0"
                      class="truncate pr-3 whitespace-nowrap">
                    {{ $item['label'] }}
                </span>
            </a>
        @endforeach

        {{-- Section: Admin --}}
        <div class="px-1 pt-4 pb-1">
            <span x-show="sidebarOpen" class="text-[10px] uppercase tracking-widest text-gray-400 font-semibold">Admin</span>
            <div x-show="!sidebarOpen" class="h-px bg-gray-200 mx-1"></div>
        </div>

        @role('super_admin')
            <a href="{{ route('users.index') }}" title="Buat Akun User"
               class="group flex items-center rounded-xl text-sm font-medium transition-colors duration-150 overflow-hidden
                      {{ $usersActive ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                <span class="flex items-center justify-center w-[48px] h-[44px] shrink-0">
                    {!! $ic['users'] !!}
                </span>
                <span x-show="sidebarOpen" x-transition.opacity.duration.150ms class="truncate pr-3 whitespace-nowrap">Buat Akun User</span>
            </a>
        @endrole

        @can('user.manage')
            <a href="{{ route('users.pending') }}" title="User Approval"
               class="group flex items-center rounded-xl text-sm font-medium transition-colors duration-150 overflow-hidden
                      {{ $pendingActive ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                <span class="flex items-center justify-center w-[48px] h-[44px] shrink-0">
                    {!! $ic['approval'] !!}
                </span>
                <span x-show="sidebarOpen" x-transition.opacity.duration.150ms class="truncate pr-3 whitespace-nowrap">User Approval</span>
            </a>
        @endcan

        @role('super_admin')
            <a href="{{ route('bidangs.index') }}" title="Kelola Kedudukan"
               class="group flex items-center rounded-xl text-sm font-medium transition-colors duration-150 overflow-hidden
                      {{ $orgActive ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                <span class="flex items-center justify-center w-[48px] h-[44px] shrink-0">
                    {!! $ic['org'] !!}
                </span>
                <span x-show="sidebarOpen" x-transition.opacity.duration.150ms class="truncate pr-3 whitespace-nowrap">Kelola Kedudukan</span>
            </a>
        @endrole

        {{-- Section: File --}}
        <div class="px-1 pt-4 pb-1">
            <span x-show="sidebarOpen" class="text-[10px] uppercase tracking-widest text-gray-400 font-semibold">File</span>
            <div x-show="!sidebarOpen" class="h-px bg-gray-200 mx-1"></div>
        </div>

        @can('files.manage')
            <a href="{{ route('folders.index') }}" title="Manajemen File"
               class="group flex items-center rounded-xl text-sm font-medium transition-colors duration-150 overflow-hidden
                      {{ $filesManageActive ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                <span class="flex items-center justify-center w-[48px] h-[44px] shrink-0">
                    {!! $ic['folder'] !!}
                </span>
                <span x-show="sidebarOpen" x-transition.opacity.duration.150ms class="truncate pr-3 whitespace-nowrap">Manajemen File</span>
            </a>
        @endcan

        @can('files.view')
            <a href="{{ route('files.shared.index') }}" title="Dokumen Dibagikan"
               class="group flex items-center rounded-xl text-sm font-medium transition-colors duration-150 overflow-hidden
                      {{ $filesSharedActive ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                <span class="flex items-center justify-center w-[48px] h-[44px] shrink-0">
                    {!! $ic['download'] !!}
                </span>
                <span x-show="sidebarOpen" x-transition.opacity.duration.150ms class="truncate pr-3 whitespace-nowrap">Dokumen Dibagikan</span>
            </a>
        @endcan

    </div>

    {{-- ===================== USER SECTION ===================== --}}
    <div class="shrink-0 border-t bg-gray-50">

        {{-- OPEN --}}
        <div x-show="sidebarOpen"
             x-transition.opacity.duration.150ms
             class="p-3 space-y-3">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-sm shrink-0">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <div class="font-semibold text-gray-900 text-sm truncate">{{ Auth::user()->name }}</div>
                    <div class="text-xs text-gray-500 truncate">{{ Auth::user()->jabatan_label }}</div>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-2">
                <a href="{{ route('profile.edit') }}"
                   class="inline-flex items-center justify-center gap-1.5 py-2 rounded-xl border bg-white hover:bg-gray-100 text-sm font-medium text-gray-700">
                    {!! $ic['profile'] !!} <span>Profile</span>
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="w-full inline-flex items-center justify-center gap-1.5 py-2 rounded-xl bg-red-600 text-white hover:bg-red-700 text-sm font-semibold">
                        {!! $ic['logout'] !!} <span>Logout</span>
                    </button>
                </form>
            </div>
        </div>

        {{-- CLOSE --}}
        <div x-show="!sidebarOpen"
             x-transition.opacity.duration.150ms
             class="py-3 flex flex-col items-center gap-2">
            <div class="w-9 h-9 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-sm">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </div>
            <a href="{{ route('profile.edit') }}" title="Profile"
               class="w-9 h-9 flex items-center justify-center rounded-xl border bg-white hover:bg-gray-100 text-gray-700">
                {!! $ic['profile'] !!}
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" title="Logout"
                        class="w-9 h-9 flex items-center justify-center rounded-xl bg-red-600 text-white hover:bg-red-700">
                    {!! $ic['logout'] !!}
                </button>
            </form>
        </div>

    </div>
</nav>
