<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponse;
use Auth;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $notifications = Auth::user()
            ->notifications()
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return $this->success($notifications);
    }
}
