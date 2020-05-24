<?php

namespace Tests\Unit;

use App\Employee;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Token;

class EmployeeTest extends TestCase
{
    use DatabaseTransactions;

    function setUp(): void
    {
        parent::setUp();
        $this->base_url = "/api/employees";
        $this->admin = Employee::first();
        $this->user = Employee::find(3);
        $this->employeeStructure = [
            "first_name",
            "last_name",
            "email",
            "phone",
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

    // Login

    public function test_login_missing_email_password()
    {
        $response = $this->post("$this->base_url/login", []);
        $response->assertStatus(400);
    }

    public function test_login_missing_email()
    {
        $response = $this->post("$this->base_url/login", ["email" => "admin@enerjoy.be"]);
        $response->assertStatus(400);
    }

    public function test_login_missing_password()
    {
        $response = $this->post("$this->base_url/login", ["password" => "secret"]);
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

    // Filter

    public function test_get_all_unauthenticated()
    {
        $response = $this->get($this->base_url);
        $response->assertStatus(401);
    }

    public function test_get_all_unauthorized()
    {
        $response = $this->actingAs($this->user)->get($this->base_url);
        $response->assertStatus(403);
    }

    public function test_get_all_success()
    {
        $response = $this->actingAs($this->admin)->get($this->base_url);
        $response->assertStatus(200);
    }

    // Show by id

    public function test_show_by_id_unauthenticated()
    {
        $response = $this->get("$this->base_url/1");
        $response->assertStatus(401);
    }

    public function test_show_by_id_unauthorized()
    {
        $response = $this->actingAs($this->user)->get("$this->base_url/1");
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

    // Self

    public function test_self_unauthenticated()
    {
        $response = $this->get("$this->base_url/self");
        $response->assertStatus(401);
    }

    public function test_self_success()
    {
        $response = $this->actingAs($this->admin)->get("$this->base_url/self");
        $response->assertStatus(200);
    }

    // Logout

    public function test_logout_unauthenticated()
    {
        $response = $this->delete("$this->base_url/logout");
        $response->assertStatus(401);
    }

    public function test_logout_success()
    {
        $token = JWTAuth::fromUser($this->user);
        $response = $this->actingAs($this->user)->delete("$this->base_url/logout", [], ["Authorization" => "Bearer $token"]);
        $response->assertStatus(200);
    }

    // Refresh

    public function test_refresh_unauthenticated()
    {
        $response = $this->get("$this->base_url/refresh");
        $response->assertStatus(401);
    }

    public function test_refresh_expired()
    {
        $token = JWTAuth::fromUser($this->user);
        JWTAuth::manager()->invalidate(new Token($token));
        $response = $this->get("$this->base_url/refresh", ["Authorization" => "Bearer $token"]);
        $response->assertStatus(401);
    }

    public function test_refresh_success()
    {
        $token = JWTAuth::fromUser($this->user);
        $response = $this->get("$this->base_url/refresh", ["Authorization" => "Bearer $token"]);
        $response->assertStatus(200);
    }

    // Update

    public function test_update_unauthenticated()
    {
        $response = $this->put("$this->base_url/1");
        $response->assertStatus(401);
    }

    public function test_update_unauthorized()
    {
        $response = $this->actingAs($this->user)->put("$this->base_url/1");
        $response->assertStatus(403);
    }

    public function test_update_not_found()
    {
        $response = $this->actingAs($this->admin)->put("$this->base_url/10000");
        $response->assertStatus(404);
    }

    public function test_update_password_does_not_match()
    {
        $response = $this->actingAs($this->admin)->put("$this->base_url/4", [
            "first_name" => "x",
            "password" => "x",
            "password_confirmation" => "b",
            "last_name" => "x",
            "salary" => 9000,
            "phone" => "x",
            "ssn" => "x",
            "birthdate" => "1981/12/27",
            "street" => "x",
            "number" => "x",
            "city" => "x",
            "postalcode" => "x",
            "country_id" => 1,
            "job_id" => 1
        ]);
        $response->assertStatus(400);
    }

    public function test_update_success()
    {
        $response = $this->actingAs($this->admin)->put("$this->base_url/4", [
            "first_name" => "x",
            "last_name" => "x",
            "salary" => 9000,
            "phone" => "x",
            "ssn" => "x",
            "birthdate" => "1981/12/27",
            "street" => "x",
            "number" => "x",
            "city" => "x",
            "postalcode" => "x",
            "country_id" => 1,
            "job_id" => 1
        ]);
        $response->assertStatus(200);
    }

    // Restore

    public function test_restore_unauthenticated()
    {
        $response = $this->put("$this->base_url/1/restore");
        $response->assertStatus(401);
    }

    public function test_restore_unauthorized()
    {
        $response = $this->actingAs($this->user)->put("$this->base_url/1/restore");
        $response->assertStatus(403);
    }

    public function test_restore_not_found()
    {
        $response = $this->actingAs($this->admin)->put("$this->base_url/10000/restore");
        $response->assertStatus(404);
    }

    public function test_restore_id_success()
    {
        $response = $this->actingAs($this->admin)->put("$this->base_url/1/restore");
        $response->assertStatus(200);
    }

    // Create
    public function test_create_unauthenticated()
    {
        $response = $this->post("$this->base_url");
        $response->assertStatus(401);
    }

    public function test_create_unauthorized()
    {
        $response = $this->actingAs($this->user)->post("$this->base_url");
        $response->assertStatus(403);
    }

    public function test_create_wrong_email()
    {
        $testEmployee = [
            "first_name" => "a",
            "last_name" => "a",
            "email" => "admine",
            "password" => "test",
            "password_confirmation" => "test",
            "salary" => 5000,
            "phone" => "d1",
            "ssn" => "1",
            "birthdate" => "1971/02/11",
            "street" => "a",
            "number" => "1",
            "city" => "a",
            "postalcode" => "1",
            "country_id" => 1,
            "job_id" => "1"
        ];

        $response = $this->actingAs($this->admin)->post("$this->base_url", $testEmployee);
        $response->assertStatus(400);
    }


    public function test_create_missing_key()
    {
        $testEmployee = [
            "first_name" => "a",
            "last_name" => "a",
            "email" => "admin2@enerjoy.be",
            "password" => "test",
            "password_confirmation" => "test",
            "salary" => 5000,
            "phone" => "d1",
            "ssn" => "1",
            "birthdate" => "1971/02/11",
            "number" => "1",
            "city" => "a",
            "postalcode" => "1",
            "country_id" => 1,
            "job_id" => "1"
        ];

        $response = $this->actingAs($this->admin)->post("$this->base_url", $testEmployee);
        $response->assertStatus(400);
    }

    public function test_create_success()
    {
        $testEmployee = [
            "first_name" => "a",
            "last_name" => "a",
            "email" => "admin2@enerjoy.be",
            "password" => "test",
            "password_confirmation" => "test",
            "salary" => 5000,
            "phone" => "d1",
            "ssn" => "1",
            "birthdate" => "1971/02/11",
            "street" => "a",
            "number" => "1",
            "city" => "a",
            "postalcode" => "1",
            "country_id" => 1,
            "job_id" => "1"
        ];

        $response = $this->actingAs($this->admin)->post("$this->base_url", $testEmployee);
        $response->assertStatus(201)
            ->assertJsonMissingExact($testEmployee);
    }

    // Delete
    public function test_delete_unauthenticated()
    {
        $response = $this->delete("$this->base_url/1");
        $response->assertStatus(401);
    }

    public function test_delete_unauthorized()
    {
        $response = $this->actingAs($this->user)->delete("$this->base_url/5");
        $response->assertStatus(403);
    }

    public function test_delete_not_found()
    {
        $response = $this->actingAs($this->admin)->delete("$this->base_url/10000");
        $response->assertStatus(404);
    }

    public function test_delete_success()
    {
        $response = $this->actingAs($this->admin)->delete("$this->base_url/5");
        $response->assertStatus(204);
    }
}
