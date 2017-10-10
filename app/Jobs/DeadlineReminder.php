<?php

namespace App\Jobs;

use App\Notifications\DeadlineReminderNotification;
use App\Task;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class DeadlineReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $task;
    protected $hours;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Task $task, $hours)
    {
        $this->task = $task;
        $this->hours = $hours;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $notifiable_user = User::find($this->task['user_id']);

        $message = "your task (" . $this->task['title'] . ") is about to reach deadline date. (it will end after $this->hours hours)";

        $notifiable_user->notify(new DeadlineReminderNotification($message));
    }
}
