<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Tax Rate Configuration
        </h2>
    </x-slot>

    <div class="py-6" x-data="taxManager()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Add Tax Rate Form -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Add New Tax Rate</h3>
                    <form action="{{ route('admin.tax.rates.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tax Name</label>
                            <input type="text" name="name" required placeholder="e.g., NHIL"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Code</label>
                            <input type="text" name="code" required placeholder="e.g., NHIL" maxlength="20"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm uppercase">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Percentage (%)</label>
                            <input type="number" name="percentage" step="0.01" min="0" max="100" required placeholder="2.50"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Description</label>
                            <input type="text" name="description" placeholder="Optional description"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div class="flex items-end">
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded w-full">
                                Add Tax Rate
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tax Rates List -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-700">Current Tax Rates</h3>
                        <a href="{{ route('admin.tax.reports.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm">
                            View Tax Reports
                        </a>
                    </div>

                    @if($taxRates->isEmpty())
                        <p class="text-gray-500 text-center py-8">No tax rates configured. Add your first tax rate above.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Percentage</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($taxRates as $tax)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">{{ $tax->name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 py-1 bg-gray-100 rounded text-sm font-mono">{{ $tax->code }}</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap font-semibold">{{ number_format($tax->percentage, 2) }}%</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($tax->is_active)
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                                                @else
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600">Disabled</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500">{{ $tax->description ?? '-' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <button type="button" 
                                                    @click="openEditModal({{ $tax->id }}, '{{ $tax->name }}', '{{ $tax->code }}', {{ $tax->percentage }}, '{{ $tax->description ?? '' }}')"
                                                    class="text-blue-600 hover:text-blue-900 mr-3">
                                                    Edit
                                                </button>
                                                <form action="{{ route('admin.tax.rates.toggle', $tax) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-{{ $tax->is_active ? 'yellow' : 'green' }}-600 hover:text-{{ $tax->is_active ? 'yellow' : 'green' }}-900 mr-3">
                                                        {{ $tax->is_active ? 'Disable' : 'Enable' }}
                                                    </button>
                                                </form>
                                                <form action="{{ route('admin.tax.rates.destroy', $tax) }}" method="POST" class="inline" onsubmit="return confirm('Delete this tax rate?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Summary -->
                        <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                            <p class="text-blue-800 font-medium">
                                Total Active Tax Rate: 
                                <span class="text-lg font-bold">{{ number_format($taxRates->where('is_active', true)->sum('percentage'), 2) }}%</span>
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Edit Modal -->
        <div x-show="showEditModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div x-show="showEditModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeEditModal()"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <!-- Modal panel -->
                <div x-show="showEditModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    
                    <form :action="editFormAction" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Edit Tax Rate</h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Tax Name</label>
                                    <input type="text" name="name" x-model="editTax.name" required
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Code</label>
                                    <input type="text" name="code" x-model="editTax.code" required maxlength="20"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm uppercase">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Percentage (%)</label>
                                    <input type="number" name="percentage" x-model="editTax.percentage" step="0.01" min="0" max="100" required
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Description</label>
                                    <input type="text" name="description" x-model="editTax.description"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                                Save Changes
                            </button>
                            <button type="button" @click="closeEditModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function taxManager() {
            return {
                showEditModal: false,
                editTax: {
                    id: null,
                    name: '',
                    code: '',
                    percentage: 0,
                    description: ''
                },
                get editFormAction() {
                    return `/tax/rates/${this.editTax.id}`;
                },
                openEditModal(id, name, code, percentage, description) {
                    this.editTax = { id, name, code, percentage, description };
                    this.showEditModal = true;
                },
                closeEditModal() {
                    this.showEditModal = false;
                    this.editTax = { id: null, name: '', code: '', percentage: 0, description: '' };
                }
            }
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
    </style>
</x-app-layout>
