<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CreateLogMergedStudentEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $main_student_id;
    public $second_student_id;
    public $user_id_updater;
    public $user_updater_fullname;
    public function __construct($main_student_id, $second_student_id,$user_id,$user_fullname)
    {
        $this->main_student_id = $main_student_id;
        $this->second_student_id = $second_student_id;

        $this->user_id_updater = $user_id;
        $this->user_updater_fullname = $user_fullname;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
