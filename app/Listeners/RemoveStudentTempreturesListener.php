<?php

namespace App\Listeners;

use App\Events\RemoveAllStudentTempreturesEvent;
use App\StudentTemperature;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class RemoveStudentTempreturesListener
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
     * @param  \App\Events\RemoveAllStudentTempreturesEvent  $event
     * @return void
     */
    public function handle(RemoveAllStudentTempreturesEvent $event)
    {
        $delete_student = StudentTemperature::where('students_id', $event->main_student_id)->delete();
    }
}
