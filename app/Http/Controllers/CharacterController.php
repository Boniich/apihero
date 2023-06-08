<?php

namespace App\Http\Controllers;

use App\Models\Character;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required',
                'age' => 'required|integer',
                'image' => 'required|image',
                'wight' => 'required|decimal:0,1000',
                'history' => 'required|string|max:500'
            ]);

            $newCharacter = new Character;

            $newCharacter->name = $request->name;
            $newCharacter->age = $request->age;
            $newCharacter->wight = $request->wight;
            $newCharacter->history = $request->history;

            $imageName = time() . '.' . $request->image->getClientOriginalExtension();
            $newCharacter->image = $imageName;
            Storage::disk('public')->put($imageName, file_get_contents($request->image));

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

        if (is_null($character)) {
            return response()->json(['error:' => 'character not found'], 404);
        }

        $character->films;

        $character->makeHidden(['created_at', 'updated_at']);
        $character->films->makeHidden(['created_at', 'updated_at', 'pivot']);

        return $character;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {

            $request->validate([
                'name' => 'required',
                'age' => 'required|integer',
                'image' => 'required|image',
                'wight' => 'required|decimal:0,1000',
                'history' => 'required|string|max:500'
            ]);

            $character = Character::find($id);

            if (is_null($character)) {
                return response()->json(['error:' => 'character not found'], 404);
            }

            $character->name = $request->name;
            $character->age = $request->age;
            $character->wight = $request->wight;
            $character->history = $request->history;

            $imageName = time() . '.' . $request->image->getClientOriginalExtension();
            Storage::disk('public')->delete($character->image);
            $character->image = $imageName;
            Storage::disk('public')->put($imageName, file_get_contents($request->image));

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

        Storage::disk('public')->delete($character->image);

        if (is_null($character)) {
            return response()->json(['error:' => 'character not found'], 404);
        }

        $character->delete();

        return response()->noContent();
    }

    public function search($name = '', $age = '')
    {
        $character = Character::where('name', $name)->orWhere('age', $age)->get();

        if (is_null($character)) {
            return response()->json(['error:' => 'character not found'], 404);
        }

        return response()->json($character);
    }
}
