<?php

namespace App\Http\Controllers;

use App\Models\Character;
use App\Models\Film;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FilmController extends Controller
{
    public function index()
    {
        return Film::all('image', 'title', 'created_date');
    }

    public function store(Request $request)
    {

        try {
            $request->validate([
                'title' => 'required',
                'image' => 'required',
                'created_date' => 'required|string',
                'score' => 'integer|min:0|max:5',
                'characters' => 'required|integer'
            ]);

            $newFilm = new Film;

            $newFilm->title = $request->title;

            $imageName = time() . '.' . $request->image->getClientOriginalExtension();
            $newFilm->image  = $imageName;
            Storage::disk('public')->put($imageName, file_get_contents($request->image));

            $newFilm->created_date = $request->created_date;
            $newFilm->score = $request->score;

            $newFilm->save();

            $newFilm->characters()->sync(Character::find($request->characters));

            $newFilm->characters;

            $newFilm->makeHidden(['created_at', 'updated_at']);
            $newFilm->characters->makeHidden(['created_at', 'updated_at', 'pivot']);

            return response()->json($newFilm);
        } catch (\Exception $ex) {

            return response()->json(['error' => 'Bad request'], 400);
        }
    }

    public function show($id)
    {

        $film = Film::find($id);

        $film->characters;

        if (is_null($film)) {
            return response()->json(['error:' => 'movie not found'], 404);
        }

        $film->makeHidden(['created_at', 'updated_at']);
        $film->characters->makeHidden(['created_at', 'updated_at', 'pivot']);

        return $film;
    }

    public function update(Request $request, $id)
    {
        try {
            $film = Film::find($id);

            if (is_null($film)) {
                return response()->json(['error:' => 'movie not found'], 404);
            }

            $film->title = $request->title;

            $imageName = time() . '.' . $request->image->getClientOriginalExtension();
            Storage::disk('public')->delete($film->image);
            $film->image = $imageName;
            Storage::disk('public')->put($imageName, file_get_contents($request->image));

            $film->created_date = $request->created_date;
            $film->score = $request->score;

            $film->update();

            $film->characters()->sync(Character::find($request->characters));

            $film->characters;

            $film->makeHidden(['created_at', 'updated_at']);
            $film->characters->makeHidden(['created_at', 'updated_at', 'pivot']);


            return response()->json($film);
        } catch (\Throwable $th) {

            return response()->json(['error' => 'Bad request']);
        }
    }

    public function destroy($id)
    {
        $film = Film::find($id);

        if (is_null($film)) {
            return response()->json(['error:' => 'id not found'], 404);
        }

        $film->delete();

        return response()->noContent();
    }

    public function search($title = '')
    {
        $film = Film::where('title', $title)->get();

        Storage::disk('public')->delete($film->image);

        if (is_null($film)) {
            return response()->json(['error:' => 'film not found'], 404);
        }

        return response()->json($film);
    }
}
