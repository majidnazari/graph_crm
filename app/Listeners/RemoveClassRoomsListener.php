<?php

namespace App\Listeners;

use App\Events\RemoveAllStudentFromClassRoomEvent;
use App\StudentClassRoom;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Log;

class RemoveClassRoomsListener
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
     * @param  \App\Events\RemoveAllStudentFromClassRoomEvent  $event
     * @return void
     */
    public function handle(RemoveAllStudentFromClassRoomEvent $event)
    {
        //Log::info("ClassRoom");
        //Log::info($event->second_student_id);
        $delete_student = StudentClassRoom::where('students_id', $event->second_student_id)->delete();
    }
}
