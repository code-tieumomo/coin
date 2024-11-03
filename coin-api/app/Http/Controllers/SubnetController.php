<?php

namespace App\Http\Controllers;

use App\Events\PrivateNotification;
use App\Models\Notification;
use App\Models\Subnet;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubnetController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $subnets = Subnet::withCount([
            'users' => function (Builder $query) {
                $query->where('users.id', Auth::id());
            }
        ])->get()->map(function ($subnet) {
            $subnet->is_joined = $subnet->users_count > 0;

            return $subnet;
        });

        return $this->success($subnets, 'Subnets retrieved successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Subnet $subnet)
    {
        try {
            return $this->success($subnet, 'Subnet retrieved successfully.');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 500);
        }
    }

    public function join(Subnet $subnet)
    {
        try {
            $subnet->users()->attach(Auth::id());

            return $this->success(null, 'Subnet joined successfully.');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 500);
        }
    }

    public function authenticate(Subnet $subnet, Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        $token = $request->get('token');
        $user = User::where('token', $token)->first();
        if (!$user) {
            return $this->error('Invalid token.', 401);
        }

        $isJoined = $user->subnets()->where('subnet_id', $subnet->id)->exists();

        if (!$isJoined) {
            return $this->error('User is not joined to the subnet.', 401);
        }

        return $this->success(null, 'User authenticated successfully.');
    }
}
