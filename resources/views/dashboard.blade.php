@extends('layouts.app')

@section('title', 'Dashboard - PayFlow Enterprise')

@section('content')
<div class="space-y-8">
    <!-- Welcome Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black uppercase tracking-tight text-black">Financial Workspace</h1>
            <p class="text-xs text-slate-600 mt-1.5 font-bold uppercase tracking-wider">Hello, {{ Auth::user()->name }} — Here's the performance summary of <b>{{ $currentMonthLabel }}</b>.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('payroll.index') }}" class="flex items-center gap-2 px-5 py-3 rounded-none bg-black hover:bg-neutral-800 font-extrabold text-xs uppercase tracking-wider text-white border border-black shadow-[3px_3px_0px_0px_#000] transition-all active:translate-x-[2px] active:translate-y-[2px] active:shadow-none">
                <i class="fa-solid fa-plus-circle text-xs"></i> Run New Payroll
            </a>
            <a href="{{ route('employees.index') }}" class="flex items-center gap-2 px-5 py-3 rounded-none bg-white border border-black hover:bg-slate-50 font-extrabold text-xs uppercase tracking-wider text-black shadow-[3px_3px_0px_0px_#000] transition-all active:translate-x-[2px] active:translate-y-[2px] active:shadow-none">
                <i class="fa-solid fa-user-plus text-xs"></i> Add Employee
            </a>
        </div>
    </div>

    <!-- 1. Stats Metric Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Card 1: Active Roster -->
        <div class="bg-white border border-black rounded-none p-6 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] relative overflow-hidden group transition-all duration-200">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">Active Roster</span>
                <div class="w-10 h-10 rounded-none bg-black border border-black text-white flex items-center justify-center shadow-sm">
                    <i class="fa-solid fa-users text-base"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-3xl font-black tracking-tight text-black">{{ $totalEmployees }}</span>
                <span class="text-[10px] font-black uppercase tracking-wider text-slate-550 ml-2">Employees</span>
            </div>
            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-500 mt-3 flex items-center gap-1.5">
                <span class="w-2 h-2 bg-emerald-500 border border-black inline-block animate-pulse"></span> Headcount Operational
            </p>
        </div>

        <!-- Card 2: Salary Payouts -->
        <div class="bg-white border border-black rounded-none p-6 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] relative overflow-hidden group transition-all duration-200">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">Cleared Payouts</span>
                <div class="w-10 h-10 rounded-none bg-black border border-black text-white flex items-center justify-center shadow-sm">
                    <i class="fa-solid fa-circle-check text-base"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-3xl font-black tracking-tight text-black">{{ auth()->user()->currency_symbol }}{{ number_format($totalPaidThisMonth, 2) }}</span>
            </div>
            <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-700 mt-3 flex items-center gap-1">
                <i class="fa-solid fa-arrow-trend-up text-xs"></i> Paid out this month
            </p>
        </div>

        <!-- Card 3: Pending Payouts -->
        <div class="bg-white border border-black rounded-none p-6 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] relative overflow-hidden group transition-all duration-200">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">Pending Ledger</span>
                <div class="w-10 h-10 rounded-none bg-black border border-black text-white flex items-center justify-center shadow-sm">
                    <i class="fa-solid fa-clock text-base"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-3xl font-black tracking-tight text-black">{{ auth()->user()->currency_symbol }}{{ number_format($totalPendingThisMonth, 2) }}</span>
            </div>
            <p class="text-[10px] font-bold uppercase tracking-wider text-amber-700 mt-3 flex items-center gap-1">
                <i class="fa-solid fa-circle-info text-xs"></i> Awaiting Approval
            </p>
        </div>

        <!-- Card 4: Attendance Rate -->
        <div class="bg-white border border-black rounded-none p-6 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] relative overflow-hidden group transition-all duration-200">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">Roster Attendance</span>
                <div class="w-10 h-10 rounded-none bg-black border border-black text-white flex items-center justify-center shadow-sm">
                    <i class="fa-solid fa-calendar-check text-base"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-3xl font-black tracking-tight text-black">{{ $attendanceRate }}%</span>
            </div>
            <p class="text-[10px] font-bold uppercase tracking-wider text-indigo-700 mt-3 flex items-center gap-1">
                <i class="fa-solid fa-calendar-day text-xs"></i> Latest: {{ $latestAttendanceDate ? $latestAttendanceDate->format('M d, Y') : 'N/A' }}
            </p>
        </div>
    </div>

    <!-- 2. Dual Chart Displays -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Expenditure Line Chart (2/3 width) -->
        <div class="lg:col-span-2 bg-white border border-black rounded-none p-6 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] flex flex-col justify-between">
            <div>
                <h3 class="text-xs font-black uppercase tracking-wider text-black flex items-center gap-2">
                    <i class="fa-solid fa-chart-line text-black"></i> Monthly Expenditure Trend
                </h3>
                <p class="text-[10px] font-semibold text-slate-500 mt-0.5">Calculated total salary cleared over the last 6 months.</p>
            </div>
            <div class="mt-6 h-72">
                <canvas id="expenditureChart"></canvas>
            </div>
        </div>

        <!-- Department Breakdown Pie Chart (1/3 width) -->
        <div class="bg-white border border-black rounded-none p-6 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] flex flex-col justify-between">
            <div>
                <h3 class="text-xs font-black uppercase tracking-wider text-black border-b border-black pb-3.5 flex items-center gap-1.5">
                    <i class="fa-solid fa-chart-pie text-black"></i> Department Headcount
                </h3>
                <p class="text-[10px] font-semibold text-slate-500 mt-1">Headcount strength distribution across divisions.</p>
            </div>
            <div class="mt-6 flex justify-center items-center h-48 relative">
                <canvas id="departmentChart"></canvas>
            </div>
            <div class="mt-4 space-y-1 bg-[#F4ECE6] p-3 rounded-none border border-black max-h-24 overflow-y-auto shadow-sm">
                @foreach($deptLabels as $index => $label)
                    <div class="flex justify-between items-center text-xs">
                        <span class="flex items-center gap-1.5 text-slate-700 font-bold">
                            <span class="w-2.5 h-2.5 rounded-none border border-black inline-block" style="background-color: {{ ['#000000', '#2D3748', '#4A5568', '#718096', '#A0AEC0', '#CBD5E0'][$index % 6] }}"></span>
                            {{ $label }}
                        </span>
                        <span class="font-extrabold text-black">{{ $deptCounts[$index] }} ({{ round(($deptCounts[$index]/max(1, array_sum($deptCounts))) * 100) }}%)</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- 3. Recent Transactions Table -->
    <div class="bg-white border border-black rounded-none p-6 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
        <div class="flex items-center justify-between border-b border-black pb-4 mb-6">
            <div>
                <h3 class="text-xs font-black uppercase tracking-wider text-black flex items-center gap-1.5">
                    <i class="fa-solid fa-clock-rotate-left text-black"></i> Recent Payment Transactions
                </h3>
                <p class="text-[10px] font-semibold text-slate-500 mt-0.5">Real-time payment logs processed by the gateway ledger.</p>
            </div>
            <a href="{{ route('payroll.index') }}" class="text-xs font-extrabold uppercase tracking-wider text-black hover:underline flex items-center gap-1">
                View Ledger <i class="fa-solid fa-arrow-right text-[10px] mt-0.5"></i>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm border-collapse">
                <thead>
                    <tr class="text-slate-400 text-[10px] font-black uppercase tracking-wider border-b border-black pb-3">
                        <th class="py-3.5 px-4 font-bold text-black">Transaction ID</th>
                        <th class="py-3.5 px-4 font-bold text-black">Employee</th>
                        <th class="py-3.5 px-4 font-bold text-black">Payout Period</th>
                        <th class="py-3.5 px-4 font-bold text-black">Amount</th>
                        <th class="py-3.5 px-4 font-bold text-black">Method</th>
                        <th class="py-3.5 px-4 font-bold text-black">Status</th>
                        <th class="py-3.5 px-4 font-bold text-black text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black/10">
                    @forelse($recentTransactions as $txn)
                        <tr class="hover:bg-slate-50 transition-colors duration-150">
                            <td class="py-4.5 px-4 font-mono text-xs font-bold text-black">
                                {{ $txn->transaction_number }}
                            </td>
                            <td class="py-4.5 px-4">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-none bg-black text-white flex items-center justify-center font-bold text-xs uppercase shadow-sm border border-black">
                                        {{ substr($txn->payroll->employee->name ?? 'E', 0, 2) }}
                                    </div>
                                    <div>
                                        <p class="font-extrabold text-black text-xs">{{ $txn->payroll->employee->name ?? 'N/A' }}</p>
                                        <p class="text-[9px] text-slate-500 font-bold uppercase tracking-wider mt-0.5">{{ $txn->payroll->employee->designation ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4.5 px-4 text-xs text-slate-700 font-bold">
                                {{ Carbon\Carbon::parse($txn->payroll->month . '-01')->format('F Y') }}
                            </td>
                            <td class="py-4.5 px-4 text-xs font-black text-black">
                                {{ auth()->user()->currency_symbol }}{{ number_format($txn->amount, 2) }}
                            </td>
                            <td class="py-4.5 px-4">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-black bg-[#F4ECE6] border border-black px-2 py-0.5 rounded-none">
                                    {{ $txn->payment_method }}
                                </span>
                            </td>
                            <td class="py-4.5 px-4">
                                @if($txn->status === 'Success')
                                    <span class="inline-flex items-center gap-1.5 text-[9px] font-black uppercase tracking-wider text-emerald-800 bg-emerald-50 border border-emerald-250 px-2 py-0.5 rounded-none">
                                        <span class="w-1.5 h-1.5 bg-emerald-500 border border-black inline-block"></span> Success
                                    </span>
                                @elseif($txn->status === 'Pending')
                                    <span class="inline-flex items-center gap-1.5 text-[9px] font-black uppercase tracking-wider text-amber-800 bg-amber-50 border border-amber-250 px-2 py-0.5 rounded-none">
                                        <span class="w-1.5 h-1.5 bg-amber-500 border border-black inline-block"></span> Pending
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 text-[9px] font-black uppercase tracking-wider text-rose-800 bg-rose-50 border border-rose-250 px-2 py-0.5 rounded-none">
                                        <span class="w-1.5 h-1.5 bg-rose-500 border border-black inline-block"></span> Failed
                                    </span>
                                @endif
                            </td>
                            <td class="py-4.5 px-4 text-right">
                                <a href="{{ route('payroll.slip', $txn->payroll_id) }}" target="_blank"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-none bg-white border border-black hover:bg-slate-50 text-black font-extrabold text-xs uppercase tracking-wider shadow-[2px_2px_0px_0px_#000] active:translate-x-[1px] active:translate-y-[1px] active:shadow-none transition-all">
                                    <i class="fa-solid fa-receipt text-xs"></i> Slip
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-xs text-slate-400 font-bold uppercase tracking-wider">
                                <i class="fa-solid fa-receipt text-3xl text-slate-300 mb-3 block"></i>
                                No payment transactions recorded yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // 1. Expenditure Trend Chart (Brutalist High-Contrast Black Line)
        const expCtx = document.getElementById('expenditureChart').getContext('2d');
        
        new Chart(expCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [{
                    label: 'Net Salaries Cleared ($)',
                    data: {!! json_encode($chartData) !!},
                    borderColor: '#000000', 
                    borderWidth: 4,
                    backgroundColor: 'rgba(0, 0, 0, 0.05)',
                    fill: true,
                    tension: 0, // Brutalist sharp points
                    pointBackgroundColor: '#000000',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        grid: { color: 'rgba(0, 0, 0, 0.15)' },
                        ticks: {
                            color: '#000000',
                            font: { family: 'Outfit', size: 10, weight: 'bold' },
                            callback: function(value) { return '{{ auth()->user()->currency_symbol }}' + value.toLocaleString(); }
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: {
                            color: '#000000',
                            font: { family: 'Outfit', size: 10, weight: 'bold' }
                        }
                    }
                }
            }
        });

        // 2. Department Headcount Breakdown Chart (Brutalist Doughnut)
        const deptCtx = document.getElementById('departmentChart').getContext('2d');
        new Chart(deptCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($deptLabels) !!},
                datasets: [{
                    data: {!! json_encode($deptCounts) !!},
                    backgroundColor: [
                        '#000000', // Black
                        '#1A202C', // Very dark slate
                        '#4A5568', // Medium slate
                        '#718096', // Light slate
                        '#A0AEC0', // Cool grey
                        '#CBD5E0'  // Light grey
                    ],
                    borderWidth: 2,
                    borderColor: '#000000',
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                cutout: '72%'
            }
        });
    });
</script>
@endsection
