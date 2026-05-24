<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{
    /**
     * Display the settings workspace (context-aware for Admins & Employees).
     */
    public function index()
    {
        $user = Auth::user();
        
        // Compile comma-separated departments string if admin
        $deptsString = '';
        if ($user->role === 'admin' && is_array($user->departments)) {
            $deptsString = implode(', ', $user->departments);
        }

        return view('settings.index', compact('user', 'deptsString'));
    }

    /**
     * Process settings update.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            // Admin Workspace Settings Validation
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'company_name' => ['required', 'string', 'max:255'],
                'departments' => ['required', 'string'],
                'currency' => ['required', 'string', 'max:10'],
                'password' => ['nullable', 'string', 'min:6', 'confirmed'],
                'stripe_key' => ['nullable', 'string', 'max:255'],
                'stripe_secret' => ['nullable', 'string', 'max:255'],
                'paypal_client_id' => ['nullable', 'string', 'max:255'],
                'paypal_client_secret' => ['nullable', 'string', 'max:255'],
                'paypal_mode' => ['nullable', 'in:sandbox,live'],
            ]);

            $oldCompany = $user->company_name;
            $newCompany = trim($request->input('company_name'));
            $oldCurrency = $user->currency ?? '$';
            $newCurrency = $request->input('currency');
            
            // Extract and clean departments
            $deptsString = $request->input('departments');
            $deptsArray = array_filter(array_map('trim', explode(',', $deptsString)));

            // 1. Run Dynamic Currency Conversion Scaling across the workspace
            if ($oldCurrency !== $newCurrency) {
                // Conversion rates relative to USD base ($ = 1.0)
                $rates = [
                    '$' => 1.0,      // USD
                    '₹' => 83.0,     // INR
                    '€' => 0.92,     // EUR
                    '£' => 0.79,     // GBP
                    '¥' => 155.0     // JPY
                ];

                if (isset($rates[$oldCurrency]) && isset($rates[$newCurrency])) {
                    $multiplier = $rates[$newCurrency] / $rates[$oldCurrency];

                    // Scale Employee Base Salaries
                    $employees = Employee::where('company_name', $oldCompany)->get();
                    foreach ($employees as $employee) {
                        $employee->salary = round($employee->salary * $multiplier, 2);
                        $employee->save();
                    }

                    // Scale Historical Payroll Records
                    $payrolls = \App\Models\Payroll::whereHas('employee', function($query) use ($oldCompany) {
                        $query->where('company_name', $oldCompany);
                    })->get();
                    foreach ($payrolls as $payroll) {
                        $payroll->basic_salary = round($payroll->basic_salary * $multiplier, 2);
                        $payroll->bonus = round($payroll->bonus * $multiplier, 2);
                        $payroll->deductions = round($payroll->deductions * $multiplier, 2);
                        $payroll->net_salary = round($payroll->net_salary * $multiplier, 2);
                        $payroll->save();
                    }

                    // Scale Bank Ledger Transaction Records
                    $transactions = \App\Models\Transaction::whereHas('payroll.employee', function($query) use ($oldCompany) {
                        $query->where('company_name', $oldCompany);
                    })->get();
                    foreach ($transactions as $transaction) {
                        $transaction->amount = round($transaction->amount * $multiplier, 2);
                        $transaction->save();
                    }
                }
            }

            // 2. Update SQLite Admin User details
            $user->name = $request->input('name');
            $user->company_name = $newCompany;
            $user->currency = $newCurrency;
            $user->departments = array_values($deptsArray);
            
            // Save Stripe & PayPal details
            $user->stripe_key = $request->input('stripe_key');
            $user->stripe_secret = $request->input('stripe_secret');
            $user->paypal_client_id = $request->input('paypal_client_id');
            $user->paypal_client_secret = $request->input('paypal_client_secret');
            $user->paypal_mode = $request->input('paypal_mode', 'sandbox');

            if ($request->filled('password')) {
                $user->password = bcrypt($request->input('password'));
            }
            $user->save();

            // 2. Cascade company name changes to maintain tenant separation integrity
            if ($oldCompany !== $newCompany) {
                // Update all user credentials records
                User::where('company_name', $oldCompany)
                    ->update(['company_name' => $newCompany]);
                
                // Update all employee dossier records
                Employee::where('company_name', $oldCompany)
                    ->update(['company_name' => $newCompany]);
            }

            // Reset dynamic currency caches on any company or currency updates
            if ($oldCompany !== $newCompany || $oldCurrency !== $newCurrency) {
                cache()->forget("company_currency_{$oldCompany}");
                cache()->forget("company_currency_{$newCompany}");
            }

            return redirect()->route('settings.index')->with('success', 'Workspace configuration updated successfully!');

        } else {
            // Employee Account Settings Validation
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'password' => ['nullable', 'string', 'min:6', 'confirmed'],
            ]);

            // 1. Update SQLite Employee User
            $user->name = $request->input('name');
            if ($request->filled('password')) {
                $user->password = bcrypt($request->input('password'));
            }
            $user->save();

            // 2. Update linked Employee profile name
            if ($user->employee_id) {
                $employee = Employee::find($user->employee_id);
                if ($employee) {
                    $employee->name = $request->input('name');
                    $employee->save();
                }
            }

            return redirect()->route('settings.index')->with('success', 'Account credentials updated successfully!');
        }
    }
}
