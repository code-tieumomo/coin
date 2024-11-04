<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserDailyOnlineTime;
use App\Models\UserOnlineTimeLog;
use Carbon\Carbon;
use DB;
use Log;

class UserTimeTrackingService
{
    /**
     * Sync user's time tracking data from the frontend
     *
     * @param User $user The user whose time data is being synced
     * @param int $accumulatedTime Total time in seconds
     * @param string $lastSyncTime Last sync timestamp
     * @param array $sessions Array of session data
     * @return bool
     */
    public function syncUserTime(
        User $user,
        int $accumulatedTime,
        string $lastSyncTime,
        array $sessions
    ): bool {
        try {
            DB::beginTransaction();

            // Convert lastSyncTime to Carbon instance
            $syncTime = Carbon::parse($lastSyncTime);
            $today = Carbon::today();

            // Process each session
            foreach ($sessions as $session) {
                $sessionStart = Carbon::parse($session['start']);
                $sessionEnd = isset($session['end']) 
                    ? Carbon::parse($session['end'])
                    : Carbon::now();
                
                // Calculate session duration in minutes
                $durationMinutes = $sessionEnd->diffInMinutes($sessionStart);

                // Create session log
                $this->createSessionLog($user, $sessionStart, $sessionEnd, $durationMinutes);

                // Update daily totals
                $this->updateDailyTotal($user, $sessionStart->toDateString(), $durationMinutes);
            }

            // Update user's last sync time
            $user->update([
                'last_time_sync' => Carbon::now(),
                'total_time_today' => $accumulatedTime
            ]);

            // Log successful sync
            Log::info('User time sync successful', [
                'user_id' => $user->id,
                'accumulated_time' => $accumulatedTime,
                'sessions_count' => count($sessions)
            ]);

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Failed to sync user time', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return false;
        }
    }

    /**
     * Create a session log entry
     */
    private function createSessionLog(
        User $user,
        Carbon $start,
        Carbon $end,
        int $durationMinutes
    ): void {
        UserOnlineTimeLog::create([
            'user_id' => $user->id,
            'date' => $start->toDateString(),
            'session_start' => $start,
            'session_end' => $end,
            'duration_minutes' => $durationMinutes,
            'metadata' => [
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'device_type' => $this->detectDeviceType(),
            ]
        ]);
    }

    /**
     * Update or create daily total
     */
    private function updateDailyTotal(
        User $user,
        string $date,
        int $minutes
    ): void {
        UserDailyOnlineTime::updateOrCreate(
            [
                'user_id' => $user->id,
                'date' => $date
            ],
            [
                'total_minutes' => DB::raw("total_minutes + $minutes")
            ]
        );
    }

    /**
     * Detect device type from user agent
     */
    private function detectDeviceType(): string
    {
        $userAgent = request()->userAgent();
        
        if (preg_match('/(tablet|ipad|playbook)|(android(?!.*(mobi|opera mini)))/i', $userAgent)) {
            return 'tablet';
        }
        
        if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|android|iemobile)/i', $userAgent)) {
            return 'mobile';
        }
        
        return 'desktop';
    }

    /**
     * Get daily stats with additional metrics
     */
    public function getDailyStats(User $user, string $date): array
    {
        $dailyTotal = UserDailyOnlineTime::where('user_id', $user->id)
            ->where('date', $date)
            ->first();

        $sessions = UserOnlineTimeLog::where('user_id', $user->id)
            ->where('date', $date)
            ->orderBy('session_start', 'desc')
            ->get();

        $sessionStats = $sessions->map(function ($session) {
            return [
                'start' => $session->session_start->format('H:i:s'),
                'end' => $session->session_end->format('H:i:s'),
                'duration' => $session->duration_minutes,
                'device' => $session->metadata['device_type'] ?? 'unknown'
            ];
        });

        // Calculate additional metrics
        $metrics = [
            'total_minutes' => $dailyTotal?->total_minutes ?? 0,
            'session_count' => $sessions->count(),
            'average_session_length' => $sessions->avg('duration_minutes') ?? 0,
            'longest_session' => $sessions->max('duration_minutes') ?? 0,
            'device_breakdown' => $sessions->groupBy('metadata.device_type')
                ->map(fn($group) => $group->count())
                ->toArray()
        ];

        return [
            'date' => $date,
            'metrics' => $metrics,
            'sessions' => $sessionStats
        ];
    }
}
