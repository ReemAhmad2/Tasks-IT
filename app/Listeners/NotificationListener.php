<?php

namespace App\Listeners;

use App\Events\NewTaskEvent;
use App\Notifications\NewTaskNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class NotificationListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\NewTaskEvent  $event
     * @return void
     */
    public function handle(NewTaskEvent $event)
    {
        $task = $event->task;

        $categories = $task->categories;

        $students = [];
        $users = [];
        foreach ($categories as $category) {

            $students = $category->students;

            foreach ($students as $student) {
                $users[] = $student->user;
            }
        }

        Notification::send($users,new NewTaskNotification($task));

    }
}
