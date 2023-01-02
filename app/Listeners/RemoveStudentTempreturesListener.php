<?php

namespace App\Listeners;

use App\Events\RemoveAllStudentTempreturesEvent;
use App\StudentTemperature;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Log;

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
        //Log::info("Tempretures");
        //Log::info($event->second_student_id);
        $delete_student = StudentTemperature::where('students_id', $event->second_student_id)->delete();
    }
}
