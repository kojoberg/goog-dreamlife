<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-16 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    <x-nav-link :href="route('pos.index')" :active="request()->routeIs('pos.*')">
                        {{ __('POS') }}
                    </x-nav-link>

                    <x-nav-link :href="route('sales.index')" :active="request()->routeIs('sales.*')">
                        {{ __('Sales History') }}
                    </x-nav-link>

                    @if(Auth::user()->role === 'cashier' || Auth::user()->isAdmin())
                        <x-nav-link :href="route('cashier.index')" :active="request()->routeIs('cashier.*')">
                            {{ __('Cashier Dashboard') }}
                        </x-nav-link>
                    @endif

                    @if(Auth::user()->isAdmin() || Auth::user()->isPharmacist())
                        <!-- Inventory Dropdown -->
                        <div class="hidden sm:flex sm:items-center sm:ms-2">
                            <x-dropdown align="right" width="48">
                                <x-slot name="trigger">
                                    <button
                                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                        <div>Inventory</div>
                                        <div class="ms-1">
                                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </button>
                                </x-slot>
                                <x-slot name="content">
                                    <x-dropdown-link :href="route('inventory.create')">
                                        {{ __('Receive Stock') }}
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('procurement.orders.index')">
                                        {{ __('Procurement (POs)') }}
                                    </x-dropdown-link>
                                    <div class="border-t border-gray-100"></div>
                                    <x-dropdown-link :href="route('products.index')">
                                        {{ __('Products') }}
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('categories.index')">
                                        {{ __('Categories') }}
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('suppliers.index')">
                                        {{ __('Suppliers') }}
                                    </x-dropdown-link>
                                </x-slot>
                            </x-dropdown>
                        </div>

                        <!-- Clinical Dropdown -->
                        <div class="hidden sm:flex sm:items-center sm:ms-2">
                            <x-dropdown align="right" width="48">
                                <x-slot name="trigger">
                                    <button
                                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                        <div>Clinical</div>
                                        <div class="ms-1">
                                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </button>
                                </x-slot>
                                <x-slot name="content">
                                    <x-dropdown-link :href="route('prescriptions.index')">
                                        {{ __('Prescriptions') }}
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('patients.index')">
                                        {{ __('Patients') }}
                                    </x-dropdown-link>
                                    <div class="border-t border-gray-100"></div>
                                    <x-dropdown-link :href="route('drug-interactions.index')">
                                        {{ __('Safety Checks') }}
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('analytics.index')">
                                        {{ __('Analytics') }}
                                    </x-dropdown-link>
                                </x-slot>
                            </x-dropdown>
                        </div>
                    @endif

                    <!-- Operations Dropdown -->
                    <div class="hidden sm:flex sm:items-center sm:ms-2">
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button
                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                    <div>Operations</div>
                                    <div class="ms-1">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                <x-dropdown-link :href="route('shifts.create')">
                                    {{ __('My Shift') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('shifts.my_index')">
                                    {{ __('My Shift Reports') }}
                                </x-dropdown-link>
                                @if(Auth::user()->isAdmin())
                                    <x-dropdown-link :href="route('admin.shifts.index')" class="bg-blue-50 border-b">
                                        {{ __('Shift Reports') }}
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('expenses.index')">
                                        {{ __('Expenses') }}
                                    </x-dropdown-link>
                                @endif
                            </x-slot>
                        </x-dropdown>
                    </div>

                    <!-- Admin Dropdown -->
                    @if(Auth::user()->isAdmin())
                        <div class="hidden sm:flex sm:items-center sm:ms-2">
                            <x-dropdown align="right" width="48">
                                <x-slot name="trigger">
                                    <button
                                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                        <div>Admin</div>
                                        <div class="ms-1">
                                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </button>
                                </x-slot>
                                <x-slot name="content">
                                    <x-dropdown-link :href="route('admin.index')" class="bg-gray-50 border-b">
                                        {{ __('Admin Console') }}
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('admin.financials.index')" class="bg-green-50 border-b">
                                        {{ __('Financial Reports') }}
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('admin.crm.index')" class="bg-indigo-50 border-b">
                                        {{ __('CRM & Messaging') }}
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('settings.index')">
                                        {{ __('Settings') }}
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('backups.index')">
                                        {{ __('Backups') }}
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('audit-logs.index')">
                                        {{ __('Audit Logs') }}
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('admin.system-health')">
                                        {{ __('System Health') }}
                                    </x-dropdown-link>
                                </x-slot>
                            </x-dropdown>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Notification Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="80">
                    <x-slot name="trigger">
                        <button
                            class="relative p-1 rounded-full text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <span class="sr-only">View notifications</span>
                            <!-- Bell Icon -->
                            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>

                            @if(Auth::user()->unreadNotifications->count() > 0)
                                <span
                                    class="absolute top-0 right-0 block h-2.5 w-2.5 rounded-full ring-2 ring-white bg-red-600"></span>
                            @endif
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="px-4 py-2 border-b border-gray-100 flex justify-between items-center">
                            <span class="text-xs font-semibold text-gray-500">Notifications</span>
                            @if(Auth::user()->unreadNotifications->count() > 0)
                                <form action="{{ route('notifications.mark-all') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-xs text-blue-600 hover:text-blue-800">Mark all
                                        read</button>
                                </form>
                            @endif
                        </div>

                        <div class="max-h-64 overflow-y-auto">
                            @forelse(Auth::user()->unreadNotifications as $notification)
                                <a href="{{ route('notifications.read', $notification->id) }}"
                                    class="block px-4 py-3 hover:bg-gray-50 transition border-l-4 border-blue-500">
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ $notification->data['message'] ?? 'New Notification' }}
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">{{ $notification->created_at->diffForHumans() }}
                                    </p>
                                </a>
                            @empty
                                <div class="px-4 py-6 text-center text-gray-500 text-sm">
                                    No new notifications
                                </div>
                            @endforelse
                        </div>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Settings Dropdown (User Name) -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <!-- Keep existing user dropdown for logout -->
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); 
                                         if ({{ Auth::user()->hasOpenShift() ? 'true' : 'false' }}) {
                                             if (!confirm('You have an OPEN SHIFT. Are you sure you want to log out without closing it?')) return;
                                         }
                                         this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('pos.index')" :active="request()->routeIs('pos.*')">
                {{ __('POS') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('sales.index')" :active="request()->routeIs('sales.*')">
                {{ __('Sales History') }}
            </x-responsive-nav-link>

            @if(Auth::user()->role === 'cashier' || Auth::user()->isAdmin())
                <x-responsive-nav-link :href="route('cashier.index')" :active="request()->routeIs('cashier.*')">
                    {{ __('Cashier Dashboard') }}
                </x-responsive-nav-link>
            @endif

            @if(Auth::user()->isAdmin() || Auth::user()->isPharmacist())
                <div class="border-t border-gray-200 my-2"></div>
                <div class="px-4 text-xs text-gray-500 uppercase font-bold">Inventory</div>
                <x-responsive-nav-link :href="route('inventory.create')">Receive Stock</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('products.index')">Products</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('categories.index')">Categories</x-responsive-nav-link>

                <div class="border-t border-gray-200 my-2"></div>
                <div class="px-4 text-xs text-gray-500 uppercase font-bold">Clinical</div>
                <x-responsive-nav-link :href="route('prescriptions.index')">Prescriptions</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('patients.index')">Patients</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('drug-interactions.index')">Safety Checks</x-responsive-nav-link>
            @endif

            <div class="border-t border-gray-200 my-2"></div>
            <div class="px-4 text-xs text-gray-500 uppercase font-bold">Operations</div>
            <x-responsive-nav-link :href="route('shifts.create')">My Shift</x-responsive-nav-link>

            @if(Auth::user()->isAdmin())
                <x-responsive-nav-link :href="route('expenses.index')">Expenses</x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); 
                                 if ({{ Auth::user()->hasOpenShift() ? 'true' : 'false' }}) {
                                     if (!confirm('You have an OPEN SHIFT. Are you sure you want to log out without closing it?')) return;
                                 }
                                 this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>