<?php

namespace App\Http\Controllers;

use App\Models\Character;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\HttpException;


/**
 * @OA\Info(
 *  version="1.0.0",
 *  title="ApiHeros Documentacion.",
 *  description="An API about Marvel Heros and their movies.",
 * )
 */
class CharacterController extends Controller
{
    /**
     * Display a listing of the resource.
     * Mostramos el listado de personajes.
     * 
     * @OA\Get(
     *      path="/api/characters",
     *      tags={"characters"},
     *      summary="Show a list of characters",
     * 
     *      @OA\Response(
     *          response=200,
     *          description="Muestra todos los personajes"  
     *      ),
     *      @OA\Response(
     *          response="default",
     *          description="Ha ocurrido un error"
     *      )
     * )
     */
    public function index()
    {
        return Character::all(['name', 'image']);
    }

    /**
     * Create a new character.
     * Note: This endpoint does not work in swagger cause is not possible upload an image here.
     * @OA\Post(
     *      path="/api/characters",
     *      operationId="store",
     *      summary="Create a new character",
     *      tags={"characters"},
     * 
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *          required={"name","age","wight", "history","image"},
     *          @OA\Property(property="name", type="string", format="string", example="Thor"),
     *          @OA\Property(property="age", type="integer", format="integer", example=10),
     *          @OA\Property(property="wight", type="decimal", format="decimal", example=10.5),
     *          @OA\Property(property="history", type="string", format="string", example="Thorrrrr"),
     *          @OA\Property(property="image", type="string", format="image", example="1686235605.png"),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successfully created character"  
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="bad request"  
     *      ),
     *      @OA\Response(
     *          response="default",
     *          description="Ha ocurrido un error"
     *      )
     * )
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
            $newCharacter->image = upLoadImage($request->image);

            $newCharacter->save();

            return response()->json($newCharacter);
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
     *      path="/api/characters/{characters}",
     *      tags={"characters"},
     *      summary="Show details about one character",
     * 
     *      @OA\Parameter(
     *      
     *          description="id of character",
     *          in="path",
     *          name="characters",
     *          required=true,
     *          @OA\Schema(type="string"),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Muestra los detalles de un personaje"  
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Character not found"  
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
     * Update a character's data
     * @OA\Put(
     *      path="/api/characters/{characters}",
     *      tags={"characters"},
     *      summary="Update a character's data",
     * 
     *      @OA\Parameter(
     *      
     *          description="id of character",
     *          in="path",
     *          name="characters",
     *          required=true,
     *          @OA\Schema(type="string"),
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *          required={"name","age","wight", "history","image"},
     *          @OA\Property(property="name", type="string", format="string", example="Thanos"),
     *          @OA\Property(property="age", type="integer", format="integer", example=1000),
     *          @OA\Property(property="wight", type="decimal", format="decimal", example=300),
     *          @OA\Property(property="history", type="string", format="string", example="Thanos is a villain"),
     *          @OA\Property(property="image", type="string", format="image", example="1686235605.png"),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Character updated"  
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Character not found"  
     *      ),
     *      @OA\Response(
     *          response="default",
     *          description="Ha ocurrido un error"
     *      )
     * )
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
            $character->image = updateLoadedImage($character->image, $request->image);

            $character->update();

            return response()->json($character);
        } catch (\Exception $ex) {
            return response()->json(['error' => 'Bad request'], 400);
        }
    }

    /**
     * Delete a character
     * @OA\Delete(
     *      path="/api/characters/{characters}",
     *      summary="Delete Character",
     *      tags={"characters"},
     * 
     *       @OA\Parameter(
     *      
     *          description="id of character",
     *          in="path",
     *          name="characters",
     *          required=true,
     *          @OA\Schema(type="string"),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Delete a character"  
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Character not found"  
     *      ),
     *      @OA\Response(
     *          response="default",
     *          description="Error"
     *      )
     * )
     */
    public function destroy($id)
    {
        $character = Character::find($id);

        if (is_null($character)) {
            return response()->json(['error:' => 'character not found'], 404);
        }

        deleteLoadedImage($character->image);
        $character->delete();

        return response()->noContent();
    }

    /**
     * Display the specified resource.
     * Muestra los detalles de un personaje
     * @param string $name
     * @param string $age
     * @return \Illuminate\Http\Response
     * @OA\Get(
     *      path="/api/searchCharacter/{name}/{age}",
     *      tags={"characters"},
     *      summary="Search a character",
     *      @OA\Parameter(
     *      
     *          description="name of character",
     *          in="path",
     *          name="name",
     *          required=false,
     *          @OA\Schema(type="string"),
     *      ),
     *      @OA\Parameter(
     *      
     *          description="age of character",
     *          in="path",
     *          name="age",
     *          required=false,
     *          @OA\Schema(type="string"),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Muestra los detalles de un personaje"  
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Character not found"  
     *      ),
     *      @OA\Response(
     *          response="default",
     *          description="Ha ocurrido un error"
     *      )
     * 
     * )
     */

    public function search($name = '', $age = '')
    {
        $character = Character::where('name', $name)->orWhere('age', $age)->get();

        if (is_null($character)) {
            return response()->json(['error:' => 'character not found'], 404);
        }

        return response()->json($character);
    }
}
