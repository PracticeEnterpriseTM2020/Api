<?php

namespace Tests\Unit;

use App\Employee;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ArticleTest extends TestCase
{
    use DatabaseTransactions;

    function setUp(): void
    {
        parent::setUp();
        $this->base_url = "/api/articles";
        $this->admin = Employee::first();
        $this->user = Employee::find(3);
        $this->structure = [
            "title",
            "description",
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
}
