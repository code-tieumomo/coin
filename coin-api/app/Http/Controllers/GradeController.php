<?php

namespace App\Http\Controllers;

use App\Events\PrivateNotification;
use App\Models\Subnet;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    use ApiResponse;

    public function store(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'subnet_id' => 'required|integer|exists:subnets,id',
            'comment' => 'nullable|string',
            'grade' => 'required|numeric',
            'submitted_at' => 'nullable|date',
            'graded_at' => 'nullable|date',
        ]);

        $token = $request->get('token');
        $user = User::where('token', $token)->first();
        if (!$user) {
            return $this->error('Invalid token.', 401);
        }

        $subnetId = $request->get('subnet_id');
        $subnet = Subnet::find($subnetId);
        if (!$subnet) {
            return $this->error('Subnet not found.', 404);
        }

        if (!$subnet->users->contains($user)) {
            return $this->error('User is not a member of the subnet.', 403);
        }

        $user->grades()->create([
            'subnet_id' => $subnetId,
            'grade' => $request->get('grade'),
            'comment' => $request->get('comment'),
            'submitted_at' => $request->get('submitted_at'),
            'graded_at' => $request->get('graded_at'),
        ]);

        $notification = $user->notifications()->create([
            'title' => 'Your submission has been graded.',
            'type' => 'success',
            'content' => 'Your submission in ' 
                . $subnet->name 
                . ' has been graded. You received a grade of ' 
                . $request->get('grade')
                . '. You can go to home page (and refresh) to see the progress of your assignment.',
        ]);
        PrivateNotification::dispatch($notification);

        return $this->success(null, 'Grade stored successfully.');
    }
}
