<?php
 
namespace App\Http\Controllers;
 
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
 
 
class AuthController extends Controller
{
    protected $rules;

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
        $this->rules = [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:6',
        ];
    }
 
 
    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request) {
        $data = $request->all();

        $validator = Validator::make($data, $this->rules);
 
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $user = new User;
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->password = bcrypt($data['password']);
        $user->save();
 
        return response()->json($user, 201);
    }
 
 
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);
 
        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
 
        return $this->respondWithToken($token);
    }
 
    public function check()
    {
        if (auth()->check()) {
            return response()->json(auth()->check());
        } else {
            return $this->refresh();
        }
        
    }
 
    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();
 
        return response()->json(['message' => 'Successfully logged out']);
    }
 
    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }
 
    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }
}