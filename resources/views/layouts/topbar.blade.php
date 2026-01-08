<header
    class="bg-white/80 backdrop-blur-md border-b border-gray-200 sticky top-0 z-30 h-16 flex items-center justify-between px-4 sm:px-6 lg:px-8">
    <div class="flex items-center gap-4">
        <!-- Mobile Sidebar Toggle -->
        <button @click="openMobileMenu = !openMobileMenu"
            class="md:hidden text-gray-500 hover:text-gray-700 p-2 rounded-lg hover:bg-gray-100">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        <!-- Current Date/Context (Optional) -->
        <div class="hidden sm:block text-sm text-gray-500">
            {{ now()->format('l, jS F Y') }}
        </div>
    </div>

    <!-- Right Side Utilities -->
    <div class="flex items-center gap-4" x-data="{ 
        searchQuery: '', 
        open: false,
        notificationsOpen: false,
        results: { patients: [], products: [] },
        isLoading: false,
        notifications: [],
        unreadCount: 0,
        
        init() {
            this.fetchNotifications();
            // Optional: Poll every 60 seconds
            setInterval(() => this.fetchNotifications(), 60000);
        },

        fetchNotifications() {
            fetch('/notifications/latest')
                .then(res => res.json())
                .then(data => {
                    this.notifications = data;
                    this.unreadCount = data.length;
                })
                .catch(err => console.error('Error fetching notifications:', err));
        },

        markAsRead(id) {
            fetch(`/notifications/${id}/read`)
                .then(() => {
                    this.notifications = this.notifications.filter(n => n.id !== id);
                    this.unreadCount = Math.max(0, this.unreadCount - 1);
                });
        },

        markAllRead() {
            fetch('/notifications/mark-all', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                }
            }).then(() => {
                this.notifications = [];
                this.unreadCount = 0;
            });
        },

        performSearch() {
            if(this.searchQuery.length < 2) {
                 this.open = false;
                 return;
            }
            this.isLoading = true;
            this.open = true;

            fetch(`/global-search?query=${encodeURIComponent(this.searchQuery)}`)
                .then(res => res.json())
                .then(data => {
                    this.results = data;
                    this.isLoading = false;
                })
                .catch(err => {
                    console.error(err);
                    this.isLoading = false;
                });
        }
    }">
        <!-- Search -->
        <div class="relative hidden lg:block" @click.outside="open = false">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </span>
            <input type="text" x-model="searchQuery" @input.debounce.300ms="performSearch()"
                @focus="if(searchQuery.length >= 2) open = true" placeholder="Global search..."
                class="pl-10 pr-4 py-2 border-none bg-gray-100/50 rounded-full text-sm focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all w-64">

            <!-- Search Results Dropdown -->
            <div x-show="open" x-transition
                class="absolute top-full left-0 mt-2 w-96 bg-white rounded-xl shadow-2xl ring-1 ring-black/5 z-50 overflow-hidden max-h-[80vh] overflow-y-auto"
                style="display: none;">

                <!-- Loading State -->
                <div x-show="isLoading" class="p-4 text-center text-gray-500 text-sm">
                    <svg class="animate-spin h-5 w-5 mx-auto mb-2 text-indigo-600" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                        </circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    Searching...
                </div>

                <!-- Empty State -->
                <template x-if="!isLoading && results.patients.length === 0 && results.products.length === 0">
                    <div class="p-4 text-center text-gray-500 text-sm">
                        No results found for "<span x-text="searchQuery"></span>"
                    </div>
                </template>

                <!-- Patients -->
                <template x-if="results.patients.length > 0">
                    <div class="border-b border-gray-100">
                        <div class="px-4 py-2 bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Patients
                        </div>
                        <template x-for="patient in results.patients" :key="patient.id">
                            <a :href="`/patients/${patient.id}`"
                                class="block px-4 py-3 hover:bg-gray-50 transition-colors group">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xs">
                                        P
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 group-hover:text-indigo-600"
                                            x-text="patient.name"></p>
                                        <p class="text-xs text-gray-500"
                                            x-text="`ID: ${patient.id} • Phone: ${patient.phone || 'N/A'}`">
                                        </p>
                                    </div>
                                </div>
                            </a>
                        </template>
                    </div>
                </template>

                <!-- Products -->
                <template x-if="results.products.length > 0">
                    <div>
                        <div class="px-4 py-2 bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Products
                        </div>
                        <template x-for="product in results.products" :key="product.id">
                            <a :href="`/inventory/${product.id}`"
                                class="block px-4 py-3 hover:bg-gray-50 transition-colors group">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-8 h-8 rounded-full bg-green-100 text-green-600 flex items-center justify-center font-bold text-xs">
                                        Rx
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 group-hover:text-indigo-600"
                                            x-text="product.name"></p>
                                        <p class="text-xs text-gray-500"
                                            x-text="`${product.generic_name || ''} • Stock: ${product.stock}`"></p>
                                    </div>
                                    <div class="ml-auto text-sm font-semibold text-gray-700"
                                        x-text="`GHS ${product.unit_price}`"></div>
                                </div>
                            </a>
                        </template>
                    </div>
                </template>
            </div>
        </div>

        <!-- Notifications Bell -->
        <div class="relative">
            <button @click="notificationsOpen = !notificationsOpen" @click.outside="notificationsOpen = false"
                class="relative p-2 text-gray-400 hover:text-indigo-600 transition-colors rounded-full hover:bg-indigo-50 focus:outline-none">
                <!-- Unread Indicator -->
                <span x-show="unreadCount > 0" x-transition.scale
                    class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full border border-white"></span>
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
            </button>

            <!-- Notifications Dropdown -->
            <div x-show="notificationsOpen" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl ring-1 ring-black ring-opacity-5 z-50 origin-top-right overflow-hidden"
                style="display: none;">
                
                <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                    <h3 class="text-sm font-semibold text-gray-900">Notifications</h3>
                    <button @click="markAllRead()" x-show="notifications.length > 0"
                        class="text-xs text-indigo-600 hover:text-indigo-800 font-medium cursor-pointer focus:outline-none">
                        Mark all as read
                    </button>
                </div>

                <div class="max-h-80 overflow-y-auto custom-scrollbar">
                    <!-- Loading State -->
                    <template x-if="false">
                        <div class="p-4 text-center text-gray-400 text-xs">Loading...</div>
                    </template>

                    <!-- Empty State -->
                    <template x-if="notifications.length === 0">
                        <div class="p-8 text-center flex flex-col items-center justify-center">
                            <div class="bg-gray-100 p-3 rounded-full mb-3">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                            </div>
                            <p class="text-gray-500 text-sm font-medium">No new notifications</p>
                            <p class="text-gray-400 text-xs mt-1">You're all caught up!</p>
                        </div>
                    </template>

                    <!-- List -->
                    <template x-for="notification in notifications" :key="notification.id">
                        <div @click="markAsRead(notification.id)"
                            class="p-4 border-b border-gray-50 hover:bg-gray-50 transition-colors cursor-pointer group">
                            <div class="flex items-start gap-3">
                                <template x-if="notification.data.icon">
                                    <!-- Dynamic Icon if provided -->
                                    <div class="flex-shrink-0">
                                        <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-indigo-100 text-indigo-600">
                                             <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </span>
                                    </div>
                                </template>
                                <template x-if="!notification.data.icon">
                                    <div class="flex-shrink-0">
                                        <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-blue-100 text-blue-600">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                            </svg>
                                        </span>
                                    </div>
                                </template>
                                
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 group-hover:text-indigo-600 transition-colors" x-text="notification.data.title || 'Notification'"></p>
                                    <p class="text-xs text-gray-500 mt-0.5 line-clamp-2" x-text="notification.data.message || notification.data.body || ''"></p>
                                    <p class="text-[10px] text-gray-400 mt-1.5" x-text="new Date(notification.created_at).toLocaleString()"></p>
                                </div>
                                
                                <!-- Unread dot within list -->
                                <div class="w-1.5 h-1.5 rounded-full bg-indigo-600 mt-1.5"></div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</header>