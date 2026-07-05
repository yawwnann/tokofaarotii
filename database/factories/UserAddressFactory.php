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
            'label' => $this->faker->randomElement(['Rumah', 'Kantor', 'Kost', 'Apartemen']),
            'receiver_name' => $this->faker->name(),
            'phone' => $this->faker->phoneNumber(),
            'province_id' => '31', // DKI Jakarta
            'province' => 'DKI JAKARTA',
            'city_id' => '3171', // Jakarta Selatan
            'city' => 'KOTA JAKARTA SELATAN',
            'district_id' => '3171010', // Jagakarsa
            'district' => 'JAGAKARSA',
            'village_id' => '3171010001', // Jagakarsa village
            'village' => 'JAGAKARSA',
            'postal_code' => $this->faker->postcode(),
            'address' => $this->faker->streetAddress(),
            'is_default' => false,
        ];
    }
}
