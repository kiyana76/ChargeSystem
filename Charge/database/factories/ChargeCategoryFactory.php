<?php

namespace Database\Factories;

use App\Models\ChargeCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChargeCategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ChargeCategory::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $array_name = ['شارژ 1000 تومانی', 'شارژ 2000 تومانی', 'شارژ 5000 تومانی', 'شارژ 10000 هزار تومانی', 'شارژ 20000 هزار تومانی'];
        $array_price = ['10000', '20000', '50000', '100000', '200000'];
        return [
            'name' => $array_name[array_rand($array_name)],
            'amount' => $array_price[array_rand($array_price)],
        ];
    }
}
