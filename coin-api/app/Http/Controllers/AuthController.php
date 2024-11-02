<?php

namespace App\Http\Controllers;

use App\Enums\RoleEnum;
use App\Models\User;
use App\Traits\ApiResponse;
use Arr;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\GoogleProvider;
use Spatie\Permission\Models\Role;

class AuthController extends Controller
{
    use ApiResponse;

    public function redirectToProvider(string $provider): JsonResponse
    {
        /**
         * @var GoogleProvider $socialiteProvider
         */
        $socialiteProvider = Socialite::driver($provider);

        return response()->json([
            'url' => $socialiteProvider
                ->stateless()
                ->redirect()
                ->getTargetUrl(),
        ]);
    }

    public function handleProviderCallback(string $provider): JsonResponse
    {
        try {
            /**
             * @var GoogleProvider $socialiteProvider
             */
            $socialiteProvider = Socialite::driver($provider);
            $googleUser = $socialiteProvider->stateless()->user();
            $googleId = $googleUser->getId();
            $email = $googleUser->getEmail();

            $user = User::where('google_id', $googleId)->first();
            if (!$user) {
                $user = User::where('email', $email)->first();
                if (!$user) {
                    $user = User::create([
                        'name' => $googleUser->getName(),
                        'email' => $email,
                        'password' => Hash::make(Str::random(24)),
                        'google_id' => $googleId,
                        'token' => Str::random(32),
                    ]);
                    $user->assignRole(RoleEnum::USER);
                } else {
                    $user->update([
                        'google_id' => $googleId,
                    ]);
                }
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'user' => $user,
                'token' => $token,
            ]);
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 401);
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->error('The provided credentials are incorrect.', 400);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function role(Request $request)
    {
        $request->validate([
            'role' => 'required|in:' . RoleEnum::PROVIDER->value . ',' . RoleEnum::MINER->value,
        ]);

        if (Auth::user()->getRoleNames()->count() > 0) {
            return $this->error('User already has a role', 400);
        }

        $roleName = $request->role;
        $role = Role::findByName($roleName);
        Auth::user()->roles()->detach();
        Auth::user()->assignRole($role);

        return $this->success([
            'role' => Auth::user()->getRoleNames()->first(),
            'permissions' => $role->permissions->pluck('name')->toArray(),
        ], 'Role Assigned');
    }

    public function logout()
    {
        Auth::user()->tokens()->delete();

        return $this->success('Tokens Revoked');
    }
}
