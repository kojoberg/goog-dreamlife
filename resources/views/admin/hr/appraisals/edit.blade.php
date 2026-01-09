<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Appraisal') }}: {{ $appraisal->user->name }}
            </h2>
            <div class="text-sm">
                Status: 
                <span class="px-2 py-1 rounded-full text-xs font-bold uppercase 
                    {{ $appraisal->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                    {{ str_replace('_', ' ', $appraisal->status) }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('info'))
                <div class="mb-4 bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4" role="alert">
                    <p>{{ session('info') }}</p>
                </div>
            @endif

            <form action="{{ route('admin.hr.appraisals.update', $appraisal) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Info Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                        <div><span class="text-gray-500">Staff:</span> <span class="font-semibold block">{{ $appraisal->user->name }}</span></div>
                        <div><span class="text-gray-500">Role:</span> <span class="font-semibold block">{{ ucfirst($appraisal->user->role) }}</span></div>
                        <div><span class="text-gray-500">Period:</span> <span class="font-semibold block">{{ $appraisal->period_month }}</span></div>
                        <div><span class="text-gray-500">Reviewer:</span> <span class="font-semibold block">{{ $appraisal->reviewer->name ?? 'N/A' }}</span></div>
                    </div>
                </div>

                @php
                    $isEmployee = auth()->id() === $appraisal->user_id;
                    $isManager = (auth()->id() === $appraisal->reviewer_id) || auth()->user()->isAdmin();
                    $canEditSelf = $isEmployee && ($appraisal->status === 'draft' || $appraisal->status === 'pending_employee');
                    $canEditManager = $isManager && $appraisal->status !== 'completed';
                @endphp

                @if($groupedKpis->count() > 0)
                    @foreach($groupedKpis as $category => $kpis)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                            <h3 class="font-bold text-lg mb-4 text-indigo-700 border-b pb-2">{{ $category ?: 'General KPIs' }}</h3>
                            
                            <div class="space-y-6">
                                @foreach($kpis as $kpi)
                                    @php
                                        $detail = $appraisal->details->where('kpi_id', $kpi->id)->first();
                                        $selfScore = $detail ? $detail->self_score : '';
                                        $selfComment = $detail ? $detail->self_comments : '';
                                        $managerScore = $detail ? $detail->score : ''; // 'score' column is manager score
                                        $managerComment = $detail ? $detail->comments : '';
                                    @endphp
                                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                        <div class="flex justify-between items-start mb-2">
                                            <div>
                                                <h4 class="font-medium text-gray-900">{{ $kpi->name }} 
                                                    <span class="text-xs text-gray-500 ml-2">(Weight: {{ $kpi->weight }})</span>
                                                </h4>
                                                <p class="text-xs text-gray-500">{{ $kpi->description }}</p>
                                            </div>
                                            <div class="text-right">
                                                <span class="text-xs font-semibold bg-gray-200 px-2 py-1 rounded text-gray-700 uppercase">{{ $kpi->type }}</span>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                                            <!-- Employee Section -->
                                            <div class="p-3 bg-blue-50 rounded border border-blue-100">
                                                <h5 class="text-xs font-bold text-blue-800 uppercase mb-2">Self Assessment</h5>
                                                <div class="grid grid-cols-3 gap-2">
                                                    <div class="col-span-1">
                                                        <label class="block text-xs text-gray-600">Score (1-5)</label>
                                                        <select
                                                            name="{{ $canEditSelf ? "scores[{$kpi->id}]" : '' }}" 
                                                            {{ $canEditSelf ? '' : 'disabled' }}
                                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                                            <option value="">Select Score</option>
                                                            <option value="1" {{ $selfScore == 1 ? 'selected' : '' }}>1 - Poor</option>
                                                            <option value="2" {{ $selfScore == 2 ? 'selected' : '' }}>2 - Fair</option>
                                                            <option value="3" {{ $selfScore == 3 ? 'selected' : '' }}>3 - Good</option>
                                                            <option value="4" {{ $selfScore == 4 ? 'selected' : '' }}>4 - Very Good</option>
                                                            <option value="5" {{ $selfScore == 5 ? 'selected' : '' }}>5 - Excellent</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-span-2">
                                                        <label class="block text-xs text-gray-600">Comments</label>
                                                        <input type="text" 
                                                            name="{{ $canEditSelf ? "comments[{$kpi->id}]" : '' }}" 
                                                            value="{{ $selfComment }}" 
                                                            {{ $canEditSelf ? '' : 'disabled' }}
                                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                                            placeholder="Self reflection...">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Manager Section -->
                                            <div class="p-3 {{ $canEditManager ? 'bg-green-50 border-green-100' : 'bg-gray-100 border-gray-200' }} rounded border">
                                                <h5 class="text-xs font-bold text-green-800 uppercase mb-2">Manager Review</h5>
                                                <div class="grid grid-cols-3 gap-2">
                                                    <div class="col-span-1">
                                                        <label class="block text-xs text-gray-600">Score (1-5)</label>
                                                        <select
                                                            name="{{ $canEditManager ? "scores[{$kpi->id}]" : '' }}" 
                                                            {{ $canEditManager ? '' : 'disabled' }}
                                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                                                            <option value="">Select Score</option>
                                                            <option value="1" {{ $managerScore == 1 ? 'selected' : '' }}>1 - Poor</option>
                                                            <option value="2" {{ $managerScore == 2 ? 'selected' : '' }}>2 - Fair</option>
                                                            <option value="3" {{ $managerScore == 3 ? 'selected' : '' }}>3 - Good</option>
                                                            <option value="4" {{ $managerScore == 4 ? 'selected' : '' }}>4 - Very Good</option>
                                                            <option value="5" {{ $managerScore == 5 ? 'selected' : '' }}>5 - Excellent</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-span-2">
                                                        <label class="block text-xs text-gray-600">Comments</label>
                                                        <input type="text" 
                                                            name="{{ $canEditManager ? "comments[{$kpi->id}]" : '' }}" 
                                                            value="{{ $managerComment }}" 
                                                            {{ $canEditManager ? '' : 'disabled' }}
                                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm"
                                                            placeholder="Manager feedback...">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="p-4 bg-yellow-50 text-yellow-700 rounded-md mb-6">
                        No KPIs found for this user role. Please define KPIs in HR Settings first.
                    </div>
                @endif

                <!-- Overall Actions -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="mb-4">
                        <label class="block font-medium text-gray-700 mb-2">Overall Comments</label>
                        <textarea name="overall_comment" rows="3" {{ (!$canEditSelf && !$canEditManager) ? 'disabled' : '' }}
                            class="block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ $appraisal->comments }}</textarea>
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        @if($canEditSelf)
                            <x-secondary-button type="submit">Save Draft</x-secondary-button>
                            <button type="submit" name="submit_appraisal" value="1" 
                                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Submit Self-Assessment
                            </button>
                        @elseif($canEditManager)
                            <x-secondary-button type="submit">Save Progress</x-secondary-button>
                            <button type="submit" name="finalize_appraisal" value="1" onclick="return confirm('Are you sure you want to finalize this appraisal? This cannot be undone.')"
                                class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Finalize & Close
                            </button>
                        @endif
                    </div>
                </div>

            </form>
        </div>
    </div>
</x-app-layout>