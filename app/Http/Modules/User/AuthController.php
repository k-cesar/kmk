<?php

namespace App\Http\Modules\User;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    /**
     * Register a User.
     * 
     * @param \Illuminate\Http\Request $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|unique:users|max:255',
            'password' => 'required|string|confirmed|min:8',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse(422, 'Datos inválidos', $validator->errors());
        }

        $user = User::create(array_merge(
            $validator->validated(),
            ['password' => Hash::make($request->password)]
        ));

        return $this->showOne($user, 201);
    }

    /**
     * Get a JWT via given credentials.
     * 
     * @param \Illuminate\Http\Request $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);
        
        if ($validator->fails()) {
            return $this->errorResponse(422, 'Datos inválidos', $validator->errors());
        }

        $user = User::query()
            ->where('email', $validator->validated()['email'])
            ->first();

        if (!$user || !Hash::check($validator->validated()['password'], $user->password)) {
            return $this->errorResponse(401, 'No Autorizado');
        }

        if ($user->token && JWTAuth::setToken($user->token)->check()) {
            return $this->errorResponse(409, 'Sesión Activa');
        }

        $user->token = auth()->attempt($validator->validated());
        $user->save();

        $permissionsByModules = $user->getPermissionsByModules();
    
        return $this->respondWithTokenAndPermissionsByModules($user->token, $permissionsByModules);
        
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return $this->showOne(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        $user = auth()->user();
        $user->token = null;
        $user->save();

        auth()->logout();

        return $this->showMessage('Cierre de Sesión Exitoso');
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        $user = auth()->user();
        $user->token = auth()->refresh();
        $user->save();

        return $this->respondWithToken($user->token);
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
            'token_type'   => 'bearer',
            'expires_in'   => auth()->factory()->getTTL() * 60
        ]);
    }

    /**
     * Get the token and permissions by module array structure.
     *
     * @param  string $token
     * @param array $permissionsByModules
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithTokenAndPermissionsByModules($token, $permissionsByModules)
    {
        return response()->json([
            'token' => [
                'access_token' => $token,
                'token_type'   => 'bearer',
                'expires_in'   => auth()->factory()->getTTL() * 60
            ],
            'modules' => $permissionsByModules
        ]);
    }
}
