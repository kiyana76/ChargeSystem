<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class ChargeCategoryTest extends TestCase
{
    use DatabaseMigrations;

    public function testChargeCategory()
    {
        $charges = \App\Models\ChargeCategory::factory()->count(2)->create();
        $response = $this->get('charge-categories');

        $response->assertResponseStatus(200);
        $response->seeJson(['message' => 'charge category retrieved!', 'body' => $response->response['body'], 'error' => false]);
    }
}
