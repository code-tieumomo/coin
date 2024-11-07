<?php

namespace App\Http\Controllers;

use App\Http\Resources\AssignmentResource;
use App\Models\Assignment;
use App\Traits\ApiResponse;
use Auth;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $assignments = Assignment::with([
            'subnets' => function ($query) {
                $query->orderBy('weight', 'desc');
            },
            'subnets.grades' => function ($query) {
                $query->where('user_id','=', Auth::user()->id);
            }
        ])
            ->active()
            ->where(function ($query) {
                $query->where("is_public", true)
                    ->orWhereHas('users', function ($query) {
                        $query->where('users.id', Auth::id());
                    });
            })
            ->orderBy('start_date')
            ->get();

        return $this->success(AssignmentResource::collection($assignments));
    }
}
