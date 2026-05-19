@extends('layouts.app')

@section('title', 'Employee Dossier Portal - PayFlow')

@section('content')
<div class="space-y-8">
    <!-- Header Block -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black uppercase tracking-tight text-black">My Personal Portal</h1>
            <p class="text-xs text-slate-600 mt-1.5 font-bold uppercase tracking-wider">Hello, {{ $employee->name }} — Access your attendance scoring, payout accounts, and print historical salary stubs.</p>
        </div>
        <div class="mt-4 md:mt-0 flex flex-wrap gap-2.5">
            <span class="text-[10px] font-black uppercase tracking-wider text-black bg-white border border-black px-3.5 py-2 rounded-none inline-block shadow-[2px_2px_0px_0px_#000]">
                Employee Reference ID: <b class="font-mono text-black">{{ $employee->employee_id }}</b>
            </span>
        </div>
    </div>

    <!-- Personal Overview Panels -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- 1. Contact Dossier Card -->
        <div class="bg-white border border-black rounded-none p-6 space-y-4 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
            <h3 class="text-xs font-black uppercase tracking-wider text-black border-b border-black pb-3 flex items-center gap-1.5">
                <i class="fa-solid fa-id-card text-black"></i> Job & Personal details
            </h3>
            <div class="space-y-3 text-xs">
                <div class="flex justify-between border-b border-black/10 pb-1.5">
                    <span class="text-slate-500 font-bold uppercase tracking-wider text-[9px]">Designation:</span>
                    <span class="font-extrabold text-black">{{ $employee->designation }}</span>
                </div>
                <div class="flex justify-between border-b border-black/10 pb-1.5">
                    <span class="text-slate-500 font-bold uppercase tracking-wider text-[9px]">Department:</span>
                    <span class="font-extrabold text-black">{{ $employee->department }}</span>
                </div>
                <div class="flex justify-between border-b border-black/10 pb-1.5">
                    <span class="text-slate-500 font-bold uppercase tracking-wider text-[9px]">Corporate Email:</span>
                    <span class="font-extrabold text-black">{{ $employee->email }}</span>
                </div>
                <div class="flex justify-between border-b border-black/10 pb-1.5">
                    <span class="text-slate-500 font-bold uppercase tracking-wider text-[9px]">Phone:</span>
                    <span class="font-extrabold text-black">{{ $employee->phone ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between pb-0.5">
                    <span class="text-slate-500 font-bold uppercase tracking-wider text-[9px]">Contract Join Date:</span>
                    <span class="font-extrabold text-black">{{ $employee->join_date ? $employee->join_date->format('F d, Y') : 'N/A' }}</span>
                </div>
            </div>
        </div>

        <!-- 2. Bank Wire Parameters -->
        <div class="bg-white border border-black rounded-none p-6 space-y-4 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
            <h3 class="text-xs font-black uppercase tracking-wider text-black border-b border-black pb-3 flex items-center gap-1.5">
                <i class="fa-solid fa-credit-card text-black"></i> Disbursement Accounts
            </h3>
            <div class="space-y-3 text-xs">
                <div class="flex justify-between border-b border-black/10 pb-1.5">
                    <span class="text-slate-500 font-bold uppercase tracking-wider text-[9px]">Monthly Base Salary:</span>
                    <span class="font-black text-black">{{ auth()->user()->currency_symbol }}{{ number_format($employee->salary, 2) }}</span>
                </div>
                <div class="flex justify-between border-b border-black/10 pb-1.5">
                    <span class="text-slate-500 font-bold uppercase tracking-wider text-[9px]">Bank Name:</span>
                    <span class="font-extrabold text-black">{{ $employee->bank_name ?? 'Not configured' }}</span>
                </div>
                <div class="flex justify-between items-center bg-[#F4ECE6] border border-black p-3 rounded-none">
                    <span class="text-slate-700 font-extrabold uppercase tracking-wider text-[9px]">Account Wire:</span>
                    <span class="font-mono font-black text-black">{{ $employee->account_number ?? 'Not configured' }}</span>
                </div>
                <div class="p-3 bg-white border border-black rounded-none text-[9px] text-black font-semibold flex items-start gap-2 mt-1">
                    <i class="fa-solid fa-circle-info text-black font-bold mt-0.5"></i>
                    <span>Contact HR Officer if you need to submit new banking credentials or verify wire routing codes.</span>
                </div>
            </div>
        </div>

        <!-- 3. Roster Check-ins Gauge -->
        <div class="bg-white border border-black rounded-none p-6 space-y-4 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
            <h3 class="text-xs font-black uppercase tracking-wider text-black border-b border-black pb-3 flex items-center gap-1.5">
                <i class="fa-solid fa-calendar-check text-black"></i> Personal Attendance
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
                        <span>Approved Leave:</span>
                        <span class="font-black text-black">{{ $attendanceStats['leave'] }}</span>
                    </div>
                    <div class="flex justify-between pb-0.5">
                        <span>Days Absent:</span>
                        <span class="font-black text-black">{{ $attendanceStats['absent'] }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payout Ledger Sheets Table -->
    <div class="bg-white border border-black rounded-none p-6 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
        <h3 class="text-xs font-black uppercase tracking-wider text-black border-b border-black pb-4 mb-6 flex items-center gap-1.5">
            <i class="fa-solid fa-receipt text-black"></i> Historical Salary Slips
        </h3>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm border-collapse">
                <thead>
                    <tr class="text-slate-400 text-[10px] font-black uppercase tracking-wider border-b border-black pb-3">
                        <th class="py-3 px-3 font-bold text-black">Billing Cycle</th>
                        <th class="py-3 px-3 font-bold text-black">Gross Salary</th>
                        <th class="py-3 px-3 font-bold text-black">Bonus (+)</th>
                        <th class="py-3 px-3 font-bold text-black">Deductions (-)</th>
                        <th class="py-3 px-3 font-bold text-black">Net Payout Amount</th>
                        <th class="py-3 px-3 font-bold text-black">Payment Status</th>
                        <th class="py-3 px-3 font-bold text-black text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black/10">
                    @forelse($payrolls as $pr)
                        <tr class="hover:bg-slate-50 transition-colors duration-150">
                            <td class="py-4.5 px-3 text-xs font-black text-black">
                                {{ Carbon\Carbon::parse($pr->month . '-01')->format('F Y') }}
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
                                        <span class="w-1.5 h-1.5 bg-emerald-500 border border-black inline-block"></span> Disbursed
                                    </span>
                                @elseif($pr->status === 'Pending')
                                    <span class="inline-flex items-center gap-1.5 text-[9px] font-black uppercase tracking-wider text-amber-800 bg-amber-50 border border-amber-250 px-2 py-0.5 rounded-none">
                                        <span class="w-1.5 h-1.5 bg-amber-500 border border-black inline-block animate-pulse"></span> Pending Clearance
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 text-[9px] font-black uppercase tracking-wider text-rose-800 bg-rose-50 border border-rose-250 px-2 py-0.5 rounded-none">
                                        <span class="w-1.5 h-1.5 bg-rose-500 border border-black inline-block"></span> Failed
                                    </span>
                                @endif
                            </td>
                            <td class="py-4.5 px-3 text-right">
                                @if($pr->status === 'Paid')
                                    <a href="{{ route('payroll.slip', $pr->id) }}" target="_blank"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-none bg-white border border-black hover:bg-slate-50 text-black font-extrabold text-xs uppercase tracking-wider shadow-[2px_2px_0px_0px_#000] active:translate-x-[1px] active:translate-y-[1px] active:shadow-none transition-all"
                                        title="Print salary invoice slip">
                                        <i class="fa-solid fa-print"></i> View / Print Slip
                                    </a>
                                @else
                                    <span class="text-xs text-slate-400 font-bold uppercase tracking-wider italic">Slip Unavailable</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-xs text-slate-400 font-bold uppercase tracking-wider">
                                <i class="fa-solid fa-receipt text-3xl text-slate-200 mb-3 block"></i>
                                No salary payout stubs recorded in your archive ledger.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
