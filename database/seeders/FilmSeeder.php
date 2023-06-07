<?php

namespace Database\Seeders;

use App\Models\Film;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FilmSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hero = new Film();

        $hero->title = "Advenger";
        $hero->image = "image a cargar";
        $hero->created_date = "2021";
        $hero->score = 5;

        $hero->save();
    }
}
