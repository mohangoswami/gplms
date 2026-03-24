<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ResultCoScholasticAreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('result_co_scholastic_areas')->insert([
    [
        'performa_id' => 1,
        'class' => 'Nursery',
        'area_name' => 'Poem',
        'display_order' => 1,
    ],
    [
        'performa_id' => 1,
        'class' => 'Nursery',
        'area_name' => 'Rhymes',
        'display_order' => 2,
    ],
    [
        'performa_id' => 1,
        'class' => '8',
        'area_name' => 'Computer',
        'display_order' => 1,
    ],
    [
        'performa_id' => 1,
        'class' => '8',
        'area_name' => 'Discipline',
        'display_order' => 2,
    ],
]);

    }
}
