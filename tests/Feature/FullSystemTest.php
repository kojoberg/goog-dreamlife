<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Patient;
use App\Models\Product;
use App\Models\Branch;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FullSystemTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    public function setUp(): void
    {
        parent::setUp();
        // Setup shared data
        $this->admin = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin@system.com',
            'name' => 'System Admin'
        ]);

        Branch::create(['name' => 'Main Branch', 'is_main' => true]);

        // Open Shift
        \App\Models\Shift::create([
            'user_id' => $this->admin->id,
            'start_time' => now(),
            'starting_cash' => 100
        ]);
    }

    /** @test */
    public function auth_pages_load_correctly()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Sign In');
    }

    /** @test */
    public function dashboard_accessible_to_admin()
    {
        $response = $this->actingAs($this->admin)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Dashboard');
    }

    /** @test */
    public function patient_management_flow()
    {
        // 1. Visit Create Page
        $response = $this->actingAs($this->admin)->get(route('patients.create'));
        $response->assertStatus(200);
        $response->assertSee('Add New Patient');

        // 2. Store Patient
        $response = $this->actingAs($this->admin)->post(route('patients.store'), [
            'name' => 'Test Patient',
            'phone' => '0500000000',
            'email' => 'test@patient.com',
            'address' => 'Test Address',
        ]);
        $response->assertRedirect(route('patients.index'));
        $this->assertDatabaseHas('patients', ['email' => 'test@patient.com']);

        // 3. Edit Patient
        $patient = Patient::where('email', 'test@patient.com')->first();
        $response = $this->actingAs($this->admin)->get(route('patients.edit', $patient));
        $response->assertStatus(200);
        $response->assertSee($patient->name);
    }

    /** @test */
    public function pos_starts_and_loads_products()
    {
        $product = Product::create([
            'name' => 'Test Panadol',
            'unit_price' => 10.00,
            // 'stock' is computed
            'category_id' => Category::create(['name' => 'Tabs'])->id,
            'barcode' => '123456'
        ]);

        \App\Models\InventoryBatch::create([
            'product_id' => $product->id,
            'batch_number' => 'B1',
            'quantity' => 100,
            'expiry_date' => now()->addYear(),
            'cost_price' => 5
        ]);

        $response = $this->actingAs($this->admin)->get(route('pos.index'));
        $response->assertStatus(200);
        $response->assertSee('Test Panadol');
        $response->assertSee('123456'); // Barcode
    }

    /** @test */
    public function prescription_create_page_loads()
    {
        $response = $this->actingAs($this->admin)->get(route('prescriptions.create'));
        $response->assertStatus(200);
        $response->assertSee('New Prescription');
    }

    /** @test */
    public function admin_pages_load_successfully()
    {
        // Users Create
        $response = $this->actingAs($this->admin)->get(route('users.create'));
        $response->assertStatus(200);

        // Branches Create
        $response = $this->actingAs($this->admin)->get(route('branches.create'));
        $response->assertStatus(200);

        // Expenses Create
        $response = $this->actingAs($this->admin)->get(route('expenses.create'));
        $response->assertStatus(200);

        // Suppliers Create
        $response = $this->actingAs($this->admin)->get(route('suppliers.create'));
        $response->assertStatus(200);

        // Shifts Reports
        $response = $this->actingAs($this->admin)->get(route('admin.shifts.index'));
        $response->assertStatus(200);
    }
}
