<?php

namespace App\Http\Controllers;

use App\Models\Character;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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
    public function show($id)
    {
        $character = Character::find($id);
        $character = $character->makeHidden(['created_at', 'updated_at']);

        if (is_null($character)) {
            return response()->json(['error:' => 'character not found'], 404);
        }

        return $character;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $character = Character::find($id);

            if (is_null($character)) {
                return response()->json(['error:' => 'character not found'], 404);
            }

            $character->name = $request->name;
            $character->age = $request->age;
            $character->wight = $request->wight;
            $character->history = $request->history;
            $character->image = $request->image;

            $character->update();

            return response()->json($character);
        } catch (\Exception $ex) {
            return response()->json(['error' => 'Bad request'], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $character = Character::find($id);

        if (is_null($character)) {
            return response()->json(['error:' => 'character not found'], 404);
        }

        $character->delete();

        return response()->noContent();
    }
}
