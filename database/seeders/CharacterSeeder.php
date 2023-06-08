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
        $character = new Character();

        $character->name = "Iron Man";
        $character->age = 50;
        $character->wight = 70.5;
        $character->history = "Tony Stark es Iron Man";
        $character->image = "imagen a cargar";

        $character->save();

        $character2 = new Character();

        $character2->name = "Thor";
        $character2->age = 300;
        $character2->wight = 150;
        $character2->history = "Thor es el dios del trueno";
        $character2->image = "imagen a cargar";

        $character2->save();


        $character3 = new Character();

        $character3->name = "Hulk";
        $character3->age = 30;
        $character3->wight = 300;
        $character3->history = "Bruce Banner es hulk";
        $character3->image = "imagen a cargar";

        $character3->save();


        $character4 = new Character();

        $character4->name = "Black Widow";
        $character4->age = 30;
        $character4->wight = 60.5;
        $character4->history = "Samanta es Black Widow";
        $character4->image = "imagen a cargar";

        $character4->save();


        $character5 = new Character();

        $character5->name = "Ojos de halcon";
        $character5->age = 30;
        $character5->wight = 70.5;
        $character5->history = "Donde apunta te la clava";
        $character5->image = "imagen a cargar";

        $character5->save();
    }
}
