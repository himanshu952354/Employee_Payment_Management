@extends('layouts.app')

@section('title', 'Employee Directory - PayFlow')

@section('content')
<div class="space-y-8">
    <!-- Header Block -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black uppercase tracking-tight text-black">Employee Directory</h1>
            <p class="text-xs text-slate-600 mt-1.5 font-bold uppercase tracking-wider">Manage personnel records, payout parameters, and general profiles.</p>
        </div>
        <button onclick="toggleModal('add-employee-modal', true)"
            class="flex items-center gap-2 px-5 py-3 rounded-none bg-black hover:bg-neutral-800 font-extrabold text-xs uppercase tracking-wider text-white border border-black shadow-[3px_3px_0px_0px_#000] transition-all active:translate-x-[2px] active:translate-y-[2px] active:shadow-none">
            <i class="fa-solid fa-user-plus text-xs"></i> Add New Employee
        </button>
    </div>

    <!-- Filters and Searches Bar -->
    <div class="bg-white border border-black rounded-none p-5 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
        <form action="{{ route('employees.index') }}" method="GET" class="flex flex-col lg:flex-row gap-4 items-center justify-between">
            <!-- Search field -->
            <div class="relative w-full lg:w-96">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-black text-sm">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </span>
                <input type="text" name="search" value="{{ request('search') }}"
                    class="block w-full rounded-none border border-black bg-[#F4ECE6] py-2.5 pl-9 pr-3 text-black placeholder:text-slate-500 focus:ring-0 focus:border-black text-xs transition-all"
                    placeholder="Search by name, email, employee ID...">
            </div>

            <div class="flex flex-wrap items-center gap-4 w-full lg:w-auto">
                <!-- Department filter -->
                <select name="department"
                    class="rounded-none border border-black bg-[#F4ECE6] py-2.5 px-3.5 text-black focus:ring-0 focus:border-black text-xs font-bold uppercase tracking-wider transition-all">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept }}" {{ request('department') == $dept ? 'selected' : '' }}>{{ $dept }}</option>
                    @endforeach
                </select>

                <!-- Status filter -->
                <select name="status"
                    class="rounded-none border border-black bg-[#F4ECE6] py-2.5 px-3.5 text-black focus:ring-0 focus:border-black text-xs font-bold uppercase tracking-wider transition-all">
                    <option value="">All Statuses</option>
                    <option value="Active" {{ request('status') == 'Active' ? 'selected' : '' }}>Active</option>
                    <option value="Inactive" {{ request('status') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                </select>

                <div class="flex items-center gap-2 ml-auto lg:ml-0">
                    <button type="submit"
                        class="px-5 py-2.5 rounded-none bg-black hover:bg-neutral-800 text-white font-extrabold text-xs uppercase tracking-wider border border-black transition-colors">
                        Apply Filters
                    </button>
                    @if(request()->anyFilled(['search', 'department', 'status']))
                        <a href="{{ route('employees.index') }}"
                            class="px-5 py-2.5 rounded-none bg-[#F4ECE6] hover:bg-slate-200 text-black font-extrabold text-xs uppercase tracking-wider border border-black transition-colors">
                            Clear
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Employee Listing Table Card -->
    <div class="bg-white border border-black rounded-none p-6 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm border-collapse">
                <thead>
                    <tr class="text-slate-400 text-[10px] font-black uppercase tracking-wider border-b border-black pb-3">
                        <th class="py-3.5 px-4 font-bold text-black">Employee ID</th>
                        <th class="py-3.5 px-4 font-bold text-black">Full Name</th>
                        <th class="py-3.5 px-4 font-bold text-black">Department</th>
                        <th class="py-3.5 px-4 font-bold text-black">Base Salary</th>
                        <th class="py-3.5 px-4 font-bold text-black">Join Date</th>
                        <th class="py-3.5 px-4 font-bold text-black" title="Click any employee status below to instantly toggle it">Status <i class="fa-solid fa-circle-info text-[9px] text-slate-450 ml-0.5"></i></th>
                        <th class="py-3.5 px-4 font-bold text-black text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black/10">
                    @forelse($employees as $emp)
                        <tr class="hover:bg-slate-50 transition-colors duration-150">
                            <td class="py-4.5 px-4 font-mono text-xs font-bold text-black">
                                {{ $emp->employee_id }}
                            </td>
                            <td class="py-4.5 px-4">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-9 h-9 rounded-none bg-black text-white border border-black flex items-center justify-center font-bold text-xs uppercase shadow-sm">
                                        {{ substr($emp->name, 0, 2) }}
                                    </div>
                                    <div>
                                        <p class="font-extrabold text-black text-xs">{{ $emp->name }}</p>
                                        <p class="text-[9px] text-slate-500 font-bold uppercase tracking-wider mt-0.5">{{ $emp->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4.5 px-4">
                                <span class="inline-flex items-center text-[9px] font-black uppercase tracking-wider px-2.5 py-0.5 rounded-none border border-black bg-[#F4ECE6] text-black">
                                    {{ $emp->department }}
                                </span>
                            </td>
                            <td class="py-4.5 px-4 text-xs font-black text-black">
                                {{ auth()->user()->currency_symbol }}{{ number_format($emp->salary, 2) }} <span class="text-[9px] text-slate-400 font-bold uppercase tracking-wider">/mo</span>
                            </td>
                            <td class="py-4.5 px-4 text-xs text-slate-700 font-bold">
                                {{ $emp->join_date ? $emp->join_date->format('M d, Y') : 'N/A' }}
                            </td>
                            <td class="py-4.5 px-4">
                                <form action="{{ route('employees.toggle-status', $emp->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    @if($emp->status === 'Active')
                                        <button type="submit" title="Click to set Inactive"
                                            class="inline-flex items-center gap-1.5 text-[9px] font-black uppercase tracking-wider text-emerald-800 bg-emerald-50 hover:bg-emerald-100 border border-emerald-250 px-2.5 py-0.5 rounded-none shadow-[1px_1px_0px_0px_rgba(0,0,0,1)] active:translate-x-[0.5px] active:translate-y-[0.5px] active:shadow-none transition-all cursor-pointer">
                                            <span class="w-1.5 h-1.5 bg-emerald-500 border border-black inline-block"></span> Active
                                        </button>
                                    @else
                                        <button type="submit" title="Click to set Active"
                                            class="inline-flex items-center gap-1.5 text-[9px] font-black uppercase tracking-wider text-slate-800 bg-slate-100 hover:bg-slate-200 border border-black px-2.5 py-0.5 rounded-none shadow-[1px_1px_0px_0px_rgba(0,0,0,1)] active:translate-x-[0.5px] active:translate-y-[0.5px] active:shadow-none transition-all cursor-pointer">
                                            <span class="w-1.5 h-1.5 bg-slate-400 border border-black inline-block"></span> Inactive
                                        </button>
                                    @endif
                                </form>
                            </td>
                            <td class="py-4.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('employees.show', $emp->id) }}"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-none bg-white border border-black hover:bg-slate-50 text-black font-extrabold text-xs uppercase tracking-wider shadow-[2px_2px_0px_0px_#000] active:translate-x-[1px] active:translate-y-[1px] active:shadow-none transition-all"
                                        title="View employee dossier">
                                        <i class="fa-solid fa-id-card text-xs"></i> Dossier
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-xs text-slate-400 font-bold uppercase tracking-wider">
                                <i class="fa-solid fa-users-slash text-4xl text-slate-300 mb-3 block"></i>
                                No active employee records matched your filter queries.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($employees->hasPages())
            <div class="mt-6 border-t border-black/10 pt-4 flex justify-between items-center text-xs text-slate-500 font-extrabold uppercase tracking-wider">
                <div>
                    Showing {{ $employees->firstItem() }} to {{ $employees->lastItem() }} of {{ $employees->total() }} entries
                </div>
                <div class="flex items-center gap-2">
                    @if($employees->onFirstPage())
                        <span class="px-3.5 py-2 bg-slate-150 text-slate-400 rounded-none border border-black font-extrabold shadow-sm cursor-not-allowed select-none">Prev</span>
                    @else
                        <a href="{{ $employees->previousPageUrl() }}" class="px-3.5 py-2 bg-white hover:bg-slate-50 text-black rounded-none border border-black font-extrabold shadow-[2px_2px_0px_0px_#000] active:translate-x-[1px] active:translate-y-[1px] active:shadow-none transition-all">Prev</a>
                    @endif

                    @if($employees->hasMorePages())
                        <a href="{{ $employees->nextPageUrl() }}" class="px-3.5 py-2 bg-white hover:bg-slate-50 text-black rounded-none border border-black font-extrabold shadow-[2px_2px_0px_0px_#000] active:translate-x-[1px] active:translate-y-[1px] active:shadow-none transition-all">Next</a>
                    @else
                        <span class="px-3.5 py-2 bg-slate-150 text-slate-400 rounded-none border border-black font-extrabold shadow-sm cursor-not-allowed select-none">Next</span>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Add Employee Glassmorphic Modal overlay -->
<div id="add-employee-modal" class="fixed inset-0 z-50 overflow-y-auto hidden flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
    <div class="bg-white border-2 border-black rounded-none shadow-[8px_8px_0px_0px_#000] max-w-2xl w-full relative z-10 overflow-hidden transform scale-95 transition-transform duration-200" id="modal-card">
        <!-- Header -->
        <div class="px-6 py-5 border-b border-black bg-[#F4ECE6] flex items-center justify-between">
            <h3 class="text-sm font-black uppercase tracking-wider text-black flex items-center gap-2">
                <i class="fa-solid fa-user-plus text-black"></i> Register New Employee
            </h3>
            <button onclick="toggleModal('add-employee-modal', false)" class="p-2 text-black hover:bg-black/5 border border-transparent transition-all">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form action="{{ route('employees.store') }}" method="POST" class="p-6 space-y-5">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Name -->
                <div>
                    <label class="block text-[9px] font-black uppercase tracking-wider text-slate-500 mb-1.5">Full Name</label>
                    <input type="text" name="name" required
                        class="block w-full rounded-none border border-black bg-[#F4ECE6] py-3 px-3.5 text-black focus:ring-0 focus:border-black text-xs transition-all">
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-[9px] font-black uppercase tracking-wider text-slate-500 mb-1.5">Email Address</label>
                    <input type="email" name="email" required
                        class="block w-full rounded-none border border-black bg-[#F4ECE6] py-3 px-3.5 text-black focus:ring-0 focus:border-black text-xs transition-all">
                </div>

                <!-- Login Password -->
                <div>
                    <label class="block text-[9px] font-black uppercase tracking-wider text-slate-500 mb-1.5">Login Password</label>
                    <input type="password" name="password" required placeholder="Min 6 characters"
                        class="block w-full rounded-none border border-black bg-[#F4ECE6] py-3 px-3.5 text-black focus:ring-0 focus:border-black text-xs transition-all">
                </div>

                <!-- Phone -->
                <div>
                    <label class="block text-[9px] font-black uppercase tracking-wider text-slate-500 mb-1.5">Phone Number</label>
                    <input type="text" name="phone"
                        class="block w-full rounded-none border border-black bg-[#F4ECE6] py-3 px-3.5 text-black focus:ring-0 focus:border-black text-xs transition-all"
                        placeholder="+91 99999 99999">
                </div>

                <!-- Join Date -->
                <div>
                    <label class="block text-[9px] font-black uppercase tracking-wider text-slate-500 mb-1.5">Join Date</label>
                    <input type="date" name="join_date" required value="{{ date('Y-m-d') }}"
                        class="block w-full rounded-none border border-black bg-[#F4ECE6] py-3 px-3.5 text-black focus:ring-0 focus:border-black text-xs transition-all">
                </div>

                <!-- Department -->
                <div>
                    <label class="block text-[9px] font-black uppercase tracking-wider text-slate-500 mb-1.5">Department</label>
                    <select name="department" required
                        class="block w-full rounded-none border border-black bg-[#F4ECE6] py-3 px-3.5 text-black focus:ring-0 focus:border-black text-xs font-bold uppercase tracking-wider transition-all">
                        @foreach($departments as $dept)
                            <option value="{{ $dept }}">{{ $dept }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Designation -->
                <div>
                    <label class="block text-[9px] font-black uppercase tracking-wider text-slate-500 mb-1.5">Designation</label>
                    <input type="text" name="designation" required
                        class="block w-full rounded-none border border-black bg-[#F4ECE6] py-3 px-3.5 text-black focus:ring-0 focus:border-black text-xs transition-all"
                        placeholder="e.g., Software Architect">
                </div>

                <!-- Base Salary -->
                <div>
                    <label class="block text-[9px] font-black uppercase tracking-wider text-slate-550 mb-1.5 font-bold">Base Monthly Salary ({{ auth()->user()->currency_symbol }})</label>
                    <input type="number" step="0.01" name="salary" required
                        class="block w-full rounded-none border border-black bg-[#F4ECE6] py-3 px-3.5 text-black focus:ring-0 focus:border-black text-xs transition-all"
                        placeholder="5000.00">
                </div>

                <!-- Bank Name -->
                <div>
                    <label class="block text-[9px] font-black uppercase tracking-wider text-slate-500 mb-1.5">Bank Name</label>
                    <input type="text" name="bank_name"
                        class="block w-full rounded-none border border-black bg-[#F4ECE6] py-3 px-3.5 text-black focus:ring-0 focus:border-black text-xs transition-all"
                        placeholder="e.g., HDFC Bank">
                </div>

                <!-- Account Number -->
                <div class="md:col-span-2">
                    <label class="block text-[9px] font-black uppercase tracking-wider text-slate-500 mb-1.5">Bank Account Number</label>
                    <input type="text" name="account_number"
                        class="block w-full rounded-none border border-black bg-[#F4ECE6] py-3 px-3.5 text-black focus:ring-0 focus:border-black text-xs transition-all"
                        placeholder="e.g., 50100987654321">
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-black/10 mt-6">
                <button type="button" onclick="toggleModal('add-employee-modal', false)"
                    class="px-5 py-2.5 rounded-none bg-transparent hover:bg-slate-100 text-slate-400 hover:text-slate-600 font-extrabold text-xs uppercase tracking-wider transition-colors">
                    Cancel
                </button>
                <button type="submit"
                    class="px-5 py-2.5 rounded-none bg-black hover:bg-neutral-800 text-white font-extrabold text-xs uppercase tracking-wider border border-black shadow-[3px_3px_0px_0px_#000] active:translate-x-[2px] active:translate-y-[2px] active:shadow-none transition-all">
                    Create Employee Profile
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function toggleModal(id, show) {
        const modal = document.getElementById(id);
        const card = modal.querySelector('#modal-card');
        if (show) {
            modal.classList.remove('hidden');
            setTimeout(() => {
                card.classList.remove('scale-95');
                card.classList.add('scale-100');
            }, 10);
        } else {
            card.classList.remove('scale-100');
            card.classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 150);
        }
    }
</script>
@endsection
