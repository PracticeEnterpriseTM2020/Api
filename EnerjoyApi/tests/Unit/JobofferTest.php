<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Employee;
use App\JobOffer;

class JobofferTest extends TestCase
{
    use DatabaseTransactions;

    function setUp(): void
    {
        parent::setUp();
        $this->base_url = "/api/joboffers";
        $this->admin = Employee::first();
        $this->user = Employee::find(3);
        $this->structure = [
            "job_offer_title",
            "job_offer_description",
            "job" => ["id", "job_title"],
            "creator" => [
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
            ]
        ];
    }

    // Filter

    public function test_get_all_unauthenticated()
    {
        $response = $this->get($this->base_url);
        $response->assertStatus(401);
    }

    public function test_get_all_success()
    {
        $response = $this->actingAs($this->user)->get($this->base_url);
        $response->assertStatus(200);
    }

    // Show by id

    public function test_show_by_id_unauthenticated()
    {
        $response = $this->get("$this->base_url/1");
        $response->assertStatus(401);
    }

    public function test_show_by_id_not_found()
    {
        $response = $this->actingAs($this->user)->get("$this->base_url/5000");
        $response->assertStatus(404);
    }

    public function test_show_by_id_success()
    {
        $response = $this->actingAs($this->user)->get("$this->base_url/1");
        $response->assertStatus(200);
        $response->assertJsonStructure($this->structure);
    }

    // Create

    public function test_create_unauthenticated()
    {
        $response = $this->post($this->base_url);
        $response->assertStatus(401);
    }

    public function test_create_unauthorized()
    {
        $response = $this->actingAs($this->user)->post($this->base_url);
        $response->assertStatus(403);
    }

    public function test_create_missing_fields()
    {
        $response = $this->actingAs($this->admin)->post($this->base_url);
        $response->assertStatus(400);
    }

    public function test_create_bad_job_id()
    {
        $response = $this->actingAs($this->admin)->post($this->base_url, ["job_offer_title" => "title", "job_offer_description" => "description", "creator_id" => 1, "job_id" => 5000]);
        $response->assertStatus(400);
    }

    public function test_create_bad_creator_id()
    {
        $response = $this->actingAs($this->admin)->post($this->base_url, ["job_offer_title" => "title", "job_offer_description" => "description", "creator_id" => 5000, "job_id" => 1]);
        $response->assertStatus(400);
    }

    public function test_create_success()
    {
        $response = $this->actingAs($this->admin)->post($this->base_url, ["job_offer_title" => "title", "job_offer_description" => "description", "creator_id" => 1, "job_id" => 1]);
        $response->assertStatus(201);
        $response->assertJson(["job_offer_title" => "title", "job_offer_description" => "description", "creator" => ["id" => 1], "job" => ["id" => 1]]);
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

    public function test_update_missing_fields()
    {
        $response = $this->actingAs($this->admin)->put("$this->base_url/1");
        $response->assertStatus(400);
    }

    public function test_update_bad_job_id()
    {
        $response = $this->actingAs($this->admin)->put("$this->base_url/1", ["job_offer_title" => "title", "job_offer_description" => "description", "creator_id" => 1, "job_id" => 5000]);
        $response->assertStatus(400);
    }

    public function test_update_bad_creator_id()
    {
        $response = $this->actingAs($this->admin)->put("$this->base_url/1", ["job_offer_title" => "title", "job_offer_description" => "description", "creator_id" => 5000, "job_id" => 1]);
        $response->assertStatus(400);
    }

    public function test_update_success()
    {
        $response = $this->actingAs($this->admin)->put("$this->base_url/1", ["job_offer_title" => "title", "job_offer_description" => "description", "creator_id" => 1, "job_id" => 1]);
        $response->assertStatus(200);
        $response->assertJson(["job_offer_title" => "title", "job_offer_description" => "description", "creator" => ["id" => 1], "job" => ["id" => 1]]);
    }

    // Delete

    public function test_delete_unauthenticated()
    {
        $response = $this->delete("$this->base_url/1");
        $response->assertStatus(401);
    }

    public function test_delete_unauthorized()
    {
        $response = $this->actingAs($this->user)->delete("$this->base_url/1");
        $response->assertStatus(403);
    }

    public function test_delete_not_found()
    {
        $response = $this->actingAs($this->admin)->delete("$this->base_url/5000");
        $response->assertStatus(404);
    }

    public function test_delete_success()
    {
        $response = $this->actingAs($this->admin)->delete("$this->base_url/1");
        $response->assertStatus(204);
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
        $response = $this->actingAs($this->admin)->put("$this->base_url/1/restore");
        $response->assertStatus(404);
    }

    public function test_restore_success()
    {
        JobOffer::destroy(1);
        $response = $this->actingAs($this->admin)->put("$this->base_url/1/restore");
        $response->assertStatus(200);
    }
}
