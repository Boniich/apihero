<?php

namespace App\Http\Controllers;

use App\Models\Character;
use App\Models\Film;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FilmController extends Controller
{

    /**
     * Display a listing of the resource.
     * Mostramos el listado de personajes.
     * 
     * @OA\Get(
     *      path="/api/movies",
     *      tags={"Movies"},
     *      summary="Show a list of movies",
     * 
     *      @OA\Response(
     *          response=200,
     *          description="Show all movies"  
     *      ),
     *      @OA\Response(
     *          response="default",
     *          description="An error has occurred"
     *      )
     * )
     */


    public function index()
    {
        return Film::all('image', 'title', 'created_date');
    }

    /**
     * Create a new character.
     * Note: This endpoint does not work in swagger cause is not possible upload an image here.
     * @OA\Post(
     *      path="/api/movies",
     *      summary="Create a new movie",
     *      tags={"Movies"},
     * 
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *          required={"title","image","created_date", "score","characters"},
     *          @OA\Property(property="title", type="string", format="string", example="Iron Man 2"),
     *          @OA\Property(property="image", type="string", format="string", example="image-movie.png"),
     *          @OA\Property(property="created_date", type="string", format="string", example="2010"),
     *          @OA\Property(property="score", type="integer", format="string", example="5"),
     *          @OA\Property(property="characters", type="array", example=
     *                      {{
     *                          "id": 4,
     *                          "name": "Black Widow",
     *                          "age": 30,
     *                          "wight": 60.5,
     *                          "history": "Samanta es Black Widow",
     *                          "image": "imagen-black-widow.png"
     *                      },
     *                          },@OA\Items(
     *                              @OA\Property(
     *                                  property="id",
     *                                  type="integer",
     *                                  example=""
     *                                  ),
     *                             @OA\Property(
     *                                 property="name",
     *                                 type="string",
     *                                 example=""
     *                                 ),
     *                             @OA\Property(
     *                                 property="age",
     *                                 type="integer",
     *                                 example=""
     *                                 ),
     * 
     *                             @OA\Property(
     *                                 property="wight",
     *                                 type="float",
     *                                 example=""
     *                                 ),
     * 
     *                             @OA\Property(
     *                                 property="history",
     *                                 type="string",
     *                                 example=""
     *                                 ),
     * 
     *                             @OA\Property(
     *                                 property="image",
     *                                 type="string",
     *                                 example=""
     *                        ),
     *                    ),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successfully created movie"  
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="bad request"  
     *      ),
     *      @OA\Response(
     *          response="default",
     *          description="An error has occurred"
     *      )
     * )
     */

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
            $newFilm->image = upLoadImage($request->image);
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


    /**
     * Display the specified resource.
     * Muestra los detalles de un personaje
     * @param int $id
     * @return \Illuminate\Http\Response
     * @OA\Get(
     *      path="/api/movies/{id}",
     *      tags={"Movies"},
     *      summary="Show details about one movie",
     * 
     *      @OA\Parameter(
     *          description="id of movie",
     *          in="path",
     *          name="id",
     *          required=true,
     *          @OA\Schema(type="string"),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Show Details about a movie"  
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Movie not found"  
     *      ),
     *      @OA\Response(
     *          response="default",
     *          description="Ha ocurrido un error"
     *      )
     * 
     * )
     */

    public function show($id)
    {

        $film = Film::find($id);

        if (is_null($film)) {
            return response()->json(['error:' => 'movie not found'], 404);
        }

        $film->characters;
        $film->makeHidden(['created_at', 'updated_at']);
        $film->characters->makeHidden(['created_at', 'updated_at', 'pivot']);

        return $film;
    }

    /**
     * Create a new character.
     * Note: This endpoint does not work in swagger cause is not possible upload an image here.
     * @OA\Put(
     *      path="/api/movies/{id}",
     *      summary="update a movie",
     *      tags={"Movies"},
     * 
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *          required={"title","image","created_date", "score","characters"},
     *          @OA\Property(property="title", type="string", format="string", example="Iron Man 2"),
     *          @OA\Property(property="image", type="string", format="string", example="image-movie.png"),
     *          @OA\Property(property="created_date", type="string", format="string", example="2010"),
     *          @OA\Property(property="score", type="integer", format="string", example="5"),
     *          @OA\Property(property="characters", type="array", example=
     *                      {{
     *                          "id": 4,
     *                          "name": "Black Widow",
     *                          "age": 30,
     *                          "wight": 60.5,
     *                          "history": "Samanta es Black Widow",
     *                          "image": "imagen-black-widow.png"
     *                      },
     *                          },@OA\Items(
     *                              @OA\Property(
     *                                  property="id",
     *                                  type="integer",
     *                                  example=""
     *                                  ),
     *                             @OA\Property(
     *                                 property="name",
     *                                 type="string",
     *                                 example=""
     *                                 ),
     *                             @OA\Property(
     *                                 property="age",
     *                                 type="integer",
     *                                 example=""
     *                                 ),
     * 
     *                             @OA\Property(
     *                                 property="wight",
     *                                 type="float",
     *                                 example=""
     *                                 ),
     * 
     *                             @OA\Property(
     *                                 property="history",
     *                                 type="string",
     *                                 example=""
     *                                 ),
     * 
     *                             @OA\Property(
     *                                 property="image",
     *                                 type="string",
     *                                 example=""
     *                        ),
     *                    ),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successfully updated movie"  
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="bad request"  
     *      ),
     *      @OA\Response(
     *          response="default",
     *          description="An error has occurred"
     *      )
     * )
     */

    public function update(Request $request, $id)
    {
        try {
            $film = Film::find($id);

            if (is_null($film)) {
                return response()->json(['error:' => 'movie not found'], 404);
            }

            $film->title = $request->title;
            $film->image = updateLoadedImage($film->image, $request->image);
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

    /**
     * Delete a moview
     * @OA\Delete(
     *      path="/api/movies/{id}",
     *      summary="Delete a moview",
     *      tags={"Movies"},
     * 
     *       @OA\Parameter(
     *      
     *          description="id of movie",
     *          in="path",
     *          name="id",
     *          required=true,
     *          @OA\Schema(type="string"),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Movie Deleted"  
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Movie not found"  
     *      ),
     *      @OA\Response(
     *          response="default",
     *          description="Error"
     *      )
     * )
     */

    public function destroy($id)
    {
        $film = Film::find($id);

        if (is_null($film)) {
            return response()->json(['error:' => 'id not found'], 404);
        }

        deleteLoadedImage($film->image);
        $film->delete();

        return response()->noContent();
    }

    /**
     * Display the specified resource.
     * Search moview
     * @param string $title
     * @return \Illuminate\Http\Response
     * @OA\Get(
     *      path="/api/searchMovies/{title}",
     *      tags={"Movies"},
     *      summary="Search a movie",
     *      @OA\Parameter(
     *      
     *          description="title of a movie",
     *          in="path",
     *          name="title",
     *          required=false,
     *          @OA\Schema(type="string"),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Movie founded"  
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Movie not found"  
     *      ),
     *      @OA\Response(
     *          response="default",
     *          description="An error has occurred"
     *      )
     * 
     * )
     */

    public function search($title = '')
    {
        $film = Film::where('title', $title)->get();

        if (is_null($film)) {
            return response()->json(['error:' => 'film not found'], 404);
        }

        return response()->json($film);
    }
}
