<?php

namespace App\Http\Controllers;

use App\Services\UserTimeTrackingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Log;

class UserTimeController extends Controller
{
    public function __construct(
        private UserTimeTrackingService $userTimeTrackingService
    ) {}

    public function syncTime(Request $request): JsonResponse
    {
        Log::info('Sync time request', $request->all());

        $validated = $request->validate([
            'accumulated_time' => 'required|integer',
            'last_sync_time' => 'required|date',
            'sessions' => 'required|array'
        ]);

        $this->userTimeTrackingService->syncUserTime(
            $request->user(),
            $validated['accumulated_time'],
            $validated['last_sync_time'],
            $validated['sessions']
        );

        return response()->json(['status' => 'success']);
    }
}
