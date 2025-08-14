<?php

namespace Database\Factories;

use App\Models\Email;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Email>
 */
class EmailFactory extends Factory
{

    protected $model = Email::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
          return [
            'public_id' => Str::uuid(),
            'email' => $this->faker->unique()->safeEmail(),
            'type' => $this->faker->randomElement(['personal', 'work', 'other']),
            'is_primary' => $this->faker->boolean(30), // 30% chance de ser primary
            'emailable_id' => null, // será preenchido ao criar relacionado
            'emailable_type' => null, // será preenchido ao criar relacionado
        ];
    }
}
