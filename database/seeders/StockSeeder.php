<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('stocks')->insert([
            [
                'name' => 'Armazém Central',
                'notes' => 'Estoque principal da empresa',
                'company_id' => 1, // precisa existir uma empresa com id=1
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Depósito Norte',
                'notes' => 'Estoque secundário no norte',
                'company_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Depósito Sul',
                'notes' => null,
                'company_id' => 2, // empresa diferente
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
