<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_employee_restricted_login(): void
    {
        // 1. Create an Admin user representing the company
        $admin = \App\Models\User::create([
            'name' => 'Admin User',
            'email' => 'admin@company.com',
            'password' => 'password',
            'role' => 'admin',
            'company_name' => 'Test Company',
        ]);

        // 2. Create an Employee in the directory
        $employee = \App\Models\Employee::create([
            'employee_id' => 'EMP-9001',
            'name' => 'John Doe',
            'email' => 'john@company.com',
            'department' => 'Engineering',
            'designation' => 'Software Engineer',
            'salary' => 5000,
            'join_date' => '2024-01-01',
            'status' => 'Active',
            'company_name' => 'Test Company',
        ]);

        // 3. Create the Employee User account
        $empUser = \App\Models\User::create([
            'name' => 'John Doe',
            'email' => 'john@company.com',
            'password' => 'password',
            'role' => 'employee',
            'company_name' => 'Test Company',
            'employee_id' => $employee->id,
        ]);

        // 4. Attempt login with correct email/password but WRONG company name
        $response1 = $this->post('/login', [
            'email' => 'john@company.com',
            'password' => 'password',
            'role' => 'employee',
            'company_name' => 'Wrong Company',
        ]);
        $response1->assertSessionHasErrors(['email']);
        $this->assertEquals(
            'You are not registered in this company.',
            session('errors')->first('email')
        );

        // 5. Attempt login with correct email/password and CORRECT company name
        $response2 = $this->post('/login', [
            'email' => 'john@company.com',
            'password' => 'password',
            'role' => 'employee',
            'company_name' => 'Test Company',
        ]);
        $response2->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($empUser);
    }
}
