<?php

namespace App\Http\Controllers;

use App\Events\PrivateNotification;
use App\Models\Notification;
use App\Models\Subnet;
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
}
