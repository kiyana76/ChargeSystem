<?php

use App\Models\Company;
use App\Models\Customer;
use App\Models\User;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Laravel\Lumen\Testing\WithoutMiddleware;

class UserTest extends TestCase
{
    use DatabaseMigrations, WithoutMiddleware;
    public function test_add_seller() {
        $major_company = Company::factory()->count(1)->create()->toArray();
        $admin = User::factory()->create(['type' => 'admin', 'company_id' => $major_company[0]['id']]);
        $company = Company::factory()->count(1)->create()->toArray();


        $token = Auth::attempt(['mobile' => $admin->toArray()['mobile'], 'password' => '123456']);

        $response = $this->actingAs($admin)->post( '/register/user', [
            'type' => 'seller',
            'name' => 'test company',
            'company_id' => $company[0]['id'],
            'mobile' => '09302828758',
            'email' => 'kiayan76@gmail.com',
            'password' => '123456',
            'status' => 'active'
        ], [
            'Authorization' => 'bearer' . $token
        ]);

        $response->assertResponseStatus(201);
        $this->seeInDatabase('users', ['mobile' => '09302828758', 'type' => 'seller']);
    }

    public function test_add_admin() {
        $major_company = Company::factory()->count(1)->create()->toArray();
        $admin = User::factory()->create(['type' => 'admin', 'company_id' => $major_company[0]['id']]);
        $company = Company::factory()->count(1)->create()->toArray();


        $token = Auth::attempt(['mobile' => $admin->toArray()['mobile'], 'password' => '123456']);

        $response = $this->actingAs($admin)->post( '/register/user', [
            'type' => 'admin',
            'name' => 'test company',
            'company_id' => $company[0]['id'],
            'mobile' => '09302828758',
            'email' => 'kiayan76@gmail.com',
            'password' => '123456',
            'status' => 'active'
        ], [
            'Authorization' => 'bearer' . $token
        ]);

        $response->assertResponseStatus(201);
        $this->seeInDatabase('users', ['mobile' => '09302828758', 'type' => 'admin']);
    }

    public function test_show_user() {
        $user = User::factory()->for(Company::factory())->create()->toArray();

        $response = $this->get('/user/' . $user['id']);

        $response->assertResponseStatus(200);
        $response->seeJson(['message' => 'user founded!', 'body' => [$user], 'error' => false]);

        $response = $this->get('/user/' . 2);

        $response->assertResponseStatus(404);
        $response->seeJson(['message' => 'user not found!', 'body' => [], 'error' => true]);
    }

    public function test_customer_login() {
        $response = $this->post('/customer/login', ['mobile' => '09302828629', 'password' => '123456']);

        $response->assertResponseStatus(200);
        $response->seeJson(['token' => $response->response['token'], 'token_type' => 'bearer']);
        $this->assertTrue(auth()->guard('customer')->check());
    }
}
