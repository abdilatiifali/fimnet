<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $number = random_int(1, 80);

        return [
            'name' => $this->faker->name(),
            'phone_number' => $this->faker->phoneNumber(),
            'house_id' => 1,
            'ip_address' => "192.168.1.{$number}",
            'router_id' => 1,
            'appartment' => '6E',
            'amount' => 2000,
            'created_at' => now()->subDays(random_int(1, 80)),
        ];
    }
}
