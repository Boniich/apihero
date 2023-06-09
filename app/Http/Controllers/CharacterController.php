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
     * @OA\Get(
     *      path="/api/characters",
     *      tags={"Characters"},
     *      summary="Show a list of characters",
     * 
     *      @OA\Response(
     *          response=200,
     *          description="Show all characters"  
     *      ),
     *      @OA\Response(
     *          response="default",
     *          description="An error ocurred"
     *      )
     * )
     */
    public function index()
    {
        $data = Character::all(['name', 'image']);

        return response()->json(successResponse($data, "show all characters"));
    }

    /**
     * Create a new character.
     * Note: This endpoint does not work in swagger cause is not possible upload an image here.
     * @OA\Post(
     *      path="/api/characters",
     *      operationId="store",
     *      summary="Create a new character",
     *      tags={"Characters"},
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
     *          description="Bad request"  
     *      ),
     *      @OA\Response(
     *          response="default",
     *          description="An error ocurred"
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

            return response()->json(successResponse($newCharacter, "Character created"));
        } catch (\Exception $ex) {
            return response()->json(errorResponse("Bad Request"), 400);
        }
    }

    /**
     * Show details about one character
     * @param int $id
     * @return \Illuminate\Http\Response
     * @OA\Get(
     *      path="/api/characters/{id}",
     *      tags={"Characters"},
     *      summary="Show details about one character",
     * 
     *      @OA\Parameter(
     *      
     *          description="id of character",
     *          in="path",
     *          name="id",
     *          required=true,
     *          @OA\Schema(type="string"),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Show details about one character"  
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Character not found"  
     *      ),
     *      @OA\Response(
     *          response="default",
     *          description="An error ocurred"
     *      )
     * 
     * )
     */
    public function show($id)
    {
        $character = Character::find($id);

        if (is_null($character)) {
            return response()->json(errorResponse("Character not found"), 404);
        }

        $character->films;

        $character->makeHidden(['created_at', 'updated_at']);
        $character->films->makeHidden(['created_at', 'updated_at', 'pivot']);

        return response()->json(successResponse($character, "Character retrieved successfully"));
    }

    /**
     * Update a character's data
     * @OA\Put(
     *      path="/api/characters/{id}",
     *      tags={"Characters"},
     *      summary="Update a character's data",
     * 
     *      @OA\Parameter(
     *      
     *          description="id of character",
     *          in="path",
     *          name="id",
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
     *          description="An error ocurred"
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
                return response()->json(errorResponse("Character not found"), 404);
            }

            $character->name = $request->name;
            $character->age = $request->age;
            $character->wight = $request->wight;
            $character->history = $request->history;
            $character->image = updateLoadedImage($character->image, $request->image);

            $character->update();

            return response()->json(successResponse($character, "Character updated successfully"));
        } catch (\Exception $ex) {
            return response()->json(errorResponse("Bad Request"), 400);
        }
    }

    /**
     * Delete a character
     * @OA\Delete(
     *      path="/api/characters/{id}",
     *      summary="Delete Character",
     *      tags={"Characters"},
     * 
     *       @OA\Parameter(
     *      
     *          description="id of character",
     *          in="path",
     *          name="id",
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
     *          description="An error ocurred"
     *      )
     * )
     */
    public function destroy($id)
    {
        $character = Character::find($id);

        if (is_null($character)) {
            return response()->json(errorResponse("Character not found"), 404);
        }

        deleteLoadedImage($character->image);
        $character->delete();

        return response()->json(successResponse($character, "Character deleted successfully"));
    }

    /**
     * Search a character
     * @param string $name
     * @param string $age
     * @return \Illuminate\Http\Response
     * @OA\Get(
     *      path="/api/searchCharacter/{name}/{age}",
     *      tags={"Characters"},
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
     *          description="Retrive a character if according to the name or age"  
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Character not found"  
     *      ),
     *      @OA\Response(
     *          response="default",
     *          description="An error ocurred"
     *      )
     * 
     * )
     */

    public function search($name = '', $age = '')
    {
        $character = Character::where('name', $name)->orWhere('age', $age)->get();

        if (is_null($character)) {
            return response()->json(errorResponse("Character not found"), 404);
        }

        return response()->json(successResponse($character, "Character retrived successfully"));
    }
}
