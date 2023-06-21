<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{

    /**
     * Register a new user.
     * @OA\Post(
     *      path="/api/register",
     *      summary="Register a new user",
     *      tags={"Auth"},
     * 
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *          required={"name","email","password","password_confirmation"},
     *          @OA\Property(property="name", type="string", format="string"),
     *          @OA\Property(property="email", type="string", format="string"),
     *          @OA\Property(property="password", type="string", format="string" ),
     *          @OA\Property(property="password_confirmation", type="string", format="string" ),
     *                    ),
     *              ),
     *      @OA\Response(
     *          response=200,
     *          description="User register successfully"  
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

    public function register(Request $request)
    {

        try {

            $request->validate([
                'name' => 'required',
                'email' => 'required|email|unique:users',
                'password' => 'required|confirmed'
            ]);

            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);

            $user->save();

            return response()->json(successResponse($user, "User register successfully"));
        } catch (\Throwable $th) {
            return response()->json(errorResponse("Bad Request"), 400);
        }
    }

    /**
     * Login User.
     * @OA\Post(
     *      path="/api/login",
     *      summary="Login user",
     *      tags={"Auth"},
     * 
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *          required={"email","password"},
     *          @OA\Property(property="email", type="string", format="string"),
     *          @OA\Property(property="password", type="string", format="string" ),
     *                    ),
     *              ),
     *      @OA\Response(
     *          response=200,
     *          description="User login successfully"  
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

    public function login(Request $request)
    {

        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('token')->plainTextToken;

            return response()->json(["success" => true, "token" => $token, "message" => "Token created successfully"]);
        } else {
            return response()->json(errorResponse("Invalid Credentials"), 401);
        }
    }
}
