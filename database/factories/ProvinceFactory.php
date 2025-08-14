<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Province>
 */
class ProvinceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
           // Obtém o ID do país Moçambique
        $mozambique = Country::firstWhere('code', 'MZ');

        // Lista oficial das províncias de Moçambique
        $provinces = [
            'Cabo Delgado',
            'Gaza',
            'Inhambane',
            'Manica',
            'Maputo',
            'Nampula',
            'Niassa',
            'Sofala',
            'Tete',
            'Zambézia'
        ];

        return [
            'name' => $this->faker->unique()->randomElement($provinces),
            'country_id' => $mozambique->id ?? null,
        ];
    }
}
