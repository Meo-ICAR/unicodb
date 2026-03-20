<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PracticeStatusLookupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            ['name' => 'istruttoria', 'color' => 'warning', 'description' => 'Pratica in fase di istruttoria', 'sort_order' => 1],
            ['name' => 'deliberata', 'color' => 'success', 'description' => 'Pratica deliberata e approvata', 'sort_order' => 2],
            ['name' => 'erogata', 'color' => 'primary', 'description' => 'Pratica erogata e completata', 'sort_order' => 3],
            ['name' => 'respinta', 'color' => 'danger', 'description' => 'Pratica respinta dalla banca', 'sort_order' => 4],
            ['name' => 'annullata', 'color' => 'gray', 'description' => 'Pratica annullata dal cliente', 'sort_order' => 5],
        ];

        foreach ($statuses as $status) {
            DB::table('practice_status_lookup')->insert([
                'name' => $status['name'],
                'color' => $status['color'],
                'description' => $status['description'],
                'sort_order' => $status['sort_order'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
