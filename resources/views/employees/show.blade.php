@extends('layouts.app')

@section('title', $employee->name . ' - Employee Dossier')

@section('content')
<div class="space-y-8">
    <!-- Back to directory breadcrumbs -->
    <div class="flex items-center gap-2 text-[10px] font-black uppercase tracking-wider text-slate-500">
        <a href="{{ route('employees.index') }}" class="hover:text-black transition-colors">Employee Directory</a>
        <i class="fa-solid fa-chevron-right text-[8px] mt-0.5"></i>
        <span class="text-black">Dossier: {{ $employee->employee_id }}</span>
    </div>

    <!-- Top Profile Summary Card -->
    <div class="bg-white border border-black rounded-none p-6 md:p-8 flex flex-col md:flex-row md:items-center justify-between gap-6 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
        <div class="flex items-center gap-5">
            <div class="w-16 h-16 rounded-none bg-black text-white border border-black flex items-center justify-center font-extrabold text-2xl uppercase shadow-sm">
                {{ strtoupper(substr($employee->name, 0, 2)) }}
            </div>
            <div>
                <h1 class="text-2xl font-black text-black flex items-center gap-2.5">
                    {{ $employee->name }}
                    <span class="text-[10px] font-bold font-mono text-black bg-[#F4ECE6] border border-black px-2.5 py-0.5 rounded-none shadow-[2px_2px_0px_0px_#000]">
                        {{ $employee->employee_id }}
                    </span>
                </h1>
                <p class="text-slate-500 text-xs mt-1.5 font-bold uppercase tracking-wider flex items-center gap-2">
                    <span>{{ $employee->designation }}</span>
                    <span class="w-1 h-1 bg-black"></span>
                    <span>{{ $employee->department }}</span>
                </p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button onclick="toggleModal('edit-employee-modal', true)"
                class="px-5 py-3 rounded-none bg-white border border-black hover:bg-slate-50 font-extrabold text-xs uppercase tracking-wider text-black shadow-[3px_3px_0px_0px_#000] transition-all active:translate-x-[2px] active:translate-y-[2px] active:shadow-none">
                <i class="fa-solid fa-user-pen mr-1"></i> Edit Profile
            </button>
            <form action="{{ route('employees.destroy', $employee->id) }}" method="POST" onsubmit="return confirm('WARNING: Are you absolutely sure you want to permanently delete this employee? All related attendance, payroll and transaction logs will be permanently erased.');" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="px-5 py-3 rounded-none bg-rose-50 border border-rose-250 hover:bg-rose-600 hover:text-white font-extrabold text-xs uppercase tracking-wider text-rose-800 shadow-[3px_3px_0px_0px_#000] transition-all active:translate-x-[2px] active:translate-y-[2px] active:shadow-none">
                    <i class="fa-solid fa-trash-can mr-1"></i> Delete Record
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left 1 Column: Dossier Details & Payout Credentials -->
        <div class="space-y-6">
            <!-- Details Card -->
            <div class="bg-white border border-black rounded-none p-6 space-y-4 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
                <h3 class="text-xs font-black uppercase tracking-wider text-black border-b border-black pb-3 flex items-center gap-1.5">
                    <i class="fa-solid fa-address-book text-black"></i> Contact Information
                </h3>
                <div class="space-y-3.5 text-xs">
                    <div class="flex justify-between items-center bg-[#F4ECE6] border border-black p-3 rounded-none">
                        <span class="text-slate-700 font-extrabold uppercase tracking-wider text-[9px]">Attendance Score:</span>
                        <span class="text-sm font-black text-black">{{ $attendanceRate }}%</span>
                    </div>
                    <div class="flex justify-between border-b border-black/10 pb-1.5">
                        <span class="text-slate-550 font-bold uppercase tracking-wider text-[9px]">Email:</span>
                        <span class="font-extrabold text-black">{{ $employee->email }}</span>
                    </div>
                    <div class="flex justify-between border-b border-black/10 pb-1.5">
                        <span class="text-slate-555 font-bold uppercase tracking-wider text-[9px]">Phone:</span>
                        <span class="font-extrabold text-black">{{ $employee->phone ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between border-b border-black/10 pb-1.5">
                        <span class="text-slate-555 font-bold uppercase tracking-wider text-[9px]">Joined Date:</span>
                        <span class="font-extrabold text-black">{{ $employee->join_date ? $employee->join_date->format('F d, Y') : 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between pb-0.5">
                        <span class="text-slate-555 font-bold uppercase tracking-wider text-[9px]">Contract Status:</span>
                        @if($employee->status === 'Active')
                            <span class="inline-flex items-center gap-1.5 text-[9px] font-black uppercase tracking-wider text-emerald-800 bg-emerald-50 border border-emerald-250 px-2 py-0.5 rounded-none animate-pulse">Active Team</span>
                        @else
                            <span class="inline-flex items-center gap-1.5 text-[9px] font-black uppercase tracking-wider text-slate-800 bg-slate-100 border border-black px-2 py-0.5 rounded-none">Inactive</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Bank Credentials -->
            <div class="bg-white border border-black rounded-none p-6 space-y-4 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
                <h3 class="text-xs font-black uppercase tracking-wider text-black border-b border-black pb-3 flex items-center gap-1.5">
                    <i class="fa-solid fa-credit-card text-black"></i> Payout Credentials
                </h3>
                <div class="space-y-3.5 text-xs">
                    <div class="flex justify-between border-b border-black/10 pb-1.5">
                        <span class="text-slate-555 font-bold uppercase tracking-wider text-[9px]">Monthly Base Salary:</span>
                        <span class="font-black text-black">{{ auth()->user()->currency_symbol }}{{ number_format($employee->salary, 2) }}</span>
                    </div>
                    <div class="flex justify-between border-b border-black/10 pb-1.5">
                        <span class="text-slate-555 font-bold uppercase tracking-wider text-[9px]">Disbursing Bank:</span>
                        <span class="font-extrabold text-black">{{ $employee->bank_name ?? 'Not configured' }}</span>
                    </div>
                    <div class="flex justify-between items-center bg-[#F4ECE6] border border-black p-3 rounded-none">
                        <span class="text-slate-700 font-extrabold uppercase tracking-wider text-[9px]">Account Wire:</span>
                        <span class="font-mono font-black text-black">{{ $employee->account_number ?? 'Not configured' }}</span>
                    </div>
                </div>
            </div>

            <!-- Attendance Metrics Gauge -->
            <div class="bg-white border border-black rounded-none p-6 space-y-4 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
                <h3 class="text-xs font-black uppercase tracking-wider text-black border-b border-black pb-3 flex items-center gap-1.5">
                    <i class="fa-solid fa-calendar-day text-black"></i> Attendance Dossier
                </h3>
                <div class="flex items-center gap-5">
                    <div class="w-16 h-16 rounded-none border-2 border-black bg-[#F4ECE6] flex items-center justify-center relative overflow-hidden flex-shrink-0 shadow-[2px_2px_0px_0px_#000]">
                        <span class="text-sm font-black text-black">{{ $attendanceRate }}%</span>
                    </div>
                    <div class="flex-1 space-y-1.5 text-xs text-slate-650 font-bold uppercase tracking-wider text-[9px]">
                        <div class="flex justify-between border-b border-black/10 pb-1">
                            <span>Days Present:</span>
                            <span class="font-black text-black">{{ $attendanceStats['present'] }}</span>
                        </div>
                        <div class="flex justify-between border-b border-black/10 pb-1">
                            <span>Days Absent:</span>
                            <span class="font-black text-black">{{ $attendanceStats['absent'] }}</span>
                        </div>
                        <div class="flex justify-between pb-0.5">
                            <span>Approved Leave:</span>
                            <span class="font-black text-black">{{ $attendanceStats['leave'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right 2 Columns: Payout Ledgers -->
        <div class="lg:col-span-2 bg-white border border-black rounded-none p-6 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
            <h3 class="text-xs font-black uppercase tracking-wider text-black border-b border-black pb-4 mb-6 flex items-center gap-1.5">
                <i class="fa-solid fa-file-invoice text-black"></i> Monthly Payroll ledger
            </h3>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm border-collapse">
                    <thead>
                        <tr class="text-slate-400 text-[10px] font-black uppercase tracking-wider border-b border-black pb-3">
                            <th class="py-3 px-3 font-bold text-black">Month</th>
                            <th class="py-3 px-3 font-bold text-black">Gross Basic</th>
                            <th class="py-3 px-3 font-bold text-black">Bonus (+)</th>
                            <th class="py-3 px-3 font-bold text-black">Deductions (-)</th>
                            <th class="py-3 px-3 font-bold text-black">Net Payout</th>
                            <th class="py-3 px-3 font-bold text-black">Status</th>
                            <th class="py-3 px-3 font-bold text-black text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-black/10">
                        @forelse($payrolls as $pr)
                            <tr class="hover:bg-slate-50 transition-colors duration-150">
                                <td class="py-4.5 px-3 text-xs font-black text-black">
                                    {{ Carbon\Carbon::parse($pr->month . '-01')->format('M Y') }}
                                </td>
                                <td class="py-4.5 px-3 text-xs text-slate-700 font-bold">
                                    {{ auth()->user()->currency_symbol }}{{ number_format($pr->basic_salary, 2) }}
                                </td>
                                <td class="py-4.5 px-3 text-xs text-emerald-700 font-extrabold">
                                    +{{ auth()->user()->currency_symbol }}{{ number_format($pr->bonus, 2) }}
                                </td>
                                <td class="py-4.5 px-3 text-xs text-rose-700 font-extrabold">
                                    -{{ auth()->user()->currency_symbol }}{{ number_format($pr->deductions, 2) }}
                                </td>
                                <td class="py-4.5 px-3 text-xs font-black text-black">
                                    {{ auth()->user()->currency_symbol }}{{ number_format($pr->net_salary, 2) }}
                                </td>
                                <td class="py-4.5 px-3">
                                    @if($pr->status === 'Paid')
                                        <span class="inline-flex items-center gap-1.5 text-[9px] font-black uppercase tracking-wider text-emerald-800 bg-emerald-50 border border-emerald-250 px-2 py-0.5 rounded-none">
                                            Success
                                        </span>
                                    @elseif($pr->status === 'Pending')
                                        <span class="inline-flex items-center gap-1.5 text-[9px] font-black uppercase tracking-wider text-amber-800 bg-amber-50 border border-amber-250 px-2 py-0.5 rounded-none animate-pulse">
                                            Pending
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 text-[9px] font-black uppercase tracking-wider text-rose-800 bg-rose-50 border border-rose-250 px-2 py-0.5 rounded-none">
                                            Failed
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4.5 px-3 text-right">
                                    @if($pr->status === 'Paid')
                                        <a href="{{ route('payroll.slip', $pr->id) }}" target="_blank"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-none bg-white border border-black hover:bg-slate-50 text-black font-extrabold text-[10px] uppercase tracking-wider shadow-[2px_2px_0px_0px_#000] active:translate-x-[1px] active:translate-y-[1px] active:shadow-none transition-all"
                                            title="Print Salary Invoice">
                                            <i class="fa-solid fa-print text-xs"></i> Slip
                                        </a>
                                    @else
                                        <a href="{{ route('payroll.index', ['month' => $pr->month]) }}"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-none bg-black hover:bg-neutral-800 text-white font-extrabold text-[10px] uppercase tracking-wider border border-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] active:translate-x-[1px] active:translate-y-[1px] active:shadow-none transition-all"
                                            title="Go to Payroll Center to clear payment">
                                            Process Payout
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-xs text-slate-400 font-bold uppercase tracking-wider">
                                    <i class="fa-solid fa-receipt text-3xl text-slate-200 mb-3 block"></i>
                                    No payroll records generated yet for this employee.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Edit Employee Glassmorphic Modal -->
<div id="edit-employee-modal" class="fixed inset-0 z-50 overflow-y-auto hidden flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
    <div class="bg-white border-2 border-black rounded-none shadow-[8px_8px_0px_0px_#000] max-w-2xl w-full relative z-10 overflow-hidden transform scale-95 transition-transform duration-200" id="modal-card">
        <!-- Header -->
        <div class="px-6 py-5 border-b border-black bg-[#F4ECE6] flex items-center justify-between">
            <h3 class="text-sm font-black uppercase tracking-wider text-black flex items-center gap-2">
                <i class="fa-solid fa-address-book text-black"></i> Edit Employee Dossier
            </h3>
            <button onclick="toggleModal('edit-employee-modal', false)" class="p-2 text-black hover:bg-black/5 border border-transparent transition-all">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form action="{{ route('employees.update', $employee->id) }}" method="POST" class="p-6 space-y-5">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Name -->
                <div>
                    <label class="block text-[9px] font-black uppercase tracking-wider text-slate-500 mb-1.5">Full Name</label>
                    <input type="text" name="name" value="{{ $employee->name }}" required
                        class="block w-full rounded-none border border-black bg-[#F4ECE6] py-3 px-3.5 text-black focus:ring-0 focus:border-black text-xs transition-all">
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-[9px] font-black uppercase tracking-wider text-slate-500 mb-1.5">Email Address</label>
                    <input type="email" name="email" value="{{ $employee->email }}" required
                        class="block w-full rounded-none border border-black bg-[#F4ECE6] py-3 px-3.5 text-black focus:ring-0 focus:border-black text-xs transition-all">
                </div>

                <!-- Phone -->
                <div>
                    <label class="block text-[9px] font-black uppercase tracking-wider text-slate-500 mb-1.5">Phone Number</label>
                    <input type="text" name="phone" value="{{ $employee->phone }}"
                        class="block w-full rounded-none border border-black bg-[#F4ECE6] py-3 px-3.5 text-black focus:ring-0 focus:border-black text-xs transition-all">
                </div>

                <!-- Join Date -->
                <div>
                    <label class="block text-[9px] font-black uppercase tracking-wider text-slate-500 mb-1.5">Join Date</label>
                    <input type="date" name="join_date" value="{{ $employee->join_date ? $employee->join_date->format('Y-m-d') : '' }}" required
                        class="block w-full rounded-none border border-black bg-[#F4ECE6] py-3 px-3.5 text-black focus:ring-0 focus:border-black text-xs transition-all">
                </div>

                <!-- Department -->
                <div>
                    <label class="block text-[9px] font-black uppercase tracking-wider text-slate-500 mb-1.5">Department</label>
                    <select name="department" required
                        class="block w-full rounded-none border border-black bg-[#F4ECE6] py-3 px-3.5 text-black focus:ring-0 focus:border-black text-xs font-bold uppercase tracking-wider transition-all">
                        @foreach($departments as $dept)
                            <option value="{{ $dept }}" {{ $employee->department === $dept ? 'selected' : '' }}>{{ $dept }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Designation -->
                <div>
                    <label class="block text-[9px] font-black uppercase tracking-wider text-slate-500 mb-1.5">Designation</label>
                    <input type="text" name="designation" value="{{ $employee->designation }}" required
                        class="block w-full rounded-none border border-black bg-[#F4ECE6] py-3 px-3.5 text-black focus:ring-0 focus:border-black text-xs transition-all">
                </div>

                <!-- Base Salary -->
                <div>
                    <label class="block text-[9px] font-black uppercase tracking-wider text-slate-550 mb-1.5 font-bold">Base Monthly Salary ({{ auth()->user()->currency_symbol }})</label>
                    <input type="number" step="0.01" name="salary" value="{{ $employee->salary }}" required
                        class="block w-full rounded-none border border-black bg-[#F4ECE6] py-3 px-3.5 text-black focus:ring-0 focus:border-black text-xs transition-all">
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-[9px] font-black uppercase tracking-wider text-slate-500 mb-1.5">Operational Status</label>
                    <select name="status" required
                        class="block w-full rounded-none border border-black bg-[#F4ECE6] py-3 px-3.5 text-black focus:ring-0 focus:border-black text-xs font-bold uppercase tracking-wider transition-all">
                        <option value="Active" {{ $employee->status === 'Active' ? 'selected' : '' }}>Active Team</option>
                        <option value="Inactive" {{ $employee->status === 'Inactive' ? 'selected' : '' }}>Inactive / Suspended</option>
                    </select>
                </div>

                <!-- Bank Name -->
                <div>
                    <label class="block text-[9px] font-black uppercase tracking-wider text-slate-550 mb-1.5">Bank Name</label>
                    <input type="text" name="bank_name" value="{{ $employee->bank_name }}"
                        class="block w-full rounded-none border border-black bg-[#F4ECE6] py-3 px-3.5 text-black focus:ring-0 focus:border-black text-xs transition-all">
                </div>

                <!-- Account Number -->
                <div>
                    <label class="block text-[9px] font-black uppercase tracking-wider text-slate-550 mb-1.5">Bank Account Number</label>
                    <input type="text" name="account_number" value="{{ $employee->account_number }}"
                        class="block w-full rounded-none border border-black bg-[#F4ECE6] py-3 px-3.5 text-black focus:ring-0 focus:border-black text-xs transition-all">
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-black/10 mt-6">
                <button type="button" onclick="toggleModal('edit-employee-modal', false)"
                    class="px-5 py-2.5 rounded-none bg-transparent hover:bg-slate-100 text-slate-400 hover:text-slate-600 font-extrabold text-xs uppercase tracking-wider transition-colors">
                    Cancel
                </button>
                <button type="submit"
                    class="px-5 py-2.5 rounded-none bg-black hover:bg-neutral-800 text-white font-extrabold text-xs uppercase tracking-wider border border-black shadow-[3px_3px_0px_0px_#000] active:translate-x-[2px] active:translate-y-[2px] active:shadow-none transition-all">
                    Save Changes
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
