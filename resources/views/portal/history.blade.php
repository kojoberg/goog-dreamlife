<x-portal-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Medical History') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-card>
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Prescription History
                    </h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">
                        Past medications dispensed to you.
                    </p>
                </div>

                @if($prescriptions->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Date</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Doctor</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Medications</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($prescriptions as $prescription)
                                    <tr class="hover:bg-gray-50 cursor-pointer" onclick="window.location='{{ route('prescriptions.show', $prescription->id) }}'">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-indigo-600 font-medium hover:text-indigo-900">
                                            {{ $prescription->created_at->format('M d, Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $prescription->doctor->name ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            <ul class="list-disc pl-4">
                                                @if($prescription->medications && is_array($prescription->medications))
                                                    @foreach(array_slice($prescription->medications, 0, 3) as $med)
                                                        <li>{{ $med['name'] ?? 'Medication' }} {{ isset($med['quantity']) ? '('.$med['quantity'].')' : '' }}</li>
                                                    @endforeach
                                                    @if(count($prescription->medications) > 3)
                                                        <li class="text-gray-400">+{{ count($prescription->medications) - 3 }} more...</li>
                                                    @endif
                                                @elseif($prescription->items && $prescription->items->count() > 0)
                                                    @foreach($prescription->items->take(3) as $item)
                                                        <li>{{ $item->product->name ?? 'Item' }} ({{ $item->quantity }})</li>
                                                    @endforeach
                                                    @if($prescription->items->count() > 3)
                                                        <li class="text-gray-400">+{{ $prescription->items->count() - 3 }} more...</li>
                                                    @endif
                                                @else
                                                    <li class="text-gray-400">No medications</li>
                                                @endif
                                            </ul>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                        {{ $prescription->status === 'dispensed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                {{ ucfirst($prescription->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4 px-4 py-3 border-t border-gray-200 sm:px-6">
                        {{ $prescriptions->links() }}
                    </div>
                @else
                    <div class="p-6 text-center text-gray-500">
                        No prescriptions found.
                    </div>
                @endif
            </x-card>
        </div>
    </div>
</x-portal-layout>