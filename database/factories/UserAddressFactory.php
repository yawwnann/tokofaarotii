<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserAddress>
 */
class UserAddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'label' => $this->faker->word(),
            'receiver_name' => $this->faker->name(),
            'phone' => $this->faker->phoneNumber(),
            'province' => $this->faker->state(),
            'city' => $this->faker->city(),
            'district' => $this->faker->citySuffix(),
            'postal_code' => $this->faker->postcode(),
            'address' => $this->faker->streetAddress(),
            'is_default' => false,
        ];
    }
}
