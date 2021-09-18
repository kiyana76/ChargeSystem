<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\CreditLogs;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CreditLogsFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CreditLogs::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $type_arr = ['increase', 'decrease', 'lock'];
        $type = $type_arr[array_rand($type_arr)];

        $admin = User::factory()->for(Company::factory())->create(['type' => 'admin'])->toArray();
        return [
            'amount' => rand(20000,100000),
            'type' => $type,
            'admin_id' => $type == 'increase' ? $admin['id'] : null
        ];
    }
}
