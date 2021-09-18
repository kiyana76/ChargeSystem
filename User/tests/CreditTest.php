<?php

use App\Classes\Credit;
use App\Models\Company;
use App\Models\CreditLogs;
use App\Models\User;
use App\Repository\Eloquent\UserEloquentRepository;
use App\Repository\UserRepositoryInterface;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Laravel\Lumen\Testing\WithoutMiddleware;

class CreditTest extends TestCase
{
    use WithoutMiddleware, DatabaseTransactions;

    public function test_increase_credit() {
        $major_company = Company::factory()->count(1)->create()->toArray();
        $company = Company::factory()->count(1)->create()->toArray();
        $admin = User::factory()->create(['type' => 'admin', 'company_id' => $major_company[0]['id']])->toArray();
        $seller = User::factory()->create(['type' => 'seller', 'company_id' => $company[0]['id']])->toArray();

        $response = $this->post('/credit', [
            'user_id' => $seller['id'],
            'amount' => 50000,
            'type' => 'increase',
        ]);
        $response->assertResponseStatus(422);

        $response = $this->post('/credit', [
            'user_id' => $seller['id'],
            'amount' => 50000,
            'type' => 'increase',
            'admin_id' => $admin['id']
        ]);
        $response->assertResponseStatus(201);
        $response->seeJson(['message' => 'credit log create successfully!', 'body' => $response->response['body'], 'error' => false]);
    }

    public function test_get_credit() {
        $company = Company::factory()->count(1)->create()->toArray();
        $seller = User::factory()->create(['type' => 'seller', 'company_id' => $company[0]['id']])->toArray();

        $response = $this->get('/get-credit');
        $response->assertResponseStatus(422);

        $response = $this->get('/get-credit?user_id=' . $seller['id']);

        $response->assertResponseStatus(200);
        $response->seeJson(['message' => 'credit retrieved!', 'body' => ['credit' => $response->response['body']['credit']], 'error' => false]);
    }

    public function test_update_credit_log() {
        $credits = CreditLogs::factory()->count(6)
            ->for(User::factory()->for(Company::factory()), 'user')
            ->create()->toArray();

        foreach ($credits as $credit) {
            switch ($credit['type']) {
                case 'lock':
                    $lock_credit = $credit;
                    break;
                case 'increase':
                    $increase_credit = $credit;
                    break;
                case 'decrease':
                    $decrease_credit = $credit;
                    break;
            }
        }


        if (isset($increase_credit)) {
            $response = $this->put('/credit', [
                'type' => 'decrease',
                'credit_log_id' => $increase_credit['id']
            ]);

            $response->seeStatusCode(422);
        }

        if (isset($decrease_credit)) {
            $response = $this->put('/credit', [
                'type' => 'increase',
                'credit_log_id' => $decrease_credit['id']
            ]);

            $response->seeStatusCode(422);
        }

        if (isset($lock_credit)) {
            $response = $this->put('/credit', [
                'type' => 'increase',
                'credit_log_id' => $lock_credit['id']
            ]);

            $response->seeStatusCode(422);
        }

        if (isset($lock_credit)) {
            $response = $this->put('/credit', [
                'type' => 'decrease',
                'credit_log_id' => $lock_credit['id']
            ]);

            $response->seeStatusCode(200);
            $response->seeJson(['message' => 'credit logs update successfully!', 'body' => [], 'error' => false]);
        }




    }

    public function test_get_credit_list() {
        $company = Company::factory()->count(1)->create()->toArray();
        $seller = User::factory()->count(2)->create(['type' => 'seller', 'company_id' => $company[0]['id']])->toArray();

//        $response = $this->get('/credit/log');
//        $response->seeStatusCode(200);
//        $response->seeJson(['message' => 'credit retrieved!', 'body' => $response->response['body'], 'error' => false]);

        $response = $this->get('/credit/log?seller_id=' . $seller[0]['id']);
        $response->seeStatusCode(200);
        $response->seeJson(['message' => 'credit retrieved!', 'body' => $response->response['body'], 'error' => false]);

        $credit_log_not_for_this_seller = false;
        foreach ($response->response['body'] as $credit_log) {
            if ($credit_log['user_id'] != $seller[0]['id']) {
                $credit_log_not_for_this_seller = true;
                break;
            }
        }
        $this->assertFalse($credit_log_not_for_this_seller);
    }
}
