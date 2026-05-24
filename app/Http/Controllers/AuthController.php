<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Show the dynamic landing page.
     */
    public function showLanding()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        $companies = User::where('role', 'admin')->distinct()->pluck('company_name');
        return view('landing', compact('companies'));
    }

    /**
     * Show the login form (legacy route fallback, redirects to landing page modals).
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return redirect()->route('landing', ['trigger' => 'login']);
    }

    /**
     * Handle authentication attempt (supporting role verification).
     */
    public function login(Request $request)
    {
        $role = $request->input('role');

        $rules = [
            'email' => ['required', 'email'],
            'password' => ['required'],
            'role' => ['required', 'in:admin,employee'],
        ];

        if ($role === 'employee') {
            $rules['company_name'] = ['required', 'string'];
        }

        $request->validate($rules);

        $email = strtolower(trim($request->input('email')));
        $password = $request->input('password');

        Log::info('Login attempt initiated', [
            'role' => $role,
            'email' => $email,
            'company_name' => $request->input('company_name') ? trim($request->input('company_name')) : null,
        ]);

        if ($role === 'admin') {
            // Verify role and credentials in a case-insensitive manner
            $user = User::whereRaw('LOWER(email) = ?', [$email])
                ->where('role', 'admin')
                ->first();

            if (!$user) {
                Log::warning('Admin login failed: Email not registered as admin', ['email' => $email]);
                return back()->withErrors([
                    'email' => "This email is not registered as an Admin.",
                ])->onlyInput('email');
            }
        } else {
            $companyName = trim($request->input('company_name'));

            // 1. Strict Tenant validation in employee directory (case-insensitive email and company name)
            $employee = Employee::whereRaw('LOWER(email) = ?', [$email])
                ->whereRaw('LOWER(company_name) = ?', [strtolower($companyName)])
                ->first();

            if (!$employee) {
                Log::warning('Employee login failed: Not registered in company', [
                    'email' => $email,
                    'company_name' => $companyName,
                ]);
                return back()->withErrors([
                    'email' => "You are not registered in this company.",
                ])->onlyInput('email');
            }

            if ($employee->status !== 'Active') {
                Log::warning('Employee login failed: Account inactive', [
                    'email' => $email,
                    'company_name' => $companyName,
                    'status' => $employee->status,
                ]);
                return back()->withErrors([
                    'email' => "Your account is currently inactive. Please contact your administrator.",
                ])->onlyInput('email');
            }

            // 2. Retrieve corresponding User credentials (case-insensitive email and company name)
            $user = User::whereRaw('LOWER(email) = ?', [$email])
                ->where('role', 'employee')
                ->whereRaw('LOWER(company_name) = ?', [strtolower($companyName)])
                ->first();

            if (!$user) {
                Log::warning('Employee login failed: Employee directory record exists, but User credentials record not found', [
                    'email' => $email,
                    'company_name' => $companyName,
                    'employee_id' => $employee->id,
                ]);
                return back()->withErrors([
                    'email' => "Employee credentials not found. Please contact your administrator.",
                ])->onlyInput('email');
            }
        }

        // Verify password against SQLite hashed password
        if (Hash::check($password, $user->password)) {
            if (Auth::loginUsingId($user->id, $request->has('remember'))) {
                $request->session()->regenerate();
                
                $successMsg = Auth::user()->role === 'admin'
                    ? "Welcome back to your workspace command center, Admin!"
                    : "Welcome to your personal self-service portal, " . Auth::user()->name . "!";
                
                Log::info('Login successful', [
                    'user_id' => $user->id,
                    'role' => $user->role,
                    'company_name' => $user->company_name,
                ]);
                    
                return redirect()->route('dashboard')->with('success', $successMsg);
            }
        }

        Log::warning('Login failed: Password hash mismatch', [
            'email' => $email,
            'role' => $user->role,
            'company_name' => $user->company_name,
        ]);

        return back()->withErrors([
            'email' => 'The provided credentials do not match our database records.',
        ])->onlyInput('email');
    }

    /**
     * Handle registration of a new user account (Admin only).
     */
    public function signup(Request $request)
    {
        $role = $request->input('role');

        if ($role !== 'admin') {
            return back()->withErrors([
                'role' => 'Employee registration is disabled. Please contact your company administrator.',
            ]);
        }

        // 1. Basic validation rules
        $rules = [
            'role' => ['required', 'in:admin'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6'],
            'company_name' => ['required', 'string', 'max:255'],
            'departments' => ['required', 'string'],
            'currency' => ['required', 'string', 'max:10'],
        ];

        $request->merge([
            'email' => strtolower(trim($request->input('email'))),
            'company_name' => trim($request->input('company_name')),
        ]);

        $request->validate($rules);

        $deptsString = $request->input('departments');
        $deptsArray = array_filter(array_map('trim', explode(',', $deptsString)));

        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
            'role' => 'admin',
            'company_name' => $request->input('company_name'),
            'departments' => array_values($deptsArray),
            'currency' => $request->input('currency', '$'),
        ]);

        // 3. Log user in directly
        Auth::login($user);
        $request->session()->regenerate();

        $welcomeMsg = "Welcome to PayFlow! Your workspace '{$user->company_name}' is set up.";

        return redirect()->route('dashboard')->with('success', $welcomeMsg);
    }

    /**
     * Log the user out.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('landing')->with('success', 'Workspace session ended successfully.');
    }
}
