<?php

namespace App\Http\Controllers;

use App\Models\Film;
use Illuminate\Http\Request;

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
                'score' => 'integer|min:0|max:5'
            ]);

            $newFilm = new Film;

            $newFilm->title = $request->title;
            $newFilm->image = $request->image;
            $newFilm->created_date = $request->created_date;
            $newFilm->score = $request->score;

            $newFilm->save();

            return response()->json($newFilm);
        } catch (\Exception $ex) {

            return response()->json(['error' => 'Bad request'], 400);
        }
    }

    public function show($id)
    {

        $film = Film::find($id);

        if (is_null($film)) {
            return response()->json(['error:' => 'movie not found'], 404);
        }

        $film = $film->makeHidden(['created_at', 'updated_at']);

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
            $film->image = $request->image;
            $film->created_date = $request->created_date;
            $film->score = $request->score;

            $film->update();

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
}
