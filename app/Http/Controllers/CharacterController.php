<?php

namespace App\Http\Controllers;

use App\Models\Character;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CharacterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Character::all(['name', 'image']);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required',
                'age' => 'required|integer',
                'image' => 'required',
                'wight' => 'required|decimal:0,1000',
                'history' => 'required|string|max:500'
            ]);

            $newCharacter = new Character;

            $newCharacter->name = $request->name;
            $newCharacter->age = $request->age;
            $newCharacter->wight = $request->wight;
            $newCharacter->history = $request->history;
            $newCharacter->image = $request->image;

            $newCharacter->save();

            return response()->json($newCharacter);
        } catch (\Exception $ex) {
            return response()->json(['error' => 'Bad request'], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Character $character)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Character $character)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Character $character)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Character $character)
    {
        //
    }
}
