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
            'password' => bcrypt('password'),
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
            'password' => bcrypt('password'),
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

        // 6. Attempt login with uppercase email to verify case-insensitivity
        $this->post('/logout');
        $response3 = $this->post('/login', [
            'email' => 'JOHN@COMPANY.COM',
            'password' => 'password',
            'role' => 'employee',
            'company_name' => 'Test Company',
        ]);
        $response3->assertRedirect('/dashboard');

        // 7. Attempt login with mixed-case company name to verify case-insensitivity
        $this->post('/logout');
        $response4 = $this->post('/login', [
            'email' => 'john@company.com',
            'password' => 'password',
            'role' => 'employee',
            'company_name' => 'tEsT cOmPaNy',
        ]);
        $response4->assertRedirect('/dashboard');
    }

    public function test_employee_creation_with_custom_password(): void
    {
        // Create an Admin user representing the company
        $admin = \App\Models\User::create([
            'name' => 'Admin User',
            'email' => 'admin@company.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'company_name' => 'Test Company',
        ]);

        $this->actingAs($admin);

        $response = $this->post(route('employees.store'), [
            'name' => 'Employee Custom',
            'email' => 'custom@company.com',
            'phone' => '1234567890',
            'department' => 'HR',
            'designation' => 'Recruiter',
            'salary' => 4500,
            'join_date' => '2024-02-01',
            'password' => 'customsecurepassword',
        ]);

        $response->assertRedirect(route('employees.index'));
        $this->assertDatabaseHas('employees', [
            'email' => 'custom@company.com',
            'company_name' => 'Test Company',
        ]);

        $user = \App\Models\User::where('email', 'custom@company.com')->first();
        $this->assertNotNull($user);
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('customsecurepassword', $user->password));
    }

    public function test_employee_update_with_custom_password(): void
    {
        // Create an Admin user representing the company
        $admin = \App\Models\User::create([
            'name' => 'Admin User',
            'email' => 'admin@company.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'company_name' => 'Test Company',
        ]);

        $this->actingAs($admin);

        $employee = \App\Models\Employee::create([
            'employee_id' => 'EMP-9002',
            'name' => 'John Doe',
            'email' => 'john@company.com',
            'department' => 'Engineering',
            'designation' => 'Software Engineer',
            'salary' => 5000,
            'join_date' => '2024-01-01',
            'status' => 'Active',
            'company_name' => 'Test Company',
        ]);

        $empUser = \App\Models\User::create([
            'name' => 'John Doe',
            'email' => 'john@company.com',
            'password' => bcrypt('password'),
            'role' => 'employee',
            'company_name' => 'Test Company',
            'employee_id' => $employee->id,
        ]);

        // 1. Update details WITHOUT changing password
        $response1 = $this->put(route('employees.update', $employee->id), [
            'name' => 'John Modified',
            'email' => 'john@company.com',
            'phone' => '1234567890',
            'department' => 'Engineering',
            'designation' => 'Senior Software Engineer',
            'salary' => 6000,
            'join_date' => '2024-01-01',
            'status' => 'Active',
        ]);

        $response1->assertRedirect(route('employees.show', $employee->id));
        $empUser->refresh();
        $this->assertEquals('John Modified', $empUser->name);
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('password', $empUser->password));

        // 2. Update details WITH custom password
        $response2 = $this->put(route('employees.update', $employee->id), [
            'name' => 'John Modified',
            'email' => 'john@company.com',
            'phone' => '1234567890',
            'department' => 'Engineering',
            'designation' => 'Senior Software Engineer',
            'salary' => 6000,
            'join_date' => '2024-01-01',
            'status' => 'Active',
            'password' => 'newsecurepassword',
        ]);

        $response2->assertRedirect(route('employees.show', $employee->id));
        $empUser->refresh();
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('newsecurepassword', $empUser->password));
    }

    public function test_employee_status_toggling_and_restricted_login(): void
    {
        // 1. Create an Admin user representing the company
        $admin = \App\Models\User::create([
            'name' => 'Admin User',
            'email' => 'admin@company.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'company_name' => 'Test Company',
        ]);

        $this->actingAs($admin);

        // 2. Create an Employee in the directory
        $employee = \App\Models\Employee::create([
            'employee_id' => 'EMP-9005',
            'name' => 'John Doe',
            'email' => 'john@company.com',
            'department' => 'Engineering',
            'designation' => 'Software Engineer',
            'salary' => 5000,
            'join_date' => '2024-01-01',
            'status' => 'Active',
            'company_name' => 'Test Company',
        ]);

        $empUser = \App\Models\User::create([
            'name' => 'John Doe',
            'email' => 'john@company.com',
            'password' => bcrypt('password'),
            'role' => 'employee',
            'company_name' => 'Test Company',
            'employee_id' => $employee->id,
        ]);

        // 3. Toggle Status to Inactive as Admin
        $response1 = $this->patch(route('employees.toggle-status', $employee->id));
        $response1->assertRedirect();
        
        $employee->refresh();
        $this->assertEquals('Inactive', $employee->status);

        // 4. Log out Admin
        $this->post('/logout');

        // 5. Attempt login as Inactive Employee
        $response2 = $this->post('/login', [
            'email' => 'john@company.com',
            'password' => 'password',
            'role' => 'employee',
            'company_name' => 'Test Company',
        ]);

        $response2->assertSessionHasErrors(['email']);
        $this->assertEquals(
            'Your account is currently inactive. Please contact your administrator.',
            session('errors')->first('email')
        );

        // 6. Log back in as Admin and Toggle back to Active
        $this->actingAs($admin);
        $response3 = $this->patch(route('employees.toggle-status', $employee->id));
        $response3->assertRedirect();
        
        $employee->refresh();
        $this->assertEquals('Active', $employee->status);

        // 7. Log out Admin
        $this->post('/logout');

        // 8. Attempt login again as now Active Employee
        $response4 = $this->post('/login', [
            'email' => 'john@company.com',
            'password' => 'password',
            'role' => 'employee',
            'company_name' => 'Test Company',
        ]);
        $response4->assertRedirect('/dashboard');
    }
}
