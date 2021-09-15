<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $status_arr = ['active', 'inactive'];
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'type' => $this->faker->randomElement(['admin', 'seller']),
            'mobile' => $this->faker->numerify('09#######'),
            'password' => Hash::make('123456'),
            'status' => $status_arr[array_rand($status_arr)]
        ];
    }
}
