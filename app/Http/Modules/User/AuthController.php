<?php

namespace App\Http\Modules\User;

use App\Support\Helper;
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
     * Get a JWT via given credentials.
     * 
     * @param \Illuminate\Http\Request $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse(422, 'Datos inválidos', $validator->errors());
        }

        $username = Helper::decrypt($validator->validated()['username'], env('PASSPHRASE'));
        $password = Helper::decrypt($validator->validated()['password'], env('PASSPHRASE'));

        $user = User::query()
            ->where('username', $username)
            ->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return $this->errorResponse(401, 'No Autorizado');
        }

        if ($user->token && JWTAuth::setToken($user->token)->check()) {
            return $this->errorResponse(409, 'Sesión Activa');
        }

        $user->token = auth()->attempt([
            'username' => $username,
            'password' => $password
        ]);

        $user->save();

        $permissions = $user->getDirectPermissions()->pluck('id');

        return $this->respondWithTokenAndPermissions($user->token, $permissions);
    }

    /**
     * Change the password of a authenticated user.
     * 
     * @param \Illuminate\Http\Request $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function reset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'actual_password' => 'required|string',
            'password'        => 'required|string|min:8|max:25|confirmed',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse(422, 'Datos inválidos', $validator->errors());
        }

        if (!Hash::check($request->actual_password,  auth()->user()->password)) {
            return $this->errorResponse(401, 'No Autorizado');
        }

        auth()->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return $this->showMessage('Cambio de Password Exitoso');
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        $user = clone(auth()->user());

        $user->permissions = auth()->user()->getDirectPermissions()->pluck('id');

        return $this->showOne($user);
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
     * @param array $permissions
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithTokenAndPermissions($token, $permissions)
    {
        return response()->json([
            'token' => [
                'access_token' => $token,
                'token_type'   => 'bearer',
                'expires_in'   => auth()->factory()->getTTL() * 60
            ],
            'permissions' => $permissions
        ]);
    }
}
