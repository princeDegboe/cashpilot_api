<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Devis;

class DevisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $devis = [
            ['name' => 'X0F'],
            ['name' => '$'],
            ['name' => '€'],
            ['name' => '£'],
            ['name' => '¥'],
            ['name' => '₽'],
            ['name' => '₹'],
            ['name' => '₩'],
            ['name' => '₿'],
            ['name' => '฿'],

        ];

        foreach ($devis as $devi) {
            Devis::create([
                'name' => $devi['name'],
            ]);
        }
    }
}
