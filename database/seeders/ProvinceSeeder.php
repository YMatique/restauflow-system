<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Province;
use App\Models\City;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtém Moçambique da tabela countries
        $mozambique = Country::firstWhere('code', 'MZ');

        if (!$mozambique) {
            $this->command->info('País Moçambique não encontrado. Rode primeiro o CountrySeeder.');
            return;
        }

        // Lista de províncias com suas cidades e capital/downtown
        $provinces = [
            'Cabo Delgado' => [
                ['name' => 'Pemba', 'capital' => true],
                ['name' => 'Mocímboa da Praia', 'capital' => false],
            ],
            'Gaza' => [
                ['name' => 'Xai-Xai', 'capital' => true],
                ['name' => 'Chókwè', 'capital' => false],
            ],
            'Inhambane' => [
                ['name' => 'Inhambane', 'capital' => true],
                ['name' => 'Maxixe', 'capital' => false],
            ],
            'Manica' => [
                ['name' => 'Chimoio', 'capital' => true],
                ['name' => 'Manica', 'capital' => false],
            ],
            'Maputo' => [
                ['name' => 'Maputo City', 'capital' => true],
                ['name' => 'Matola', 'capital' => false],
            ],
            'Nampula' => [
                ['name' => 'Nampula', 'capital' => true],
                ['name' => 'Montepuez', 'capital' => false],
            ],
            'Niassa' => [
                ['name' => 'Lichinga', 'capital' => true],
                ['name' => 'Cuamba', 'capital' => false],
            ],
            'Sofala' => [
                ['name' => 'Beira', 'capital' => true],
                ['name' => 'Dondo', 'capital' => false],
            ],
            'Tete' => [
                ['name' => 'Tete', 'capital' => true],
                ['name' => 'Moatize', 'capital' => false],
            ],
            'Zambézia' => [
                ['name' => 'Quelimane', 'capital' => true],
                ['name' => 'Mocuba', 'capital' => false],
            ],
        ];

        foreach ($provinces as $provinceName => $cities) {

            $capitalCity = null;

            // Cria as cidades primeiro
            foreach ($cities as $cityData) {
                $city = City::updateOrCreate(
                    ['name' => $cityData['name']],
                    [
                        'public_id' => Str::uuid(),
                        'name' => $cityData['name'],
                        'capital' => $cityData['capital'],
                    ]
                );

                // Se for capital, guardamos para associar à província
                if ($cityData['capital']) {
                    $capitalCity = $city;
                }
            }

            // Cria ou atualiza a província, associando a cidade capital
            if ($capitalCity) {
                Province::updateOrCreate(
                    ['name' => $provinceName, 'country_id' => $mozambique->id],
                    [
                        'public_id' => Str::uuid(),
                        'name' => $provinceName,
                        'country_id' => $mozambique->id,
                        'city_id' => $capitalCity->id
                    ]
                );
            }
        }
    }
}
