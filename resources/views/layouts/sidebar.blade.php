<aside :class="sidebarCollapsed ? 'w-20' : 'w-64'"
    class="bg-slate-850 text-white flex flex-col transition-all duration-300 z-20 hidden md:flex shrink-0 h-screen fixed">

    <!-- Logo Area -->
    <div class="h-16 flex items-center bg-slate-900 shadow-md transition-all duration-300 overflow-hidden"
        :class="sidebarCollapsed ? 'px-0 justify-center' : 'px-6'">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 whitespace-nowrap">
            @if(isset($settings) && $settings->logo_path)
                <img src="{{ Storage::disk('public')->url($settings->logo_path) }}" alt="Logo"
                    class="h-8 w-auto object-contain">
                <span x-show="!sidebarCollapsed"
                    class="font-bold text-lg tracking-wide truncate transition-opacity duration-300">
                    {{ $settings->business_name ?? 'DREAM LIFE' }}
                </span>
            @else
                <div class="bg-indigo-500 p-1.5 rounded-lg shrink-0">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                    </svg>
                </div>
                <span x-show="!sidebarCollapsed" class="font-bold text-lg tracking-wide transition-opacity duration-300">
                    DREAM<span class="text-indigo-400">LIFE</span>
                </span>
            @endif
        </a>
    </div>

    <!-- Toggle Button -->
    <button @click="toggleSidebar()"
        class="absolute top-16 -right-3 bg-indigo-600 text-white p-1 rounded-full shadow-lg hover:bg-indigo-700 transition-colors z-50 border border-slate-700 focus:outline-none hidden md:flex">
        <svg :class="sidebarCollapsed ? 'rotate-180' : ''" class="w-4 h-4 transition-transform duration-300" fill="none"
            stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
    </button>

    <!-- Navigation Links -->
    <nav class="flex-1 overflow-y-auto py-4 space-y-1 custom-scrollbar" :class="sidebarCollapsed ? 'px-2' : 'px-3'">

        <!-- Dashboard -->
        <a href="{{ route('dashboard') }}" :class="sidebarCollapsed ? 'justify-center px-0' : 'px-3'"
            class="{{ request()->routeIs('dashboard') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }} flex items-center gap-3 py-2.5 rounded-lg transition-all group overflow-hidden">
            <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}"
                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
            </svg>
            <span x-show="!sidebarCollapsed"
                class="font-medium whitespace-nowrap transition-opacity duration-200">Dashboard</span>
        </a>

        <!-- Clinical Section -->
        <div x-data="{ open: {{ request()->is('patients*') || request()->is('setup/prescriptions*') || request()->is('safety*') ? 'true' : 'false' }} }"
            class="group relative">
            <button @click="if(sidebarCollapsed) { toggleSidebar(); open = true; } else { open = !open; }"
                :class="sidebarCollapsed ? 'justify-center px-0' : 'px-3'"
                class="w-full flex items-center justify-between py-2.5 text-slate-300 hover:bg-slate-800 hover:text-white rounded-lg transition-colors group">
                <div class="flex items-center overflow-hidden" :class="sidebarCollapsed ? '' : 'gap-3'">
                    <svg class="w-5 h-5 shrink-0 text-slate-400 group-hover:text-white" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                    </svg>
                    <span x-show="!sidebarCollapsed"
                        class="font-medium whitespace-nowrap transition-opacity duration-200">Clinical</span>
                </div>
                <svg x-show="!sidebarCollapsed" :class="{'rotate-90': open}"
                    class="w-4 h-4 text-slate-500 transition-transform duration-200" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>
            <div x-show="open && !sidebarCollapsed" x-transition class="mt-1 pl-10 space-y-1 overflow-hidden">
                <a href="{{ route('patients.index') }}"
                    class="{{ request()->routeIs('patients*') ? 'text-indigo-400' : 'text-slate-400 hover:text-white' }} block py-1.5 text-sm whitespace-nowrap">Patients</a>
                <a href="{{ route('prescriptions.create') }}"
                    class="{{ request()->routeIs('prescriptions*') ? 'text-indigo-400' : 'text-slate-400 hover:text-white' }} block py-1.5 text-sm whitespace-nowrap">Prescriptions</a>
                <a href="{{ route('admin.safety.index') }}"
                    class="{{ request()->routeIs('admin.safety*') ? 'text-indigo-400' : 'text-slate-400 hover:text-white' }} block py-1.5 text-sm whitespace-nowrap">Safety
                    Checks</a>
            </div>
        </div>

        <!-- POS / Sales -->
        <a href="{{ route('pos.index') }}" :class="sidebarCollapsed ? 'justify-center px-0' : 'px-3'"
            class="{{ request()->routeIs('pos.index') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }} flex items-center gap-3 py-2.5 rounded-lg transition-all group overflow-hidden">
            <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('pos.index') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}"
                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span x-show="!sidebarCollapsed" class="font-medium whitespace-nowrap transition-opacity duration-200">Point
                of Sale</span>
        </a>

        <!-- Inventory -->
        <div x-data="{ open: {{ request()->is('products*') || request()->is('inventory*') ? 'true' : 'false' }} }"
            class="group relative">
            <button @click="if(sidebarCollapsed) { toggleSidebar(); open = true; } else { open = !open; }"
                :class="sidebarCollapsed ? 'justify-center px-0' : 'px-3'"
                class="w-full flex items-center justify-between py-2.5 text-slate-300 hover:bg-slate-800 hover:text-white rounded-lg transition-colors group">
                <div class="flex items-center overflow-hidden" :class="sidebarCollapsed ? '' : 'gap-3'">
                    <svg class="w-5 h-5 shrink-0 text-slate-400 group-hover:text-white" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <span x-show="!sidebarCollapsed"
                        class="font-medium whitespace-nowrap transition-opacity duration-200">Inventory</span>
                </div>
                <svg x-show="!sidebarCollapsed" :class="{'rotate-90': open}"
                    class="w-4 h-4 text-slate-500 transition-transform duration-200" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>
            <div x-show="open && !sidebarCollapsed" x-transition class="mt-1 pl-10 space-y-1 overflow-hidden">
                <a href="{{ route('products.index') }}"
                    class="{{ request()->routeIs('products*') ? 'text-indigo-400' : 'text-slate-400 hover:text-white' }} block py-1.5 text-sm whitespace-nowrap">Products</a>
                <a href="{{ route('inventory.index') }}"
                    class="{{ request()->routeIs('inventory*') ? 'text-indigo-400' : 'text-slate-400 hover:text-white' }} block py-1.5 text-sm whitespace-nowrap">Stock
                    Levels</a>
            </div>
        </div>

        <!-- HR Management -->
        <div x-data="{ open: {{ request()->is('admin/hr*') ? 'true' : 'false' }} }" class="group relative">
            <button @click="if(sidebarCollapsed) { toggleSidebar(); open = true; } else { open = !open; }"
                :class="sidebarCollapsed ? 'justify-center px-0' : 'px-3'"
                class="w-full flex items-center justify-between py-2.5 text-slate-300 hover:bg-slate-800 hover:text-white rounded-lg transition-colors group">
                <div class="flex items-center overflow-hidden" :class="sidebarCollapsed ? '' : 'gap-3'">
                    <svg class="w-5 h-5 shrink-0 text-slate-400 group-hover:text-white" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span x-show="!sidebarCollapsed"
                        class="font-medium whitespace-nowrap transition-opacity duration-200">HR Management</span>
                </div>
                <svg x-show="!sidebarCollapsed" :class="{'rotate-90': open}"
                    class="w-4 h-4 text-slate-500 transition-transform duration-200" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>
            <div x-show="open && !sidebarCollapsed" x-transition class="mt-1 pl-10 space-y-1 overflow-hidden">
                <a href="{{ route('admin.hr.dashboard') }}"
                    class="{{ request()->routeIs('admin.hr.dashboard') ? 'text-indigo-400' : 'text-slate-400 hover:text-white' }} block py-1.5 text-sm whitespace-nowrap">Overview</a>
                <a href="{{ route('admin.hr.employees.index') }}"
                    class="{{ request()->routeIs('admin.hr.employees*') ? 'text-indigo-400' : 'text-slate-400 hover:text-white' }} block py-1.5 text-sm whitespace-nowrap">Employees</a>
                <a href="{{ route('admin.hr.payroll.index') }}"
                    class="{{ request()->routeIs('admin.hr.payroll*') ? 'text-indigo-400' : 'text-slate-400 hover:text-white' }} block py-1.5 text-sm whitespace-nowrap">Payroll</a>
                <a href="{{ route('admin.hr.appraisals.index') }}"
                    class="{{ request()->routeIs('admin.hr.appraisals*') ? 'text-indigo-400' : 'text-slate-400 hover:text-white' }} block py-1.5 text-sm whitespace-nowrap">Appraisals</a>
                <a href="{{ route('admin.hr.kpis.index') }}"
                    class="{{ request()->routeIs('admin.hr.kpis*') ? 'text-indigo-400' : 'text-slate-400 hover:text-white' }} block py-1.5 text-sm whitespace-nowrap">KPIs</a>
            </div>
        </div>

        <!-- Reports -->
        <div x-data="{ open: {{ request()->is('admin/financials*') || request()->is('admin/tax*') ? 'true' : 'false' }} }"
            class="group relative">
            <button @click="if(sidebarCollapsed) { toggleSidebar(); open = true; } else { open = !open; }"
                :class="sidebarCollapsed ? 'justify-center px-0' : 'px-3'"
                class="w-full flex items-center justify-between py-2.5 text-slate-300 hover:bg-slate-800 hover:text-white rounded-lg transition-colors group">
                <div class="flex items-center overflow-hidden" :class="sidebarCollapsed ? '' : 'gap-3'">
                    <svg class="w-5 h-5 shrink-0 text-slate-400 group-hover:text-white" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span x-show="!sidebarCollapsed"
                        class="font-medium whitespace-nowrap transition-opacity duration-200">Reports</span>
                </div>
                <svg x-show="!sidebarCollapsed" :class="{'rotate-90': open}"
                    class="w-4 h-4 text-slate-500 transition-transform duration-200" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>
            <div x-show="open && !sidebarCollapsed" x-transition class="mt-1 pl-10 space-y-1 overflow-hidden">
                <a href="{{ route('admin.financials.sales') }}"
                    class="{{ request()->routeIs('admin.financials.sales') ? 'text-indigo-400' : 'text-slate-400 hover:text-white' }} block py-1.5 text-sm whitespace-nowrap">Sales
                    Report</a>
                <a href="{{ route('admin.tax.reports.index') }}"
                    class="{{ request()->routeIs('admin.tax.reports.index') ? 'text-indigo-400' : 'text-slate-400 hover:text-white' }} block py-1.5 text-sm whitespace-nowrap">Tax
                    Report</a>
                <a href="{{ route('admin.tax.rates.index') }}"
                    class="{{ request()->routeIs('admin.tax.rates.index') ? 'text-indigo-400' : 'text-slate-400 hover:text-white' }} block py-1.5 text-sm whitespace-nowrap">Tax
                    Settings</a>
            </div>
        </div>

        <!-- Settings -->
        <a href="{{ route('settings.index') }}" :class="sidebarCollapsed ? 'justify-center px-0' : 'px-3'"
            class="{{ request()->routeIs('settings*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }} flex items-center gap-3 py-2.5 rounded-lg transition-all group mt-6 overflow-hidden">
            <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('settings*') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}"
                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <span x-show="!sidebarCollapsed"
                class="font-medium whitespace-nowrap transition-opacity duration-200">System Settings</span>
        </a>

    </nav>

    <!-- Footer / User Info -->
    <div class="bg-slate-900 px-4 py-4 border-t border-slate-700 overflow-hidden"
        :class="sidebarCollapsed ? 'px-2' : 'px-4'">
        <div class="flex items-center gap-3" :class="sidebarCollapsed ? 'justify-center' : ''">
            <div class="w-8 h-8 rounded-full bg-indigo-500 flex items-center justify-center text-sm font-bold shrink-0">
                {{ substr(auth()->user()->name, 0, 1) }}
            </div>
            <div x-show="!sidebarCollapsed" class="flex-1 min-w-0 transition-opacity duration-200">
                <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name }}</p>
                <p class="text-xs text-slate-400 truncate capitalize">{{ auth()->user()->role }}</p>
            </div>
            <form x-show="!sidebarCollapsed" method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-slate-400 hover:text-white transition-colors p-1">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                </button>
            </form>
        </div>
    </div>
</aside>