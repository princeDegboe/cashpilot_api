<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TypeTransaction;

class TypeTransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            ['name' => 'Entrée'],
            ['name' => 'Sortie'],
        ];

        foreach ($types as $type) {
            TypeTransaction::create([
                'name' => $type['name'],
            ]);
        }
    }
}
