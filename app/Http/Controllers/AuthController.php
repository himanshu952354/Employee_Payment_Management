<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
        return view('landing');
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
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
            'role' => ['required', 'in:admin,employee'],
        ]);

        $role = $request->input('role');
        $email = $request->input('email');
        $password = $request->input('password');

        // Verify role and credentials in SQLite directly
        $user = User::where('email', $email)->first();

        if ($user) {
            if ($user->role !== $role) {
                $roleLabel = $role === 'admin' ? 'Admin (Boss)' : 'Employee';
                return back()->withErrors([
                    'email' => "This email is not registered as an {$roleLabel}.",
                ])->onlyInput('email');
            }

            // Verify password against SQLite hashed password
            if (Hash::check($password, $user->password)) {
                if (Auth::loginUsingId($user->id, $request->has('remember'))) {
                    $request->session()->regenerate();
                    
                    $successMsg = Auth::user()->role === 'admin'
                        ? "Welcome back to your workspace command center, Admin!"
                        : "Welcome to your personal self-service portal, " . Auth::user()->name . "!";
                        
                    return redirect()->route('dashboard')->with('success', $successMsg);
                }
            }
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our database records.',
        ])->onlyInput('email');
    }

    /**
     * Handle registration of a new user account (Admin or Employee).
     */
    public function signup(Request $request)
    {
        $role = $request->input('role');

        // 1. Basic validation rules
        $rules = [
            'role' => ['required', 'in:admin,employee'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6'],
        ];

        // Admin requires company name, employee doesn't
        if ($role === 'admin') {
            $rules['company_name'] = ['required', 'string', 'max:255'];
            $rules['departments'] = ['required', 'string'];
            $rules['currency'] = ['required', 'string', 'max:10'];
        }

        $request->validate($rules);

        // 2. Process based on role
        if ($role === 'admin') {
            $deptsString = $request->input('departments');
            $deptsArray = array_filter(array_map('trim', explode(',', $deptsString)));

            $user = User::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'password' => $request->input('password'),
                'role' => 'admin',
                'company_name' => $request->input('company_name'),
                'departments' => array_values($deptsArray),
                'currency' => $request->input('currency', '$'),
            ]);
        } else {
            // Check if employee email already exists in directory to link profiles
            $employee = Employee::where('email', $request->input('email'))->first();

            if (!$employee) {
                // Generate a mock profile so employee can immediately test their personal dashboard stubs
                $employee = Employee::create([
                    'employee_id' => 'EMP-' . rand(1008, 9999),
                    'name' => $request->input('name'),
                    'email' => $request->input('email'),
                    'phone' => '+91 99999 00000',
                    'department' => 'Engineering',
                    'designation' => 'Software Engineer',
                    'salary' => 5000.00,
                    'bank_name' => 'State Bank of India',
                    'account_number' => 'ACC' . rand(1000000000, 9999999999),
                    'join_date' => now()->toDateString(),
                    'status' => 'Active',
                    'company_name' => 'PayFlow Enterprise',
                ]);
            }

            $user = User::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'password' => $request->input('password'),
                'role' => 'employee',
                'company_name' => $employee->company_name,
                'employee_id' => $employee->id,
            ]);
        }

        // 3. Log user in directly
        Auth::login($user);
        $request->session()->regenerate();

        $welcomeMsg = $user->role === 'admin'
            ? "Welcome to PayFlow! Your workspace '{$user->company_name}' is set up."
            : "Welcome to PayFlow! Your personal self-service portal is set up.";

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
