<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Categorie;

class CategorieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Salaire','type_id' => 1],
            ['name' => 'Revenus supplémentaires','type_id' => 1],
            ['name' => 'Remboursements','type_id' => 1],
            ['name' => 'Cadeaux','type_id' => 1],
            ['name' => 'Ventes','type_id' => 1],
            ['name' => 'Intérêts','type_id' => 1],
            ['name' => 'Allocation','type_id' => 1],
            ['name' => 'Autres','type_id' => 1],
            ['name' => 'Alimentation','type_id' => 2],
            ['name' => 'Transport','type_id' => 2],
            ['name' => 'Logement','type_id' => 2],
            ['name' => 'Santé','type_id' => 2],
            ['name' => 'Divertissement','type_id' => 2],
            ['name' => 'Achats personnels','type_id' => 2],
            ['name' => 'Soins personnels','type_id' => 2],
            ['name' => 'Autres','type_id' => 2],

        ];

        foreach ($categories as $categorie) {
            Categorie::create([
                'name' => $categorie['name'],
                'type_id' => $categorie['type_id']
            ]);
        }
    }
}
