<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Middleware is now handled in routes, no need for constructor middleware
    }

    /**
     * Register a new user.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
     public function register(Request $request)
    {
        \Log::info('Registration attempt started', ['raw_email' => $request->email]);

        // Normalize email to lowercase and trim whitespace
        $email = strtolower(trim($request->email));

        \Log::info('Email normalized', ['normalized_email' => $email]);

        // Check if user was created very recently (within last 5 seconds) - likely a duplicate request
        $recentUser = User::where('email', $email)
            ->where('created_at', '>', now()->subSeconds(5))
            ->first();

        if ($recentUser) {
            \Log::warning('Duplicate registration request detected - returning success for existing user', [
                'email' => $email,
                'user_id' => $recentUser->id,
                'created_at' => $recentUser->created_at
            ]);

            // Return success with token for the existing user (idempotent behavior)
            $token = auth('api')->login($recentUser);
            return $this->respondWithToken($token);
        }

        $validator = Validator::make(array_merge($request->all(), ['email' => $email]), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            \Log::error('Validation failed', [
                'email' => $email,
                'errors' => $validator->errors()->toArray()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        \Log::info('Validation passed, attempting to create user', ['email' => $email]);

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $email,
                'password' => Hash::make($request->password),
            ]);

            \Log::info('User created successfully', ['user_id' => $user->id, 'email' => $email]);

            $token = auth('api')->login($user);

            \Log::info('User logged in successfully', ['user_id' => $user->id]);

            return $this->respondWithToken($token);
        } catch (\Illuminate\Database\QueryException $e) {
            \Log::error('Database error during user creation', [
                'email' => $email,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Database error (possible duplicate entry)',
                'errors' => $e->getMessage()
            ], 409);
        } catch (\Exception $e) {
            \Log::error('Exception during registration', [
                'email' => $email,
                'error' => $e->getMessage()
            ]);

            // If anything fails after user creation, delete the user
            if (isset($user) && $user->exists) {
                \Log::info('Deleting user due to registration failure', ['user_id' => $user->id]);
                $user->delete();
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Registration failed',
                'errors' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a JWT via given credentials.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        // Normalize email to lowercase and trim whitespace
        $email = strtolower(trim($request->email));

        $validator = Validator::make(array_merge($request->all(), ['email' => $email]), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $credentials = [
            'email' => $email,
            'password' => $request->password
        ];

        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid credentials'
            ], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json([
            'status' => 'success',
            'user' => auth('api')->user()
        ]);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth('api')->logout();

        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out'
        ]);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        $token = auth('api')->refresh();

        return $this->respondWithToken($token);
    }

    /**
     * Get the token array structure.
     *
     * @param string $token
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'status' => 'success',
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'user' => auth('api')->user()
        ]);
    }
}