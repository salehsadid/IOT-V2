<?php

namespace Database\Factories;

use App\Enums\PatientStatus;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Patient>
 */
class PatientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        static $counter = 0;
        $counter++;

        return [
            'patient_code'      => 'PKN-' . str_pad($counter, 4, '0', STR_PAD_LEFT),
            'full_name'         => fake()->name(),
            'gender'            => fake()->randomElement(['male', 'female']),
            'date_of_birth'     => fake()->dateTimeBetween('-80 years', '-40 years')->format('Y-m-d'),
            'phone'             => fake()->optional(0.8)->numerify('+60#########'),
            'emergency_contact' => fake()->optional(0.7)->name() . ' — ' . fake()->numerify('+60#########'),
            'status'            => PatientStatus::Active->value,
            'notes'             => fake()->optional(0.4)->sentence(10),
        ];
    }

    /**
     * State for an inactive patient record.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PatientStatus::Inactive->value,
        ]);
    }
}
