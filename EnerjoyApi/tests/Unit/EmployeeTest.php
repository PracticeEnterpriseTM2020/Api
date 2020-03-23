<?php

namespace Tests\Unit;

use App\Employee;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EmployeeTest extends TestCase
{
    use DatabaseTransactions;

    function setUp(): void
    {
        parent::setUp();
        $this->base_url = "/api/employees";
        $this->admin = Employee::first();
        $this->hrUser = Employee::find(2);
        $this->normalUser = Employee::find(3);
        $this->employeeStructure = [
            "first_name",
            "last_name",
            "email",
            "salary",
            "ssn",
            "birthdate",
            "address" => [
                "id", "street", "number",
                "city" => [
                    "id", "name", "postalcode",
                    "country" => ["id", "name"]
                ]
            ],
            "job" => ["id", "job_title"],
        ];
    }

    // LOGIN

    public function test_login_missing_email_password()
    {
        $response = $this->post("$this->base_url/login", []);
        $response->assertStatus(400);
    }

    public function test_login_missing_email()
    {
        $response = $this->post("$this->base_url/login", []);
        $response->assertStatus(400);
    }

    public function test_login_missing_password()
    {
        $response = $this->post("$this->base_url/login", []);
        $response->assertStatus(400);
    }

    public function test_login_wrong_credentials()
    {
        $response = $this->post("$this->base_url/login", ["email" => "admin@enerjoy.be", "password" => "secret2"]);
        $response->assertStatus(401);
    }

    public function test_login_success()
    {
        $response = $this->post("$this->base_url/login", ["email" => "admin@enerjoy.be", "password" => "secret"]);
        $response->assertStatus(200)->assertJsonStructure($this->employeeStructure);
    }

    // FILTER

    public function test_get_all_unauthenticated()
    {
        $response = $this->get($this->base_url);
        $response->assertStatus(401);
    }

    public function test_get_all_unauthorized()
    {
        $response = $this->actingAs($this->normalUser)->get($this->base_url);
        $response->assertStatus(403);
    }

    public function test_get_all_success()
    {
        $response = $this->actingAs($this->admin)->get($this->base_url);
        $response->assertStatus(200);
    }

    // SHOW BY ID

    public function test_show_by_id_unauthenticated()
    {
        $response = $this->get("$this->base_url/1");
        $response->assertStatus(401);
    }

    public function test_show_by_id_unauthorized()
    {
        $response = $this->actingAs($this->normalUser)->get("$this->base_url/1");
        $response->assertStatus(403);
    }

    public function test_show_by_id_not_found()
    {
        $response = $this->actingAs($this->admin)->get("$this->base_url/10000");
        $response->assertStatus(404);
    }

    public function test_show_by_id_success()
    {
        $response = $this->actingAs($this->admin)->get("$this->base_url/1");
        $response->assertStatus(200);
        $response->assertJsonStructure($this->employeeStructure);
    }
}
