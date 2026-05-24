@extends('layouts.app')

@section('title', 'Settings - PayFlow')

@section('content')
<div class="space-y-8 select-none">
    
    <!-- Title Header block -->
    <div class="p-6 border-2 border-black bg-white shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-black text-black leading-tight flex items-center gap-2">
                ⚙️ Workspace & Profile Settings
            </h1>
            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-500 mt-1">
                @if($user->role === 'admin')
                    Configure your corporate workspace details, payment currencies, and security keys.
                @else
                    Manage your personal account credentials, passwords, and security profiles.
                @endif
            </p>
        </div>
        <div>
            <span class="inline-block px-3 py-1.5 bg-[#F4ECE6] border border-black text-black text-[9px] font-black uppercase tracking-widest">
                <i class="fa-solid fa-shield text-amber-500 mr-1 animate-pulse"></i> {{ $user->role === 'admin' ? 'Administrative Control' : 'Employee Account' }}
            </span>
        </div>
    </div>

    <!-- Main Settings Panel Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left 2 Columns: Settings Forms -->
        <div class="lg:col-span-2 border-2 border-black bg-white shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] p-6 md:p-8 space-y-6">
            
            <form action="{{ route('settings.update') }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                @if($user->role === 'admin')
                    <!-- ADMIN SETTINGS BLOCK -->
                    
                    <div class="border-b-2 border-black pb-4">
                        <h4 class="text-xs font-black uppercase tracking-wider text-black flex items-center gap-2">
                            <i class="fa-solid fa-building text-amber-600"></i> Corporate Workspace Tenancy
                        </h4>
                        <p class="text-[9px] text-slate-500 font-semibold mt-1">Updating these values automatically synchronizes all registered employees under this corporate umbrella.</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div class="space-y-2">
                            <label class="text-[9px] font-black uppercase tracking-widest text-slate-700 block">Company Name</label>
                            <input type="text" name="company_name" value="{{ old('company_name', $user->company_name) }}" required
                                class="w-full bg-[#F4ECE6] border border-black px-4 py-3 text-xs font-bold text-black shadow-[2.5px_2.5px_0px_0px_#000] focus:shadow-[4px_4px_0px_0px_#000] transition-all outline-none">
                        </div>

                        <div class="space-y-2">
                            <label class="text-[9px] font-black uppercase tracking-widest text-slate-700 block">Workspace Currency Symbol</label>
                            <select name="currency" required
                                class="w-full bg-[#F4ECE6] border border-black px-4 py-3 text-xs font-bold text-black shadow-[2.5px_2.5px_0px_0px_#000] focus:shadow-[4px_4px_0px_0px_#000] transition-all outline-none">
                                <option value="$" {{ old('currency', $user->currency) === '$' ? 'selected' : '' }}>$ (USD - Dollars)</option>
                                <option value="₹" {{ old('currency', $user->currency) === '₹' ? 'selected' : '' }}>₹ (INR - Rupees)</option>
                                <option value="€" {{ old('currency', $user->currency) === '€' ? 'selected' : '' }}>€ (EUR - Euros)</option>
                                <option value="£" {{ old('currency', $user->currency) === '£' ? 'selected' : '' }}>£ (GBP - Pounds)</option>
                                <option value="¥" {{ old('currency', $user->currency) === '¥' ? 'selected' : '' }}>¥ (JPY - Yen)</option>
                            </select>
                        </div>
                    </div>

                    <!-- PAYMENT GATEWAYS CONFIGURATION BLOCK -->
                    <div class="border-b-2 border-black pb-4 pt-6">
                        <h4 class="text-xs font-black uppercase tracking-wider text-black flex items-center gap-2">
                            <i class="fa-solid fa-credit-card text-emerald-600"></i> Payment Gateway Integrations
                        </h4>
                        <p class="text-[9px] text-slate-500 font-semibold mt-1">Configure your own Stripe and PayPal keys to clear salary payments. Leave blank to use server defaults.</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 pt-2">
                        <div class="space-y-2">
                            <label class="text-[9px] font-black uppercase tracking-widest text-slate-700 block">Stripe Publishable Key</label>
                            <input type="text" name="stripe_key" value="{{ old('stripe_key', $user->stripe_key) }}"
                                class="w-full bg-[#F4ECE6] border border-black px-4 py-3 text-xs font-bold text-black shadow-[2.5px_2.5px_0px_0px_#000] focus:shadow-[4px_4px_0px_0px_#000] transition-all outline-none"
                                placeholder="pk_test_...">
                        </div>

                        <div class="space-y-2">
                            <label class="text-[9px] font-black uppercase tracking-widest text-slate-700 block">Stripe Secret Key</label>
                            <input type="password" name="stripe_secret" value="{{ old('stripe_secret', $user->stripe_secret) }}"
                                class="w-full bg-[#F4ECE6] border border-black px-4 py-3 text-xs font-bold text-black shadow-[2.5px_2.5px_0px_0px_#000] focus:shadow-[4px_4px_0px_0px_#000] transition-all outline-none"
                                placeholder="sk_test_••••••••">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                        <div class="space-y-2 sm:col-span-1">
                            <label class="text-[9px] font-black uppercase tracking-widest text-slate-700 block">PayPal Mode</label>
                            <select name="paypal_mode"
                                class="w-full bg-[#F4ECE6] border border-black px-4 py-3 text-xs font-bold text-black shadow-[2.5px_2.5px_0px_0px_#000] focus:shadow-[4px_4px_0px_0px_#000] transition-all outline-none">
                                <option value="sandbox" {{ old('paypal_mode', $user->paypal_mode ?? 'sandbox') === 'sandbox' ? 'selected' : '' }}>Sandbox</option>
                                <option value="live" {{ old('paypal_mode', $user->paypal_mode) === 'live' ? 'selected' : '' }}>Live</option>
                            </select>
                        </div>

                        <div class="space-y-2 sm:col-span-1">
                            <label class="text-[9px] font-black uppercase tracking-widest text-slate-700 block">PayPal Client ID</label>
                            <input type="text" name="paypal_client_id" value="{{ old('paypal_client_id', $user->paypal_client_id) }}"
                                class="w-full bg-[#F4ECE6] border border-black px-4 py-3 text-xs font-bold text-black shadow-[2.5px_2.5px_0px_0px_#000] focus:shadow-[4px_4px_0px_0px_#000] transition-all outline-none"
                                placeholder="Client ID">
                        </div>

                        <div class="space-y-2 sm:col-span-1">
                            <label class="text-[9px] font-black uppercase tracking-widest text-slate-700 block">PayPal Client Secret</label>
                            <input type="password" name="paypal_client_secret" value="{{ old('paypal_client_secret', $user->paypal_client_secret) }}"
                                class="w-full bg-[#F4ECE6] border border-black px-4 py-3 text-xs font-bold text-black shadow-[2.5px_2.5px_0px_0px_#000] focus:shadow-[4px_4px_0px_0px_#000] transition-all outline-none"
                                placeholder="Client Secret">
                        </div>
                    </div>

                @endif

                <!-- SHARED SETTINGS: PERSONAL DETAILS & SECURITY -->
                <div class="border-b-2 border-black pb-4 pt-4">
                    <h4 class="text-xs font-black uppercase tracking-wider text-black flex items-center gap-2">
                        <i class="fa-solid fa-user-shield text-indigo-650"></i> Profile & Security Configuration
                    </h4>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div class="space-y-2">
                        <label class="text-[9px] font-black uppercase tracking-widest text-slate-700 block">Profile Display Name</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                            class="w-full bg-[#F4ECE6] border border-black px-4 py-3 text-xs font-bold text-black shadow-[2.5px_2.5px_0px_0px_#000] focus:shadow-[4px_4px_0px_0px_#000] transition-all outline-none">
                    </div>

                    <div class="space-y-2">
                        <label class="text-[9px] font-black uppercase tracking-widest text-slate-400 block">Registered Email Address (Read-Only)</label>
                        <input type="email" value="{{ $user->email }}" disabled
                            class="w-full bg-[#F4ECE6]/30 border border-slate-300 px-4 py-3 text-xs font-bold text-slate-500 cursor-not-allowed outline-none">
                    </div>
                </div>

                <!-- Passwords Security reset -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div class="space-y-2">
                        <label class="text-[9px] font-black uppercase tracking-widest text-slate-700 block">New Security Password (Optional)</label>
                        <input type="password" name="password" placeholder="••••••••"
                            class="w-full bg-[#F4ECE6] border border-black px-4 py-3 text-xs font-bold text-black shadow-[2.5px_2.5px_0px_0px_#000] focus:shadow-[4px_4px_0px_0px_#000] transition-all outline-none">
                    </div>

                    <div class="space-y-2">
                        <label class="text-[9px] font-black uppercase tracking-widest text-slate-700 block">Confirm Password</label>
                        <input type="password" name="password_confirmation" placeholder="••••••••"
                            class="w-full bg-[#F4ECE6] border border-black px-4 py-3 text-xs font-bold text-black shadow-[2.5px_2.5px_0px_0px_#000] focus:shadow-[4px_4px_0px_0px_#000] transition-all outline-none">
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit"
                        class="px-6 py-3 border-2 border-black font-black text-xs uppercase tracking-wider text-white bg-black hover:bg-neutral-900 shadow-[3px_3px_0px_0px_#22c55e] transition-all active:translate-x-[2px] active:translate-y-[2px] active:shadow-none cursor-pointer whitespace-nowrap">
                        <i class="fa-solid fa-circle-check text-green-400 mr-1.5"></i> Save Settings
                    </button>
                </div>

            </form>

        </div>

        <!-- Right 1 Column: Information Dossier Metadata Card -->
        <div class="space-y-6">
            
            <div class="border-2 border-black bg-white shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] p-6 space-y-4">
                <div class="w-16 h-16 bg-black flex items-center justify-center font-black text-xl text-white border-2 border-black shadow-[3px_3px_0px_0px_#f59e0b] select-none mx-auto mb-4">
                    {{ strtoupper(substr($user->name, 0, 2)) }}
                </div>
                <div class="text-center">
                    <h3 class="text-sm font-black text-black leading-tight">{{ $user->name }}</h3>
                    <p class="text-[9px] font-black text-indigo-650 uppercase tracking-widest mt-1">{{ $user->role === 'admin' ? 'Workspace Director' : 'Active Employee' }}</p>
                </div>

                <div class="border-t border-black/10 pt-4 space-y-2.5 text-[10px]">
                    <div class="flex justify-between border-b border-dashed border-slate-200 pb-1.5">
                        <span class="text-slate-500 font-bold uppercase tracking-wider">Active Workspace</span>
                        <strong class="font-extrabold text-black">{{ $user->company_name }}</strong>
                    </div>
                    @if($user->role === 'employee' && $user->employee_id)
                        <div class="flex justify-between border-b border-dashed border-slate-200 pb-1.5">
                            <span class="text-slate-500 font-bold uppercase tracking-wider">Employee Dossier ID</span>
                            <strong class="font-mono font-extrabold text-slate-800 bg-slate-100 px-1 border border-black text-[9px]">{{ $user->employee->employee_id ?? 'N/A' }}</strong>
                        </div>
                    @endif
                    <div class="flex justify-between">
                        <span class="text-slate-500 font-bold uppercase tracking-wider">Session Security Status</span>
                        <strong class="text-emerald-600 font-black uppercase tracking-wider flex items-center gap-1">
                            <span class="w-2 h-2 bg-emerald-500 border border-black inline-block animate-pulse"></span> Protected
                        </strong>
                    </div>
                </div>
            </div>

            <!-- Helpful notice box -->
            <div class="border-2 border-black bg-amber-50 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] p-5 space-y-2.5">
                <h4 class="text-[9px] font-black uppercase tracking-widest text-amber-800"><i class="fa-solid fa-lightbulb"></i> Tenancy Isolation System</h4>
                <p class="text-[9px] font-bold text-amber-900 leading-relaxed">
                    PayFlow utilizes dynamic Multi-Tenant Partitioning. Modifying workspace variables (such as currency keys or company titles) cascades instantly to all linked employee files to preserve strict payroll system alignment.
                </p>
            </div>

        </div>

    </div>

</div>
@endsection
