@extends('layouts.app')

@section('title', 'Payroll Operations - PayFlow')

@section('content')
<div class="space-y-8">
    <!-- Header Block -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black uppercase tracking-tight text-black">Payroll & Payouts</h1>
            <p class="text-xs text-slate-650 mt-1.5 font-bold uppercase tracking-wider">Generate payrolls, apply bonuses/taxes, and clear transaction payments.</p>
        </div>

        <!-- Bulk Generate Trigger Form -->
        <form action="{{ route('payroll.generate') }}" method="POST" class="flex items-center gap-3">
            @csrf
            <input type="hidden" name="month" value="{{ $month }}">
            <button type="submit"
                class="flex items-center gap-2 px-5 py-3 rounded-none bg-black hover:bg-neutral-800 font-extrabold text-xs uppercase tracking-wider text-white border border-black shadow-[3px_3px_0px_0px_#000] transition-all active:translate-x-[2px] active:translate-y-[2px] active:shadow-none">
                <i class="fa-solid fa-file-invoice-dollar text-xs"></i> Generate Pending Month Payroll
            </button>
        </form>
    </div>

    <!-- Month Selection Bar -->
    <div class="bg-white border border-black rounded-none p-5 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
        <form action="{{ route('payroll.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4 items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-none bg-black border border-black text-white flex items-center justify-center shadow-sm">
                    <i class="fa-solid fa-credit-card text-base"></i>
                </div>
                <div>
                    <span class="block text-[9px] font-black uppercase tracking-wider text-slate-450">Active Payout Cycle</span>
                    <span class="text-black text-sm font-black uppercase tracking-wider">{{ Carbon\Carbon::parse($month . '-01')->format('F Y') }}</span>
                </div>
            </div>

            <div class="flex items-center gap-3 w-full sm:w-auto">
                <input type="month" name="month" value="{{ $month }}"
                    class="block w-full sm:w-48 rounded-none border border-black bg-[#F4ECE6] py-2.5 px-3.5 text-black focus:ring-0 focus:border-black text-xs font-bold transition-all duration-200">
                <button type="submit"
                    class="px-5 py-2.5 rounded-none bg-black hover:bg-neutral-800 font-extrabold text-xs uppercase tracking-wider text-white border border-black transition-colors whitespace-nowrap">
                    Go to Cycle
                </button>
            </div>
        </form>
    </div>

    <!-- Payout Statistics Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Roster Processed Count -->
        <div class="bg-white border border-black rounded-none p-5 relative overflow-hidden group shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
            <div class="flex items-center justify-between text-[9px] font-black uppercase tracking-wider text-slate-400">
                <span>Processed Ledgers</span>
                <i class="fa-solid fa-file-invoice text-black text-base"></i>
            </div>
            <div class="mt-3">
                <span class="text-2xl font-black text-black">{{ $totalProcessedCount }}</span>
                <span class="text-[9px] text-slate-500 font-bold uppercase tracking-wider ml-1">/ {{ count($employees) }} active roster</span>
            </div>
        </div>

        <!-- Cleared Payments Count -->
        <div class="bg-white border border-black rounded-none p-5 relative overflow-hidden group shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
            <div class="flex items-center justify-between text-[9px] font-black uppercase tracking-wider text-slate-400">
                <span>Cleared Payments</span>
                <i class="fa-solid fa-circle-check text-black text-base"></i>
            </div>
            <div class="mt-3">
                <span class="text-2xl font-black text-black">{{ $totalPaidCount }}</span>
                <span class="text-[9px] text-slate-500 font-bold uppercase tracking-wider ml-1">/ {{ $totalProcessedCount }} generated</span>
            </div>
        </div>

        <!-- Disbursed Volume -->
        <div class="bg-white border border-black rounded-none p-5 relative overflow-hidden group shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
            <div class="flex items-center justify-between text-[9px] font-black uppercase tracking-wider text-slate-400">
                <span>Disbursed Volume</span>
                <i class="fa-solid fa-wallet text-black text-base"></i>
            </div>
            <div class="mt-3">
                <span class="text-2xl font-black text-black">{{ auth()->user()->currency_symbol }}{{ number_format($totalExpenses, 2) }}</span>
            </div>
        </div>

        <!-- Pending Ledger Volume -->
        <div class="bg-white border border-black rounded-none p-5 relative overflow-hidden group shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
            <div class="flex items-center justify-between text-[9px] font-black uppercase tracking-wider text-slate-400">
                <span>Awaiting Approval</span>
                <i class="fa-solid fa-clock text-black text-base"></i>
            </div>
            <div class="mt-3">
                <span class="text-2xl font-black text-black">{{ auth()->user()->currency_symbol }}{{ number_format($totalPendingExpenses, 2) }}</span>
            </div>
        </div>
    </div>

    <!-- Payroll Roster Table -->
    <div class="bg-white border border-black rounded-none p-6 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm border-collapse">
                <thead>
                    <tr class="text-slate-400 text-[10px] font-black uppercase tracking-wider border-b border-black pb-3">
                        <th class="py-3.5 px-4 font-bold text-black">Employee ID</th>
                        <th class="py-3.5 px-4 font-bold text-black">FullName</th>
                        <th class="py-3.5 px-4 font-bold text-black">Gross Salary</th>
                        <th class="py-3.5 px-4 font-bold text-black">Bonus (+)</th>
                        <th class="py-3.5 px-4 font-bold text-black">Deductions (-)</th>
                        <th class="py-3.5 px-4 font-bold text-black">Net Payout</th>
                        <th class="py-3.5 px-4 font-bold text-black">Status</th>
                        <th class="py-3.5 px-4 font-bold text-black text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black/10">
                    @forelse($employees as $emp)
                        @php
                            $pr = $payrolls->get($emp->id);
                        @endphp
                        <tr class="hover:bg-slate-50 transition-colors duration-150">
                            <td class="py-4.5 px-4 font-mono text-xs font-bold text-black">
                                {{ $emp->employee_id }}
                            </td>
                            <td class="py-4.5 px-4">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-none bg-black text-white border border-black flex items-center justify-center font-bold text-xs uppercase shadow-sm">
                                        {{ substr($emp->name, 0, 2) }}
                                    </div>
                                    <div>
                                        <p class="font-extrabold text-black text-xs">{{ $emp->name }}</p>
                                        <p class="text-[9px] text-slate-500 font-bold uppercase tracking-wider mt-0.5">{{ $emp->designation }}</p>
                                    </div>
                                </div>
                            </td>
                            @if($pr)
                                <td class="py-4.5 px-4 text-xs font-bold text-slate-700">
                                    {{ auth()->user()->currency_symbol }}{{ number_format($pr->basic_salary, 2) }}
                                </td>
                                <td class="py-4.5 px-4 text-xs text-emerald-700 font-extrabold">
                                    +{{ auth()->user()->currency_symbol }}{{ number_format($pr->bonus, 2) }}
                                </td>
                                <td class="py-4.5 px-4 text-xs text-rose-700 font-extrabold">
                                    -{{ auth()->user()->currency_symbol }}{{ number_format($pr->deductions, 2) }}
                                </td>
                                <td class="py-4.5 px-4 text-xs font-black text-black">
                                    {{ auth()->user()->currency_symbol }}{{ number_format($pr->net_salary, 2) }}
                                </td>
                                <td class="py-4.5 px-4">
                                    @if($pr->status === 'Paid')
                                        <span class="inline-flex items-center gap-1.5 text-[9px] font-black uppercase tracking-wider text-emerald-800 bg-emerald-50 border border-emerald-250 px-2.5 py-0.5 rounded-none">
                                            <span class="w-1.5 h-1.5 bg-emerald-500 border border-black inline-block"></span> Paid
                                        </span>
                                    @elseif($pr->status === 'Pending')
                                        <span class="inline-flex items-center gap-1.5 text-[9px] font-black uppercase tracking-wider text-amber-800 bg-amber-50 border border-amber-250 px-2.5 py-0.5 rounded-none">
                                            <span class="w-1.5 h-1.5 bg-amber-500 border border-black inline-block animate-pulse"></span> Pending
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 text-[9px] font-black uppercase tracking-wider text-rose-800 bg-rose-50 border border-rose-250 px-2.5 py-0.5 rounded-none">
                                            <span class="w-1.5 h-1.5 bg-rose-500 border border-black inline-block"></span> Failed
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4.5 px-4 text-right font-semibold">
                                    <div class="flex items-center justify-end gap-2">
                                        @if($pr->status !== 'Paid')
                                            <!-- Edit parameters trigger -->
                                            <button onclick="openAdjustModal({{ $pr->id }}, {{ $pr->bonus }}, {{ $pr->deductions }}, '{{ $emp->name }}')"
                                                class="inline-flex items-center gap-1 px-3 py-1.5 rounded-none bg-white border border-black hover:bg-slate-50 text-black font-extrabold text-[10px] uppercase tracking-wider shadow-[2px_2px_0px_0px_#000] active:translate-x-[1px] active:translate-y-[1px] active:shadow-none transition-all"
                                                title="Adjust bonus/taxes">
                                                <i class="fa-solid fa-calculator"></i> Adjust
                                            </button>
                                            
                                            <!-- Pay trigger -->
                                            <button onclick="openPayModal({{ $pr->id }}, {{ $pr->net_salary }}, '{{ $emp->name }}')"
                                                class="inline-flex items-center gap-1 px-3 py-1.5 rounded-none bg-black border border-black hover:bg-neutral-800 text-white font-extrabold text-[10px] uppercase tracking-wider shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] active:translate-x-[1px] active:translate-y-[1px] active:shadow-none transition-all"
                                                title="Clear payout clearance">
                                                <i class="fa-solid fa-cash-register"></i> Disburse
                                            </button>
                                        @else
                                            <!-- Printable Slip -->
                                            <a href="{{ route('payroll.slip', $pr->id) }}" target="_blank"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-none bg-white border border-black hover:bg-slate-50 text-black font-extrabold text-[10px] uppercase tracking-wider shadow-[2px_2px_0px_0px_#000] active:translate-x-[1px] active:translate-y-[1px] active:shadow-none transition-all"
                                                title="Print salary stubs">
                                                <i class="fa-solid fa-print"></i> Slip
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            @else
                                <td colspan="5" class="py-4.5 px-4 text-xs font-bold text-slate-400 italic">
                                    Ledger sheet not generated for this billing cycle.
                                </td>
                                <td class="py-4.5 px-4">
                                    <span class="inline-flex items-center gap-1.5 text-[9px] font-black uppercase tracking-wider text-slate-800 bg-slate-100 border border-black px-2.5 py-0.5 rounded-none">
                                        Unprocessed
                                    </span>
                                </td>
                                <td class="py-4.5 px-4 text-right">
                                    <span class="text-[9px] text-slate-400 font-bold uppercase tracking-wider italic">Click 'Generate' above</span>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-xs text-slate-400 font-bold uppercase tracking-wider">
                                <i class="fa-solid fa-users-slash text-4xl text-slate-300 mb-3 block"></i>
                                No active employees registered. Register employees in the directory first.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal 1: Adjust Parameters Modal -->
<div id="adjust-modal" class="fixed inset-0 z-50 overflow-y-auto hidden flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
    <div class="bg-white border-2 border-black rounded-none shadow-[8px_8px_0px_0px_#000] max-w-md w-full relative z-10 overflow-hidden transform scale-95 transition-transform duration-200" id="adjust-modal-card">
        <div class="px-6 py-5 border-b border-black bg-[#F4ECE6] flex items-center justify-between">
            <h3 class="text-sm font-black uppercase tracking-wider text-black flex items-center gap-2">
                <i class="fa-solid fa-calculator text-black"></i> Adjust Financials
            </h3>
            <button onclick="toggleModal('adjust-modal', false)" class="p-2 text-black hover:bg-black/5 border border-transparent transition-all">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form id="adjust-form" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            
            <p class="text-xs text-slate-650 font-bold uppercase tracking-wider">Adjusting monthly payout metrics for: <b class="text-black font-extrabold" id="adjust-emp-name"></b></p>
            
            <!-- Bonus -->
            <div>
                <label class="block text-[9px] font-black uppercase tracking-wider text-slate-500 mb-1.5">Monthly Bonus ({{ auth()->user()->currency_symbol }})</label>
                <input type="number" step="0.01" name="bonus" id="input-bonus" required min="0"
                    class="block w-full rounded-none border border-black bg-[#F4ECE6] py-3 px-3.5 text-black focus:ring-0 focus:border-black text-xs font-bold transition-all">
            </div>

            <!-- Deductions -->
            <div>
                <label class="block text-[9px] font-black uppercase tracking-wider text-slate-500 mb-1.5">Taxes & Deductions ({{ auth()->user()->currency_symbol }})</label>
                <input type="number" step="0.01" name="deductions" id="input-deductions" required min="0"
                    class="block w-full rounded-none border border-black bg-[#F4ECE6] py-3 px-3.5 text-black focus:ring-0 focus:border-black text-xs font-bold transition-all">
            </div>

            <!-- Buttons -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-black/10 mt-6">
                <button type="button" onclick="toggleModal('adjust-modal', false)"
                    class="px-5 py-2.5 rounded-none bg-transparent hover:bg-slate-100 text-slate-400 hover:text-slate-600 font-extrabold text-xs uppercase tracking-wider transition-colors">
                    Cancel
                </button>
                <button type="submit"
                    class="px-5 py-2.5 rounded-none bg-black hover:bg-neutral-800 text-white font-extrabold text-xs uppercase tracking-wider border border-black shadow-[3px_3px_0px_0px_#000] active:translate-x-[2px] active:translate-y-[2px] active:shadow-none transition-all">
                    Save Parameters
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 2: Clear Payment / Disburse Modal overlay -->
<div id="pay-modal" class="fixed inset-0 z-50 overflow-y-auto hidden flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
    <div class="bg-white border-2 border-black rounded-none shadow-[8px_8px_0px_0px_#000] max-w-lg w-full relative z-10 overflow-hidden transform scale-95 transition-transform duration-200" id="pay-modal-card" style="font-family: 'Outfit', sans-serif;">
        <!-- Header -->
        <div class="px-6 py-5 border-b border-black bg-[#F4ECE6] flex items-center justify-between">
            <h3 class="text-sm font-black uppercase tracking-wider text-black flex items-center gap-2">
                <i class="fa-solid fa-cash-register text-black"></i> Corporate Payment Desk
            </h3>
            <button onclick="toggleModal('pay-modal', false)" class="p-2 text-black hover:bg-black/5 border border-transparent transition-all">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <!-- Main Content Area -->
        <div class="relative min-h-[420px]">
            <!-- Form & Gateway selection screen -->
            <div id="pay-form-screen" class="p-6 space-y-5">
                <p class="text-xs text-slate-650 font-bold uppercase tracking-wider">
                    Disbursing monthly payout of: <b class="text-black font-extrabold" id="pay-emp-amount"></b> for <b class="text-black font-extrabold" id="pay-emp-name"></b>
                </p>

                <!-- Gateways Navigation Tabs -->
                <div>
                    <label class="block text-[9px] font-black uppercase tracking-wider text-slate-500 mb-2">Select Payout Gateway</label>
                    <div class="grid grid-cols-4 gap-1.5 bg-[#F4ECE6] p-1 border border-black">
                        <button type="button" onclick="selectGateway('Stripe')" id="tab-Stripe"
                            class="gateway-tab py-2.5 text-[9px] font-black uppercase tracking-wider border border-transparent transition-all flex flex-col items-center justify-center gap-1 text-black bg-white border-black shadow-[2px_2px_0px_0px_#000]">
                            <i class="fa-solid fa-credit-card text-xs"></i> Stripe Card
                        </button>
                        <button type="button" onclick="selectGateway('PayPal')" id="tab-PayPal"
                            class="gateway-tab py-2.5 text-[9px] font-black uppercase tracking-wider border border-transparent transition-all flex flex-col items-center justify-center gap-1 text-slate-500 hover:text-black">
                            <i class="fa-brands fa-paypal text-xs"></i> PayPal
                        </button>
                        <button type="button" onclick="selectGateway('Bank Transfer')" id="tab-Bank-Transfer"
                            class="gateway-tab py-2.5 text-[9px] font-black uppercase tracking-wider border border-transparent transition-all flex flex-col items-center justify-center gap-1 text-slate-500 hover:text-black">
                            <i class="fa-solid fa-bank text-xs"></i> SWIFT Wire
                        </button>
                        <button type="button" onclick="selectGateway('Cash')" id="tab-Cash"
                            class="gateway-tab py-2.5 text-[9px] font-black uppercase tracking-wider border border-transparent transition-all flex flex-col items-center justify-center gap-1 text-slate-500 hover:text-black">
                            <i class="fa-solid fa-hand-holding-dollar text-xs"></i> Cash Ledger
                        </button>
                    </div>
                </div>

                <!-- FORM CONTAINER -->
                <form id="pay-form" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="payment_method" id="selected-method-input" value="Stripe">

                    <!-- GATEWAY PANELS -->
                    <!-- 1. Stripe Form Panel -->
                    <div id="panel-Stripe" class="gateway-panel space-y-4">
                        <!-- Premium Interactive Credit Card Preview -->
                        <div class="relative w-full h-36 bg-gradient-to-br from-indigo-600 via-indigo-700 to-purple-800 border-2 border-black text-white p-5 flex flex-col justify-between shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] overflow-hidden">
                            <!-- Card Design Embellishments -->
                            <div class="absolute -right-10 -bottom-10 w-32 h-32 bg-white/10 rounded-full blur-xl pointer-events-none"></div>
                            <div class="absolute -left-10 -top-10 w-24 h-24 bg-purple-500/20 rounded-full blur-xl pointer-events-none"></div>

                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-circle-nodes text-lg text-white/80"></i>
                                    <span class="text-[9px] font-extrabold uppercase tracking-widest text-indigo-200">PayFlow Connect</span>
                                </div>
                                <!-- Dynamic Brand Logo Container -->
                                <div id="card-brand-logo" class="text-xl font-black italic tracking-tighter opacity-90 transition-all duration-300">
                                    <i class="fa-brands fa-cc-visa text-2xl"></i>
                                </div>
                            </div>
                            
                            <!-- Card Number -->
                            <div id="preview-card-number" class="text-sm font-bold tracking-[0.2em] font-mono text-center my-3 opacity-90">
                                ••••  ••••  ••••  ••••
                            </div>

                            <div class="flex items-center justify-between text-[8px] uppercase tracking-wider text-indigo-200">
                                <div>
                                    <span class="block text-[6px] text-indigo-300">Cardholder Name</span>
                                    <span id="preview-card-name" class="font-bold text-white uppercase text-[8px]">Corporate Officer</span>
                                </div>
                                <div class="text-right">
                                    <span class="block text-[6px] text-indigo-300">Expires</span>
                                    <span id="preview-card-expiry" class="font-bold text-white text-[8px]">12/30</span>
                                </div>
                            </div>
                        </div>

                        <!-- Stripe Inputs -->
                        <div class="grid grid-cols-2 gap-3">
                            <div class="col-span-2">
                                <label class="block text-[9px] font-black uppercase tracking-wider text-slate-500 mb-1">Corporate Card Number</label>
                                <div class="relative">
                                    <input type="text" id="stripe-card-number" placeholder="4242 4242 4242 4242" value="{{ auth()->user()->stripe_key }}"
                                        class="block w-full rounded-none border border-black bg-[#F4ECE6] py-2 px-3 text-black focus:ring-0 focus:border-black text-xs font-bold font-mono transition-all">
                                    <div class="absolute right-3 top-2.5 text-xs text-slate-400">
                                        <i class="fa-solid fa-lock"></i>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label class="block text-[9px] font-black uppercase tracking-wider text-slate-500 mb-1">Expiry Date</label>
                                <input type="text" id="stripe-card-expiry" placeholder="MM/YY" maxlength="5" value="{{ auth()->user()->stripe_secret }}"
                                    class="block w-full rounded-none border border-black bg-[#F4ECE6] py-2 px-3 text-black focus:ring-0 focus:border-black text-xs font-bold text-center transition-all">
                            </div>
                            <div>
                                <label class="block text-[9px] font-black uppercase tracking-wider text-slate-500 mb-1">Secure CVC</label>
                                <input type="password" id="stripe-card-cvc" placeholder="•••" maxlength="4" value="{{ auth()->user()->paypal_client_id }}"
                                    class="block w-full rounded-none border border-black bg-[#F4ECE6] py-2 px-3 text-black focus:ring-0 focus:border-black text-xs font-bold text-center transition-all">
                            </div>
                        </div>
                    </div>

                    <!-- 2. PayPal Panel -->
                    <div id="panel-PayPal" class="gateway-panel space-y-3 hidden">
                        <div class="p-6 bg-amber-50/50 border border-amber-200 text-center space-y-3 flex flex-col items-center justify-center">
                            <div class="text-amber-500 text-3xl">
                                <i class="fa-brands fa-paypal"></i>
                            </div>
                            <h4 class="text-xs font-black uppercase tracking-wider text-black">PayPal Corporate Payout Desk</h4>
                            @if(auth()->user()->paypal_client_secret)
                                <p class="text-[8px] text-amber-700 font-extrabold uppercase tracking-wider">Disbursing Account: {{ auth()->user()->paypal_client_secret }}</p>
                            @endif
                            <p class="text-[9px] text-slate-500 max-w-xs font-bold leading-normal uppercase">
                                Click the yellow PayPal Smart button to initiate simulated authorization popup and synchronize ledger balances.
                            </p>
                            <!-- Smart PayPal Button -->
                            <button type="button" onclick="triggerPayPalPopup()"
                                class="w-full max-w-xs py-3 bg-[#FFC439] hover:bg-[#F2B51F] border border-black text-black font-extrabold text-[10px] uppercase tracking-wider shadow-[3px_3px_0px_0px_#000] active:translate-x-[1px] active:translate-y-[1px] active:shadow-none flex items-center justify-center gap-2 transition-all">
                                <i class="fa-brands fa-paypal"></i> Pay with <span class="font-black italic">PayPal</span>
                            </button>
                        </div>
                    </div>

                    <!-- 3. SWIFT Wire Transfer Panel -->
                    <div id="panel-Bank-Transfer" class="gateway-panel space-y-3 hidden">
                        <div class="p-3.5 bg-slate-50 border border-black space-y-2">
                            <div class="flex items-center justify-between border-b border-dashed border-slate-350 pb-1.5">
                                <span class="text-[9px] font-black text-slate-500 uppercase tracking-wider">Beneficiary Account IBAN</span>
                                <span class="text-[9px] font-black text-black font-mono select-all">GB93WEST12345678901234</span>
                            </div>
                            <div class="flex items-center justify-between border-b border-dashed border-slate-350 pb-1.5">
                                <span class="text-[9px] font-black text-slate-500 uppercase tracking-wider">Routing BIC/SWIFT</span>
                                <span class="text-[9px] font-black text-black font-mono select-all">WESTGB2LXXX</span>
                            </div>
                        </div>

                        <!-- Drag and Drop file upload simulator -->
                        <div>
                            <label class="block text-[9px] font-black uppercase tracking-wider text-slate-500 mb-1">Attach Clearance Receipt</label>
                            <div id="swift-dropzone" onclick="triggerFileSelect()"
                                class="border border-dashed border-black bg-[#F4ECE6] hover:bg-[#eae0d7] p-5 text-center cursor-pointer transition-all flex flex-col items-center justify-center gap-1">
                                <i class="fa-solid fa-cloud-arrow-up text-xl text-black"></i>
                                <span class="text-[9px] font-black text-black uppercase tracking-wider" id="dropzone-text">Drag & Drop receipt or Click here</span>
                                <span class="text-[7px] text-slate-400 font-bold uppercase tracking-wider">PDF, PNG, JPG accepted (Simulated Upload)</span>
                                <input type="file" id="swift-file-input" class="hidden" accept=".pdf,.png,.jpg,.jpeg" onchange="handleFileSelect(event)">
                            </div>
                            <!-- Mock Upload Progress Bar -->
                            <div id="swift-progress-wrapper" class="hidden mt-2 space-y-1">
                                <div class="flex items-center justify-between text-[8px] font-black uppercase tracking-wider">
                                    <span class="text-indigo-650" id="swift-file-name">receipt.pdf</span>
                                    <span class="text-black" id="swift-progress-pct">0%</span>
                                </div>
                                <div class="w-full bg-[#F4ECE6] border border-black h-2 overflow-hidden">
                                    <div id="swift-progress-bar" class="bg-black h-full transition-all duration-300" style="width: 0%;"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 4. Cash Ledger Panel -->
                    <div id="panel-Cash" class="gateway-panel space-y-3 hidden">
                        <div class="p-4 bg-emerald-50 border border-emerald-250 flex items-start gap-2.5">
                            <i class="fa-solid fa-circle-check text-emerald-600 text-sm mt-0.5"></i>
                            <div>
                                <h4 class="text-xs font-black uppercase tracking-wider text-emerald-800">Local Cash Disbursement</h4>
                                <p class="text-[9px] text-emerald-700/80 mt-1 font-bold leading-normal uppercase">
                                    This instantly offsets the employee’s ledger balance with a manual cash release marker. No remote API call is initiated.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Reference Notes -->
                    <div>
                        <label class="block text-[9px] font-black uppercase tracking-wider text-slate-500 mb-1">Reference Notes (Optional)</label>
                        <textarea name="notes" rows="2" placeholder="e.g. Monthly salary payout approved by executive."
                            class="block w-full rounded-none border border-black bg-[#F4ECE6] py-2 px-3 text-black focus:ring-0 focus:border-black text-xs font-bold transition-all resize-none"></textarea>
                    </div>

                    <!-- Modal Actions footer -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-black/10 mt-5">
                        <button type="button" onclick="toggleModal('pay-modal', false)"
                            class="px-5 py-2.5 rounded-none bg-transparent hover:bg-slate-100 text-slate-400 hover:text-slate-600 font-extrabold text-xs uppercase tracking-wider transition-colors">
                            Cancel
                        </button>
                        <button type="submit" id="pay-submit-btn"
                            class="px-5 py-2.5 rounded-none bg-black hover:bg-neutral-800 text-white font-extrabold text-xs uppercase tracking-wider border border-black shadow-[3px_3px_0px_0px_#000] active:translate-x-[2px] active:translate-y-[2px] active:shadow-none transition-all flex items-center gap-2">
                            <span>Approve & Disburse</span> <i class="fa-solid fa-chevron-right"></i>
                        </button>
                    </div>
                </form>
            </div>

            <!-- STEPPER PROGRESS LOADING OVERLAY (Hidden by Default) -->
            <div id="pay-loading-screen" class="absolute inset-0 bg-white/95 flex flex-col items-center justify-center p-8 space-y-6 hidden select-none">
                <div class="relative w-16 h-16 flex items-center justify-center">
                    <!-- Spinning Outer ring -->
                    <div class="absolute inset-0 rounded-full border-4 border-[#F4ECE6] border-t-black animate-spin"></div>
                    <i class="fa-solid fa-lock text-black text-lg"></i>
                </div>

                <div class="text-center space-y-1 max-w-sm">
                    <h4 class="text-xs font-black uppercase tracking-widest text-black">Verifying Transaction Ledgers</h4>
                    <p class="text-[9px] text-slate-400 font-bold uppercase tracking-wider" id="stepper-subtext">Initializing handshake with banking API...</p>
                </div>

                <!-- Animated Step Markers -->
                <div class="w-full max-w-xs space-y-2 text-[8px] font-black uppercase tracking-wider text-slate-400">
                    <div id="step-1" class="flex items-center gap-2 transition-colors duration-300">
                        <i class="fa-solid fa-circle-notch animate-spin text-[10px]"></i>
                        <span>1. Establish Secure Handshake</span>
                    </div>
                    <div id="step-2" class="flex items-center gap-2 transition-colors duration-300">
                        <i class="fa-solid fa-circle text-[6px] opacity-30"></i>
                        <span>2. Card & Vault Checksum Authentication</span>
                    </div>
                    <div id="step-3" class="flex items-center gap-2 transition-colors duration-300">
                        <i class="fa-solid fa-circle text-[6px] opacity-30"></i>
                        <span>3. Resolve Clearing House Ledgers</span>
                    </div>
                    <div id="step-4" class="flex items-center gap-2 transition-colors duration-300">
                        <i class="fa-solid fa-circle text-[6px] opacity-30"></i>
                        <span>4. Generate Monospace Voucher</span>
                    </div>
                </div>
            </div>

            <!-- GORGEOUS MONOSPACE RECEIPT VOUCHER SCREEN (Hidden by Default) -->
            <div id="pay-success-screen" class="absolute inset-0 bg-white p-8 space-y-5 hidden overflow-y-auto">
                <div class="text-center space-y-1">
                    <div class="w-10 h-10 bg-emerald-50 border border-emerald-200 text-emerald-600 rounded-full flex items-center justify-center mx-auto text-base">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                    <h4 class="text-xs font-black uppercase tracking-widest text-emerald-800">Clearance Dispatch Complete</h4>
                    <p class="text-[8px] text-slate-400 font-bold uppercase tracking-wider">Payroll ledger updated successfully</p>
                </div>

                <!-- Vintage Cryptographic Monospace Receipt Ticket -->
                <div class="border-2 border-black border-dashed bg-slate-50 p-4 font-mono text-[10px] text-black space-y-2 leading-relaxed shadow-[4px_4px_0px_0px_#000]">
                    <div class="text-center font-bold tracking-widest border-b border-black border-dashed pb-1.5 uppercase">
                        *** PayFlow Clearance Stub ***
                    </div>
                    <div class="grid grid-cols-2 gap-1 pt-1.5">
                        <span class="text-slate-500 uppercase">Beneficiary:</span>
                        <span class="font-bold text-right" id="rec-employee-name">Himanshu Shekhar</span>
                        
                        <span class="text-slate-500 uppercase">Volume:</span>
                        <span class="font-bold text-right text-emerald-700" id="rec-amount">₹45,000.00</span>
                        
                        <span class="text-slate-500 uppercase">Gateway:</span>
                        <span class="font-bold text-right uppercase" id="rec-gateway">Stripe Payouts</span>
                        
                        <span class="text-slate-500 uppercase">Status:</span>
                        <span class="font-bold text-right text-emerald-600 uppercase">Success (Cleared)</span>
                        
                        <span class="text-slate-500 uppercase">Txn Key:</span>
                        <span class="font-bold text-right text-[8px] text-indigo-700 font-mono select-all overflow-hidden text-ellipsis" id="rec-txn-id">ch_1N9e5e2eZvKYlo2C</span>
                    </div>
                    <div class="text-[7px] text-slate-400 font-bold text-center border-t border-black border-dashed pt-2 mt-2 uppercase tracking-wide">
                        Authorized Corporate Digital Signature Verified
                    </div>
                </div>

                <!-- Success Voucher Actions -->
                <div class="flex items-center justify-center gap-3 pt-4">
                    <button type="button" onclick="printReceiptStub()"
                        class="px-5 py-2.5 rounded-none bg-white hover:bg-slate-50 text-black font-extrabold text-xs uppercase tracking-wider border border-black shadow-[3px_3px_0px_0px_#000] active:translate-x-[2px] active:translate-y-[2px] active:shadow-none transition-all flex items-center gap-2">
                        <i class="fa-solid fa-print"></i> <span>Print Voucher</span>
                    </button>
                    <button type="button" onclick="closeVoucherComplete()"
                        class="px-5 py-2.5 rounded-none bg-black hover:bg-neutral-800 text-white font-extrabold text-xs uppercase tracking-wider border border-black shadow-[3px_3px_0px_0px_#000] active:translate-x-[2px] active:translate-y-[2px] active:shadow-none transition-all">
                        Done
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let selectedGateway = 'Stripe';
    let currentPayrollId = null;

    function toggleModal(id, show) {
        const modal = document.getElementById(id);
        const card = modal.querySelector('#' + id + '-card');
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

    function openAdjustModal(payrollId, bonus, deductions, empName) {
        const form = document.getElementById('adjust-form');
        form.action = `/payroll/${payrollId}/update`;
        document.getElementById('adjust-emp-name').innerText = empName;
        document.getElementById('input-bonus').value = bonus;
        document.getElementById('input-deductions').value = deductions;
        toggleModal('adjust-modal', true);
    }

    function openPayModal(payrollId, netSalary, empName) {
        currentPayrollId = payrollId;
        const form = document.getElementById('pay-form');
        form.action = `/payroll/${payrollId}/pay`;
        
        document.getElementById('pay-emp-name').innerText = empName;
        document.getElementById('pay-emp-amount').innerText = '{{ auth()->user()->currency_symbol }}' + Number(netSalary).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});

        // Reset Gateway selection back to Stripe
        selectGateway('Stripe');

        // Prefill Card Preview from inputs
        const cardNum = document.getElementById('stripe-card-number').value || '••••  ••••  ••••  ••••';
        const cardExp = document.getElementById('stripe-card-expiry').value || '12/30';
        document.getElementById('preview-card-number').innerText = cardNum;
        document.getElementById('preview-card-expiry').innerText = cardExp;
        
        // Reset screens
        document.getElementById('pay-success-screen').classList.add('hidden');
        document.getElementById('pay-loading-screen').classList.add('hidden');
        document.getElementById('pay-form-screen').classList.remove('hidden');

        // Reset SWIFT inputs
        document.getElementById('swift-progress-wrapper').classList.add('hidden');
        document.getElementById('dropzone-text').innerText = 'Drag & Drop receipt or Click here';
        document.getElementById('swift-dropzone').className = "border border-dashed border-black bg-[#F4ECE6] hover:bg-[#eae0d7] p-5 text-center cursor-pointer transition-all flex flex-col items-center justify-center gap-1";

        toggleModal('pay-modal', true);
    }

    // Switch between Gateway Tabs and update states
    function selectGateway(method) {
        selectedGateway = method;
        document.getElementById('selected-method-input').value = method;

        // Reset all tabs active states
        document.querySelectorAll('.gateway-tab').forEach(tab => {
            tab.classList.remove('bg-white', 'border-black', 'shadow-[2px_2px_0px_0px_#000]', 'text-black');
            tab.classList.add('text-slate-500');
        });

        // Activate selected tab
        const activeTab = document.getElementById('tab-' + method.replace(' ', '-'));
        if (activeTab) {
            activeTab.classList.remove('text-slate-500');
            activeTab.classList.add('bg-white', 'border-black', 'shadow-[2px_2px_0px_0px_#000]', 'text-black');
        }

        // Hide all gateway panels
        document.querySelectorAll('.gateway-panel').forEach(panel => {
            panel.classList.add('hidden');
        });

        // Show selected panel
        const activePanel = document.getElementById('panel-' + method.replace(' ', '-'));
        if (activePanel) {
            activePanel.classList.remove('hidden');
        }

        // Adjust Disburse button visibility depending on PayPal
        const submitBtn = document.getElementById('pay-submit-btn');
        if (method === 'PayPal') {
            submitBtn.classList.add('hidden'); // PayPal uses smart button triggers
        } else {
            submitBtn.classList.remove('hidden');
        }
    }

    // Stripe Live Card Preview Dynamics
    const stripeCardInput = document.getElementById('stripe-card-number');
    if (stripeCardInput) {
        stripeCardInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
            let formatted = '';
            for (let i = 0; i < value.length; i++) {
                if (i > 0 && i % 4 === 0) formatted += ' ';
                formatted += value[i];
            }
            e.target.value = formatted.substring(0, 19);

            // Update card preview text
            const previewCard = document.getElementById('preview-card-number');
            previewCard.innerText = e.target.value || '••••  ••••  ••••  ••••';

            // Detect Card Brand Logo
            const brandLogo = document.getElementById('card-brand-logo');
            if (value.startsWith('4')) {
                brandLogo.innerHTML = '<i class="fa-brands fa-cc-visa text-2xl"></i>';
            } else if (value.startsWith('5')) {
                brandLogo.innerHTML = '<i class="fa-brands fa-cc-mastercard text-2xl"></i>';
            } else if (value.startsWith('3')) {
                brandLogo.innerHTML = '<i class="fa-brands fa-cc-amex text-2xl"></i>';
            } else {
                brandLogo.innerHTML = '<i class="fa-solid fa-credit-card text-xl"></i>';
            }
        });
    }

    // SWIFT file selection simulator
    function triggerFileSelect() {
        document.getElementById('swift-file-input').click();
    }

    function handleFileSelect(event) {
        const file = event.target.files[0];
        if (!file) return;

        document.getElementById('swift-file-name').innerText = file.name;
        document.getElementById('swift-progress-wrapper').classList.remove('hidden');
        document.getElementById('dropzone-text').innerText = 'Receipt Attached';

        // Animate mock file upload progress
        let pct = 0;
        const progressPct = document.getElementById('swift-progress-pct');
        const progressBar = document.getElementById('swift-progress-bar');
        const interval = setInterval(() => {
            pct += 10;
            progressBar.style.width = pct + '%';
            progressPct.innerText = pct + '%';
            if (pct >= 100) {
                clearInterval(interval);
                progressPct.innerText = 'Uploaded';
                document.getElementById('swift-dropzone').classList.remove('border-dashed');
                document.getElementById('swift-dropzone').classList.add('border-emerald-500', 'bg-emerald-50/20');
            }
        }, 120);
    }

    // Launch PayPal Authentication Popup Portal Simulator
    function triggerPayPalPopup() {
        const netAmount = document.getElementById('pay-emp-amount').innerText;
        const empName = document.getElementById('pay-emp-name').innerText;
        
        const popupHtml = `
            <!DOCTYPE html>
            <html>
            <head>
                <title>PayPal Secure Payout Consent Portal</title>
                <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;700;900&display=swap" rel="stylesheet">
                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
                <style>
                    body {
                        font-family: 'Outfit', sans-serif;
                        background-color: #0b0f19;
                        color: #fff;
                        display: flex;
                        flex-direction: column;
                        justify-content: space-between;
                        height: 100vh;
                        padding: 24px;
                        margin: 0;
                        box-sizing: border-box;
                        overflow: hidden;
                    }
                    .header {
                        display: flex;
                        align-items: center;
                        justify-content: space-between;
                        border-b: 1px solid #1e293b;
                        padding-bottom: 12px;
                        margin-bottom: 16px;
                    }
                    .title-container {
                        display: flex;
                        align-items: center;
                        gap: 6px;
                    }
                    .title {
                        font-size: 11px;
                        font-weight: 900;
                        text-transform: uppercase;
                        letter-spacing: 0.1em;
                        color: #cbd5e1;
                    }
                    .badge {
                        background-color: rgba(251, 191, 36, 0.1);
                        border: 1px solid rgba(251, 191, 36, 0.3);
                        color: #fbbf24;
                        font-size: 8px;
                        font-weight: 900;
                        text-transform: uppercase;
                        padding: 2px 8px;
                    }
                    .label {
                        font-size: 8px;
                        font-weight: 900;
                        text-transform: uppercase;
                        letter-spacing: 0.05em;
                        color: #64748b;
                        display: block;
                        margin-bottom: 4px;
                    }
                    .amount {
                        font-size: 24px;
                        font-weight: 900;
                        letter-spacing: -0.02em;
                        color: #fff;
                        margin: 0;
                    }
                    .card {
                        padding: 16px;
                        background-color: rgba(15, 23, 42, 0.6);
                        border: 1px solid #1e293b;
                        display: flex;
                        flex-direction: column;
                        gap: 10px;
                        margin-top: 16px;
                    }
                    .row {
                        display: flex;
                        justify-content: space-between;
                        font-size: 10px;
                    }
                    .row-label {
                        color: #94a3b8;
                        font-weight: 600;
                        text-transform: uppercase;
                    }
                    .row-value {
                        font-weight: 800;
                        color: #fff;
                    }
                    .footer-container {
                        margin-top: auto;
                    }
                    .footer-text {
                        font-size: 8px;
                        color: #64748b;
                        font-weight: 900;
                        text-transform: uppercase;
                        text-align: center;
                        margin: 0 0 12px 0;
                        line-height: 1.4;
                    }
                    .btn {
                        width: 100%;
                        padding: 12px;
                        background-color: #fbbf24;
                        border: 1px solid #000;
                        color: #000;
                        font-weight: 900;
                        font-size: 10px;
                        text-transform: uppercase;
                        letter-spacing: 0.1em;
                        cursor: pointer;
                        transition: all 0.2s;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        gap: 8px;
                        box-shadow: 3px 3px 0px 0px rgba(255,255,255,0.15);
                    }
                    .btn:hover {
                        background-color: #f59e0b;
                    }
                    .btn:disabled {
                        opacity: 0.6;
                        cursor: not-allowed;
                    }
                </style>
            </head>
            <body>
                <div>
                    <div class="header">
                        <div class="title-container">
                            <i class="fa-brands fa-paypal" style="color: #fbbf24; font-size: 16px;"></i>
                            <span class="title">PayPal Sandbox</span>
                        </div>
                        <span class="badge">Developer</span>
                    </div>

                    <div>
                        <span class="label">Requested Authorization Volume</span>
                        <h2 class="amount">${netAmount}</h2>
                    </div>

                    <div class="card">
                        <div class="row">
                            <span class="row-label">Beneficiary Name</span>
                            <span class="row-value">${empName}</span>
                        </div>
                        <div class="row">
                            <span class="row-label">Channel Method</span>
                            <span class="row-value">REST API Payout Dispatch</span>
                        </div>
                    </div>
                </div>

                <div class="footer-container">
                    <p class="footer-text">
                        By clicking below, you consent to vault clearance matching the sandbox target configurations.
                    </p>
                    <button onclick="this.disabled=true; this.innerHTML='<i class=\\'fa-solid fa-spinner animate-spin\\'></i> Clearing Balance...'; setTimeout(() => { window.opener.postMessage({ paypalSuccess: true }, '*'); window.close(); }, 1500);" id="approve-btn" class="btn">
                        <i class="fa-solid fa-circle-check"></i> Approve and Complete Payout
                    </button>
                </div>
            </body>
            </html>
        `;
        
        const w = 450;
        const h = 500;
        const left = (screen.width / 2) - (w / 2);
        const top = (screen.height / 2) - (h / 2);
        
        const popup = window.open("", "PayPalAuthPortal", `width=${w},height=${h},top=${top},left=${left}`);
        popup.document.write(popupHtml);
        popup.document.close();
    }

    // Set up message listener for PayPal postMessage bridge
    window.addEventListener('message', function(event) {
        if (event.data && event.data.paypalSuccess) {
            submitPaymentAJAX(); // Trigger the backend clearance dynamically!
        }
    });

    // Form submit intercept
    document.getElementById('pay-form').addEventListener('submit', function(e) {
        e.preventDefault();
        submitPaymentAJAX();
    });

    // Submits the payout and runs the gorgeous step-by-step clearance animation
    function submitPaymentAJAX() {
        const form = document.getElementById('pay-form');
        const formData = new FormData(form);

        // Show Stepper loading screen instantly
        const loadingScreen = document.getElementById('pay-loading-screen');
        loadingScreen.classList.remove('hidden');

        // Reset Stepper markers
        const steps = ['step-1', 'step-2', 'step-3', 'step-4'];
        steps.forEach(stepId => {
            const el = document.getElementById(stepId);
            el.className = "flex items-center gap-2 text-slate-400 transition-colors duration-300";
            if (stepId === 'step-1') {
                el.innerHTML = '<i class="fa-solid fa-circle-notch animate-spin text-[10px]"></i> <span>1. Establish Secure Handshake</span>';
            } else if (stepId === 'step-2') {
                el.innerHTML = '<i class="fa-solid fa-circle text-[6px] opacity-30"></i> <span>2. Card & Vault Checksum Authentication</span>';
            } else if (stepId === 'step-3') {
                el.innerHTML = '<i class="fa-solid fa-circle text-[6px] opacity-30"></i> <span>3. Resolve Clearing House Ledgers</span>';
            } else if (stepId === 'step-4') {
                el.innerHTML = '<i class="fa-solid fa-circle text-[6px] opacity-30"></i> <span>4. Generate Monospace Voucher</span>';
            }
        });

        // Run sequential timed step messages to look incredibly beautiful
        setTimeout(() => {
            markStepDone('step-1', '1. Secure Handshake Tunnel Established');
            markStepActive('step-2', '2. Authenticating Signature Checksums...');
        }, 1000);

        setTimeout(() => {
            markStepDone('step-2', '2. Card & Vault Verification Completed');
            markStepActive('step-3', '3. Resolving Banking Ledgers...');
        }, 2000);

        setTimeout(() => {
            markStepDone('step-3', '3. Remote Settlement Confirmed');
            markStepActive('step-4', '4. Writing Cryptographic Receipt Stub...');
        }, 3000);

        // Perform the actual AJAX dispatch
        setTimeout(() => {
            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => { throw err; });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    markStepDone('step-4', '4. Receipt Ledger Saved Successfully');
                    
                    // Render Receipt voucher
                    document.getElementById('rec-employee-name').innerText = data.employee_name;
                    document.getElementById('rec-amount').innerText = '{{ auth()->user()->currency_symbol }}' + data.amount;
                    document.getElementById('rec-gateway').innerText = selectedGateway + ' Payout';
                    document.getElementById('rec-txn-id').innerText = data.txn_number;

                    // Transition screens
                    setTimeout(() => {
                        loadingScreen.classList.add('hidden');
                        document.getElementById('pay-success-screen').classList.remove('hidden');
                        
                        // Dynamically update corresponding row inside current DOM payroll list
                        const activeRow = document.querySelector(`button[onclick*="openPayModal(${currentPayrollId}"]`);
                        if (activeRow) {
                            const tdAction = activeRow.closest('td');
                            const trParent = tdAction.closest('tr');
                            
                            // 1. Swap Action cell to show Slip Print Anchor
                            tdAction.innerHTML = `
                                <div class="flex items-center justify-end gap-2">
                                    <a href="/payroll/${currentPayrollId}/slip" target="_blank"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-none bg-white border border-black hover:bg-slate-50 text-black font-extrabold text-[10px] uppercase tracking-wider shadow-[2px_2px_0px_0px_#000] active:translate-x-[1px] active:translate-y-[1px] active:shadow-none transition-all">
                                        <i class="fa-solid fa-print"></i> Slip
                                    </a>
                                </div>
                            `;
                            
                            // 2. Find and update the status badge cell in the same row
                            const tdStatus = trParent.querySelector('td:nth-last-child(2)');
                            if (tdStatus) {
                                tdStatus.innerHTML = `
                                    <span class="inline-flex items-center gap-1.5 text-[9px] font-black uppercase tracking-wider text-emerald-800 bg-emerald-50 border border-emerald-250 px-2.5 py-0.5 rounded-none">
                                        <span class="w-1.5 h-1.5 bg-emerald-500 border border-black inline-block"></span> Paid
                                    </span>
                                `;
                            }
                        }
                    }, 500);
                } else {
                    alert(data.message || 'Disbursement error occurred.');
                    loadingScreen.classList.add('hidden');
                }
            })
            .catch(err => {
                alert(err.message || 'A network error occurred. Please verify payment client credentials.');
                loadingScreen.classList.add('hidden');
            });
        }, 3500);
    }

    function markStepActive(stepId, text) {
        const el = document.getElementById(stepId);
        el.className = "flex items-center gap-2 text-indigo-650 font-bold transition-colors duration-300";
        el.innerHTML = `<i class="fa-solid fa-circle-notch animate-spin text-[10px]"></i> <span>${text}</span>`;
    }

    function markStepDone(stepId, text) {
        const el = document.getElementById(stepId);
        el.className = "flex items-center gap-2 text-emerald-600 font-bold transition-colors duration-300";
        el.innerHTML = `<i class="fa-solid fa-circle-check text-[10px]"></i> <span>${text}</span>`;
    }

    function closeVoucherComplete() {
        toggleModal('pay-modal', false);
        setTimeout(() => {
            document.getElementById('pay-success-screen').classList.add('hidden');
            document.getElementById('pay-form-screen').classList.remove('hidden');
        }, 300);
    }

    function printReceiptStub() {
        const employee = document.getElementById('rec-employee-name').innerText;
        const amount = document.getElementById('rec-amount').innerText;
        const gateway = document.getElementById('rec-gateway').innerText;
        const txnId = document.getElementById('rec-txn-id').innerText;
        const date = new Date().toLocaleString();

        const printWindow = window.open('', '_blank', 'width=600,height=500');
        printWindow.document.write(`
            <html>
            <head>
                <title>Print Clearance Voucher - \${txnId}</title>
                <style>
                    body {
                        font-family: 'Courier New', Courier, monospace;
                        background-color: #fff;
                        color: #000;
                        padding: 40px;
                        font-size: 12px;
                        line-height: 1.6;
                    }
                    .dashed-line {
                        border-top: 1px dashed #000;
                        margin: 20px 0;
                    }
                    .text-center { text-align: center; }
                    .bold { font-weight: bold; }
                    table { width: 100%; border-collapse: collapse; }
                    td { padding: 4px 0; }
                    .right { text-align: right; }
                </style>
            </head>
            <body onload="window.print(); window.close();">
                <div class="text-center bold">
                    PAYFLOW ENTERPRISE SYSTEM<br>
                    CLEARANCE DISBURSEMENT STUB VOUCHER
                </div>
                <div class="dashed-line"></div>
                <table>
                    <tr><td class="bold">BENEFICIARY:</td><td class="right">\${employee}</td></tr>
                    <tr><td class="bold">TOTAL AMOUNT:</td><td class="right">\${amount}</td></tr>
                    <tr><td class="bold">GATEWAY METHOD:</td><td class="right">\${gateway}</td></tr>
                    <tr><td class="bold">SETTLEMENT STATUS:</td><td class="right">SUCCESS / CLEARED</td></tr>
                    <tr><td class="bold">TRANSACTION ID:</td><td class="right font-mono">\${txnId}</td></tr>
                    <tr><td class="bold">CLEARANCE DATE:</td><td class="right">\${date}</td></tr>
                </table>
                <div class="dashed-line"></div>
                <div class="text-center font-mono" style="font-size: 9px;">
                    SECURE BLOCK LEDGER CRYPTOGRAPHIC SIGNATURE CERTIFIED<br>
                    THANK YOU FOR WORKING WITH US
                </div>
            </body>
            </html>
        `);
        printWindow.document.close();
    }
</script>
@endsection
