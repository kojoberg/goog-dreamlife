<x-app-layout>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-10">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Record Expense</h1>
                <p class="text-slate-500 text-sm mt-1">Log a new business expense.</p>
            </div>
            <a href="{{ route('expenses.index') }}"
                class="bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 font-semibold py-2 px-4 rounded-lg shadow-sm transition">
                &larr; Back to List
            </a>
        </div>

        <x-card class="max-w-2xl mx-auto">
            <form action="{{ route('expenses.store') }}" method="POST" class="space-y-6">
                @csrf

                <x-form-input name="title" label="Expense Title" placeholder="e.g. Monthly Rent" required autofocus />

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-form-input name="amount" label="Amount (GHS)" type="number" step="0.01" required />

                    <x-form-input name="date" label="Date" type="date" value="{{ date('Y-m-d') }}" required />
                </div>

                <x-form-select name="category" label="Category">
                    <option value="Current Expenses">Current Expenses</option>
                    <option value="Rent">Rent</option>
                    <option value="Utilities">Utilities (Light/Water)</option>
                    <option value="Salaries">Salaries</option>
                    <option value="Maintenance">Maintenance</option>
                    <option value="Transportation">Transportation</option>
                    <option value="Inventory">Inventory Purchase (Direct)</option>
                    <option value="Other">Other</option>
                </x-form-select>

                @if(isset($branches) && $branches)
                    <!-- Branch Selector (Super Admin Only) -->
                    <div class="bg-indigo-50 p-4 rounded-lg border border-indigo-200">
                        <label for="branch_id" class="block text-indigo-700 text-sm font-semibold mb-2">
                            Assign to Branch
                        </label>
                        <select name="branch_id" id="branch_id"
                            class="w-full border border-indigo-300 rounded-lg py-2.5 px-3 focus:ring-2 focus:ring-indigo-500">
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ $branch->is_main ? 'selected' : '' }}>
                                    {{ $branch->name }} {{ $branch->is_main ? '(Main)' : '' }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-indigo-600 mt-1">As Super Admin, you can assign expenses to any branch.</p>
                    </div>
                @endif

                <x-form-textarea name="description" label="Description / Notes" rows="3" />

                <div class="flex justify-end pt-2">
                    <x-primary-button>
                        Save Expense
                    </x-primary-button>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>