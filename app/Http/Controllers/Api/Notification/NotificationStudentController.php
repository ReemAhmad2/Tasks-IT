<?php

namespace App\Http\Controllers\Api\Notification;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class NotificationStudentController extends Controller
{
    public function allNotifications()
    {
        $user = Auth::user();
        return NotificationResource::collection($user->notifications);
    }

    // public function readed()
    // public function unreaded()
}
