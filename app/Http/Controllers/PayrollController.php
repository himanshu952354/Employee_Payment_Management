<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Payroll;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class PayrollController extends Controller
{
    /**
     * Display payroll list for a specific month.
     */
    public function index(Request $request)
    {
        $month = $request->filled('month') ? $request->month : Carbon::now()->format('Y-m');

        $employees = Employee::where('company_name', auth()->user()->company_name)
            ->where('status', 'Active')
            ->orderBy('name', 'asc')
            ->get();

        $employeeIds = $employees->pluck('id');

        // Fetch generated payrolls for this month
        $payrolls = Payroll::where('month', $month)
            ->whereIn('employee_id', $employeeIds)
            ->get()
            ->keyBy('employee_id');

        // Statistics
        $totalProcessedCount = Payroll::where('month', $month)->whereIn('employee_id', $employeeIds)->count();
        $totalPaidCount = Payroll::where('month', $month)->whereIn('employee_id', $employeeIds)->where('status', 'Paid')->count();
        $totalExpenses = Payroll::where('month', $month)->whereIn('employee_id', $employeeIds)->where('status', 'Paid')->sum('net_salary');
        $totalPendingExpenses = Payroll::where('month', $month)->whereIn('employee_id', $employeeIds)->where('status', 'Pending')->sum('net_salary');

        return view('payroll.index', compact(
            'employees',
            'month',
            'payrolls',
            'totalProcessedCount',
            'totalPaidCount',
            'totalExpenses',
            'totalPendingExpenses'
        ));
    }

    /**
     * Bulk generate pending payroll records for a specific month.
     */
    public function generateMonth(Request $request)
    {
        $request->validate([
            'month' => 'required|string|regex:/^\d{4}-\d{2}$/',
        ]);

        $month = $request->month;
        $activeEmployees = Employee::where('company_name', auth()->user()->company_name)
            ->where('status', 'Active')
            ->get();
        $generatedCount = 0;

        foreach ($activeEmployees as $employee) {
            // Check if payroll already exists
            $exists = Payroll::where('employee_id', $employee->id)
                ->where('month', $month)
                ->exists();

            if (!$exists) {
                Payroll::create([
                    'employee_id' => $employee->id,
                    'month' => $month,
                    'basic_salary' => $employee->salary,
                    'bonus' => 0.00,
                    'deductions' => 0.00,
                    'net_salary' => $employee->salary,
                    'status' => 'Pending',
                ]);
                $generatedCount++;
            }
        }

        return redirect()->route('payroll.index', ['month' => $month])
            ->with('success', "Successfully generated {$generatedCount} pending payroll records for month: " . Carbon::parse($month . '-01')->format('F Y'));
    }

    /**
     * Update individual payroll metrics (bonus & deductions).
     */
    public function updateSingle(Request $request, Payroll $payroll)
    {
        if ($payroll->employee->company_name !== auth()->user()->company_name) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'bonus' => 'required|numeric|min:0',
            'deductions' => 'required|numeric|min:0',
        ]);

        $netSalary = $payroll->basic_salary + $request->bonus - $request->deductions;

        $payroll->update([
            'bonus' => $request->bonus,
            'deductions' => $request->deductions,
            'net_salary' => $netSalary,
        ]);

        return redirect()->route('payroll.index', ['month' => $payroll->month])
            ->with('success', "Payroll updated for " . $payroll->employee->name);
    }

    /**
     * Process transaction payout simulation.
     */
    public function processPayment(Request $request, Payroll $payroll)
    {
        if ($payroll->employee->company_name !== auth()->user()->company_name) {
            abort(403, 'Unauthorized access.');
        }

        if ($payroll->status === 'Paid') {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'This payroll is already marked as paid.'], 422);
            }
            return redirect()->back()->with('error', 'This payroll is already marked as paid.');
        }

        $request->validate([
            'payment_method' => 'required|string|in:Bank Transfer,PayPal,Stripe,Cash',
            'notes' => 'nullable|string',
        ]);

        $txnNumber = 'TXN-' . strtoupper(Str::random(12));

        // Format currency symbols to ISO 3-letter codes for payment gateways
        $currencySymbolsMap = [
            '$' => 'usd',
            '₹' => 'inr',
            '€' => 'eur',
            '£' => 'gbp',
            '¥' => 'jpy'
        ];
        $currencySymbol = auth()->user()->currency ?? '$';
        $isoCurrency = $currencySymbolsMap[$currencySymbol] ?? 'usd';

        // 1. Process Stripe API Gateway if configured
        if ($request->payment_method === 'Stripe') {
            $secretKey = env('STRIPE_SECRET');
            if ($secretKey && $secretKey !== 'sk_test_placeholder') {
                try {
                    $response = Http::withoutVerifying()
                        ->asForm()
                        ->withBasicAuth($secretKey, '')
                        ->post('https://api.stripe.com/v1/charges', [
                            'amount' => intval($payroll->net_salary * 100), // in cents
                            'currency' => $isoCurrency,
                            'source' => 'tok_visa', // simulated card token
                            'description' => 'Salary payout clearance for ' . $payroll->employee->name . ' (' . $payroll->month . ')',
                        ]);
                    
                    if ($response->failed()) {
                        $error = $response->json()['error']['message'] ?? 'Stripe gateway transaction rejected.';
                        if ($request->ajax() || $request->wantsJson()) {
                            return response()->json(['success' => false, 'message' => "Stripe API Error: " . $error], 422);
                        }
                        return redirect()->back()->with('error', "Stripe API Error: " . $error);
                    }
                    
                    $stripeData = $response->json();
                    $txnNumber = $stripeData['id'] ?? $txnNumber;
                } catch (\Exception $e) {
                    if ($request->ajax() || $request->wantsJson()) {
                        return response()->json(['success' => false, 'message' => "Stripe Connection Error: " . $e->getMessage()], 500);
                    }
                    return redirect()->back()->with('error', "Stripe Connection Error: " . $e->getMessage());
                }
            }
        }

        // 2. Process PayPal REST Payouts API if configured
        if ($request->payment_method === 'PayPal') {
            $paypalId = env('PAYPAL_CLIENT_ID');
            $paypalSecret = env('PAYPAL_CLIENT_SECRET');
            $mode = env('PAYPAL_MODE', 'sandbox');
            
            if ($paypalId && $paypalId !== 'client_id_placeholder' && $paypalSecret && $paypalSecret !== 'client_secret_placeholder') {
                try {
                    $baseUrl = $mode === 'live' 
                        ? 'https://api-m.paypal.com' 
                        : 'https://api-m.sandbox.paypal.com';
                        
                    // Fetch OAuth Token
                    $tokenResponse = Http::withoutVerifying()
                        ->asForm()
                        ->withBasicAuth($paypalId, $paypalSecret)
                        ->post("{$baseUrl}/v1/oauth2/token", [
                            'grant_type' => 'client_credentials'
                        ]);
                        
                    if ($tokenResponse->failed()) {
                        if ($request->ajax() || $request->wantsJson()) {
                            return response()->json(['success' => false, 'message' => 'PayPal Authentication Failure.'], 422);
                        }
                        return redirect()->back()->with('error', 'PayPal Authentication Failure.');
                    }
                    
                    $accessToken = $tokenResponse->json()['access_token'];
                    
                    // Dispatch Payout
                    $payoutResponse = Http::withoutVerifying()
                        ->withToken($accessToken)
                        ->post("{$baseUrl}/v1/payments/payouts", [
                            'sender_batch_header' => [
                                'sender_batch_id' => 'BATCH_' . time() . '_' . $payroll->id,
                                'email_subject' => 'Salary Payout Cleared!',
                                'email_message' => 'You have received your monthly salary payout.'
                            ],
                            'items' => [
                                [
                                    'recipient_type' => 'EMAIL',
                                    'amount' => [
                                        'value' => number_format($payroll->net_salary, 2, '.', ''),
                                        'currency' => strtoupper($isoCurrency)
                                    ],
                                    'note' => 'Payroll disbursement: ' . $payroll->month,
                                    'receiver' => strtolower(str_replace(' ', '', $payroll->employee->name)) . '@payflow.com',
                                    'sender_item_id' => 'ITEM_' . $payroll->id
                                ]
                            ]
                        ]);
                        
                    if ($payoutResponse->failed()) {
                        $errorDetail = $payoutResponse->json()['message'] ?? 'PayPal payouts failed.';
                        
                        // GRACEFUL SANDBOX COMPLIANCE FALLBACK FOR INDIAN / REGION-BLOCKED MERCHANT PROFILES
                        if ($mode === 'sandbox' && (str_contains(strtolower($errorDetail), 'country') || str_contains(strtolower($errorDetail), 'allowed to send') || str_contains(strtolower($errorDetail), 'not allowed'))) {
                            // Automatically switch to simulated vault clearance to bypass local Reserve Bank / PayPal Sandbox regional restrictions
                            $txnNumber = 'MOCK_PAYPAL_' . strtoupper(bin2hex(random_bytes(8)));
                        } else {
                            if ($request->ajax() || $request->wantsJson()) {
                                return response()->json(['success' => false, 'message' => 'PayPal REST API Error: ' . $errorDetail], 422);
                            }
                            return redirect()->back()->with('error', 'PayPal REST API Error: ' . $errorDetail);
                        }
                    } else {
                        $payoutData = $payoutResponse->json();
                        $txnNumber = $payoutData['batch_header']['payout_batch_id'] ?? $txnNumber;
                    }
                } catch (\Exception $e) {
                    if ($request->ajax() || $request->wantsJson()) {
                        return response()->json(['success' => false, 'message' => "PayPal Connection Error: " . $e->getMessage()], 500);
                    }
                    return redirect()->back()->with('error', "PayPal Connection Error: " . $e->getMessage());
                }
            }
        }

        // 3. Create success Transaction record
        Transaction::create([
            'payroll_id' => $payroll->id,
            'transaction_number' => $txnNumber,
            'amount' => $payroll->net_salary,
            'payment_method' => $request->payment_method,
            'status' => 'Success',
            'notes' => $request->notes ?? ('Automated payroll disbursement for ' . Carbon::parse($payroll->month . '-01')->format('F Y')),
        ]);

        // 4. Update Payroll record status
        $payroll->update([
            'status' => 'Paid',
            'processed_at' => Carbon::now(),
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Salary payment processed successfully via {$request->payment_method}.",
                'txn_number' => $txnNumber,
                'amount' => number_format($payroll->net_salary, 2),
                'employee_name' => $payroll->employee->name
            ]);
        }

        return redirect()->route('payroll.index', ['month' => $payroll->month])
            ->with('success', "Salary payment processed successfully for {$payroll->employee->name}. Txn ID: {$txnNumber}");
    }

    /**
     * Display printable salary slip.
     */
    public function slip(Payroll $payroll)
    {
        $user = auth()->user();
        if ($user->role === 'admin' && $payroll->employee->company_name !== $user->company_name) {
            abort(403, 'Unauthorized access to salary slip.');
        }
        if ($user->role === 'employee' && $payroll->employee_id !== $user->employee_id) {
            abort(403, 'Unauthorized access to salary slip.');
        }

        $payroll->load(['employee', 'transaction']);
        return view('payroll.slip', compact('payroll'));
    }
}
