<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Services\MongoDBService;

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

        // 1. Sync / fetch from MongoDB
        $mongoService = new MongoDBService();
        $mongoUsers = $mongoService->selectCollection('users');
        $mongoUser = $mongoUsers->findOne(['email' => $email]);

        if (!$mongoUser) {
            // Check if user is in SQLite to auto-migrate to MongoDB (e.g. seeded admin)
            $sqliteUser = User::where('email', $email)->first();
            if ($sqliteUser) {
                $this->syncUserToMongoDB($sqliteUser);
                $mongoUser = $mongoUsers->findOne(['email' => $email]);
            }
        }

        // 2. If user exists in MongoDB, verify role and credentials
        if ($mongoUser) {
            if (($mongoUser['role'] ?? '') !== $role) {
                $roleLabel = $role === 'admin' ? 'Admin (Boss)' : 'Employee';
                return back()->withErrors([
                    'email' => "This email is not registered as an {$roleLabel}.",
                ])->onlyInput('email');
            }

            // Verify password against MongoDB hashed password
            if (Hash::check($password, $mongoUser['password'])) {
                // Ensure local SQLite copy exists and matches
                $localUser = $this->syncUserFromMongoDB($email);
                
                if ($localUser && Auth::loginUsingId($localUser->id, $request->has('remember'))) {
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

        // Check if email already exists in MongoDB
        $mongoService = new MongoDBService();
        $mongoUsers = $mongoService->selectCollection('users');
        $mongoUser = $mongoUsers->findOne(['email' => $request->input('email')]);

        if ($mongoUser) {
            return back()->withErrors([
                'email' => 'The email has already been taken in MongoDB records.',
            ])->onlyInput('email');
        }

        // 2. Process based on role
        if ($role === 'admin') {
            $deptsString = $request->input('departments');
            $deptsArray = array_filter(array_map('trim', explode(',', $deptsString)));

            $user = User::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'password' => Hash::make($request->input('password')),
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
                'password' => Hash::make($request->input('password')),
                'role' => 'employee',
                'company_name' => $employee->company_name,
                'employee_id' => $employee->id,
            ]);
        }

        // 3. Sync to MongoDB
        $this->syncUserToMongoDB($user);

        // 4. Log user in directly
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

    /**
     * Sync user details TO MongoDB users collection.
     */
    protected function syncUserToMongoDB(User $user)
    {
        try {
            $mongoService = new MongoDBService();
            $mongoUsers = $mongoService->selectCollection('users');
            
            $existing = $mongoUsers->findOne(['email' => $user->email]);
            
            $data = [
                'name' => $user->name,
                'email' => $user->email,
                'password' => $user->password,
                'role' => $user->role,
                'company_name' => $user->company_name,
                'employee_id' => $user->employee_id,
                'departments' => $user->departments,
                'currency' => $user->currency ?? '$',
                'updated_at' => now()->toIso8601String(),
            ];
            
            if ($existing) {
                $mongoUsers->updateOne(['email' => $user->email], ['$set' => $data]);
            } else {
                $data['created_at'] = now()->toIso8601String();
                $mongoUsers->insertOne($data);
            }
        } catch (\Exception $e) {
            \Log::error('MongoDB User Sync Failed: ' . $e->getMessage());
        }
    }

    /**
     * Fetch user from MongoDB and sync to SQLite database local copy.
     */
    protected function syncUserFromMongoDB(string $email): ?User
    {
        try {
            $mongoService = new MongoDBService();
            $mongoUsers = $mongoService->selectCollection('users');
            
            $mongoUser = $mongoUsers->findOne(['email' => $email]);
            if ($mongoUser) {
                // Find or create in SQLite
                $user = User::where('email', $email)->first();
                if (!$user) {
                    $user = new User();
                }
                
                $user->name = $mongoUser['name'] ?? 'User';
                $user->email = $mongoUser['email'];
                $user->password = $mongoUser['password'];
                $user->role = $mongoUser['role'] ?? 'employee';
                $user->company_name = $mongoUser['company_name'] ?? null;
                $user->employee_id = $mongoUser['employee_id'] ?? null;
                $user->departments = $mongoUser['departments'] ?? null;
                $user->currency = $mongoUser['currency'] ?? '$';
                $user->save();
                
                return $user;
            }
        } catch (\Exception $e) {
            \Log::error('MongoDB User Fetch Failed: ' . $e->getMessage());
        }
        return null;
    }
}
