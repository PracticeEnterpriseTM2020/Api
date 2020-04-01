<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Employee;
use App\Invoice;
use Illuminate\Foundation\Testing\DatabaseTransactions;


class InvoiceTest extends TestCase
{
    use DatabaseTransactions;

    function setUp(): void
    {
        parent::setUp();
        $this->base_url = "/api/invoices";
        $this->admin = Employee::first();
        $this->user = Employee::find(3);
        $this->structure = ["id",
            "customerId",
            "price",
            "date",
            "paid",
            "active"];
    }

    // Filter

    /*
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
    */

    public function test_get_all_success()
    {
        $response = $this->get($this->base_url);
        $response->assertStatus(200);
    }

    // Show by id

    /*
    public function test_show_by_id_unauthenticated()
    {
        $response = $this->get("$this->base_url/1");
        $response->assertStatus(401);
    }
    */

    public function test_show_by_id_not_found()
    {
        $response = $this->get("$this->base_url", ["id"=>"5000"]);
        $response->assertStatus(404);
    }

    public function test_show_by_id_success()
    {
        $response = $this->actingAs($this->admin)->get("$this->base_url", ["id"=>"1"]);
        $response->assertStatus(200);
    }

    // Create

    /*
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
    */

    public function test_create_missing_fields()
    {
        $data = ["id" => 11,
                "price" => 100,
                "date" => "1971/02/11"];
        $response = $this->post($this->base_url);
        $response->assertStatus(400);
    }
    

    public function test_create_success()
    {
        $data = ["id" => 15,
                "customerId" => 1,
                "price" => 100,
                "date" => "1971/02/11"];

        $response = $this->post($this->base_url, $data);
        $response->assertStatus(201);
    }

    // Update
    /*
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

    public function test_update_success()
    {
        $response = $this->actingAs($this->admin)->put("$this->base_url/1", ["job_title" => "job_title"]);
        $response->assertStatus(200);
        $response->assertJson(["job_title" => "job_title"]);
    }
    */
    // Delete
    /*
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
    */

    public function test_delete_not_found()
    {
        $response = $this->delete("$this->base_url", ["id"=>"1000"]);
        $response->assertStatus(404);
    }

    public function test_delete_success()
    {
        $response = $this->delete("$this->base_url", ["id"=>"1"]);
        $response->assertStatus(204);
    }

    // Restore
    /*
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
    */

    public function test_restore_not_found()
    {
        $response = $this->put("$this->base_url/restore", ["id"=>"1000"]);
        $response->assertStatus(404);
    }

    public function test_restore_success()
    {
        //Invoice::destroy(1);
        $this->delete("$this->base_url", ["id"=>"1"]);

        $response = $this->put("$this->base_url/restore", ["id"=>"1"]);
        $response->assertStatus(200);
    }
}
