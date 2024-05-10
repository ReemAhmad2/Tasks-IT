<?php

namespace App\Http\Controllers\Api\Notification;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Traits\GeneralTrait;

class NotificationStudentController extends Controller
{

    use GeneralTrait;

    public function allNotifications()
    {
        $user = Auth::user();
        $notifications = NotificationResource::collection($user->notifications);
        return $this->apiResponse($notifications);
    }

    // public function readed()
    // public function unreaded()
}
