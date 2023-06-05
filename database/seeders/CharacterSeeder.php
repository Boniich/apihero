<?php

namespace Database\Seeders;

use App\Models\Character;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CharacterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hero = new Character();

        $hero->name = "Iron Man";
        $hero->age = 50;
        $hero->wight = 70.5;
        $hero->history = "Tony Stark es Iron Man";
        $hero->image = "imagen a cargar";

        $hero->save();
    }
}
