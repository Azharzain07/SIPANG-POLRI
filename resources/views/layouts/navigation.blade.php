<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center">
                        <img src="{{ asset('images/logo_bagren.png') }}" alt="Logo Bagren" class="h-10 w-auto">
                        <div class="ml-3 flex flex-col">
                            <span class="text-lg font-bold text-gray-800 whitespace-nowrap">Bagren Polres Garut</span>
                            <span class="text-xs text-gray-500">Sistem Informasi Perencanaan Anggaran</span>
                        </div>
                    </a>
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                @auth
                    <div class="ms-3 relative">
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition">
                                    <div>Layanan</div>
                                    <div class="ms-1"><svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg></div>
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                @if (in_array(auth()->user()->role, ['admin', 'ppk', 'npwp']))
                                    <x-dropdown-link :href="route('admin.pengajuan.index')">
                                        {{ __('Review Pengajuan') }}
                                    </x-dropdown-link>
                                @else
                                    <x-dropdown-link :href="route('pengajuan.create')">
                                        {{ __('Buat Pengajuan') }}
                                    </x-dropdown-link>
                                @endif
                            </x-slot>
                        </x-dropdown>
                    </div>
                    

                    <div class="ms-3 relative">
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&color=7F9CF5&background=EBF4FF" alt="Profil" class="h-8 w-8 rounded-full object-cover mr-2">
                                    <div>{{ Auth::user()->name }}</div>
                                    <div class="ms-1"><svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg></div>
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                @if(auth()->user()->role == 'admin')
                                    <x-dropdown-link :href="route('categories.index')">
                                        {{ __('Manajemen Nama Polsek') }}
                                    </x-dropdown-link>
                                @else
                                    <x-dropdown-link :href="route('pengajuan.index')">
                                        {{ __('Riwayat Pengajuan Saya') }}
                                    </x-dropdown-link>
                                @endif
                                <hr class="my-1 border-gray-100"/>
                                <x-dropdown-link :href="route('profile.edit')">
                                    {{ __('Profil Saya') }}
                                </x-dropdown-link>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                        {{ __('Log Out') }}
                                    </x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    </div>
                @else
                    {{-- Tombol Login untuk tamu --}}
                    <div class="ms-3">
                        <a href="{{ route('login') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500">
                            Login
                        </a>
                    </div>
                @endauth
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        @auth
            <div class="pt-2 pb-3 space-y-1">
                {{-- Menambahkan link dashboard di mobile --}}
                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>

                @if(in_array(auth()->user()->role, ['admin', 'ppk', 'npwp']))
                    <x-responsive-nav-link :href="route('admin.pengajuan.index')" :active="request()->routeIs('admin.pengajuan.index')">
                        {{ __('Review Pengajuan') }}
                    </x-responsive-nav-link>
                    @if(auth()->user()->role == 'admin')
                        <x-responsive-nav-link :href="route('categories.index')" :active="request()->routeIs('categories.index')">
                            {{ __('Manajemen Bagian') }}
                        </x-responsive-nav-link>
                    @endif
                @else
                     <x-responsive-nav-link :href="route('pengajuan.create')" :active="request()->routeIs('pengajuan.create')">
                        {{ __('Buat Pengajuan') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('pengajuan.index')" :active="request()->routeIs('pengajuan.index')">
                        {{ __('Riwayat Pengajuan') }}
                    </x-responsive-nav-link>
                @endif
            </div>

            <div class="pt-4 pb-1 border-t border-gray-200">
                <div class="px-4">
                    <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                </div>
                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('profile.edit')">
                        {{ __('Profil Saya') }}
                    </x-responsive-nav-link>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')"
                                onclick="event.preventDefault();
                                            this.closest('form').submit();">
                            {{ __('Log Out') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            </div>
        @else
            {{-- Tampilan mobile untuk tamu --}}
            <div class="pt-2 pb-3 space-y-1">
                <x-responsive-nav-link :href="route('login')">
                    {{ __('Login') }}
                </x-responsive-nav-link>
            </div>
        @endauth
    </div>
</nav>