<x-app-layout>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-10">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Add Category</h1>
                <p class="text-slate-500 text-sm mt-1">Create a new product category.</p>
            </div>
            <a href="{{ route('categories.index') }}"
                class="bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 font-semibold py-2 px-4 rounded-lg shadow-sm transition">
                &larr; Back to List
            </a>
        </div>

        <x-card class="max-w-md mx-auto">
            <form action="{{ route('categories.store') }}" method="POST" class="space-y-6">
                @csrf

                <x-form-input name="name" label="Category Name" placeholder="e.g. Antibiotics" required autofocus />

                <div class="flex justify-end pt-2">
                    <x-primary-button>
                        Save Category
                    </x-primary-button>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>