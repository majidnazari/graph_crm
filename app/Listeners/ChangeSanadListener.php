<?php

namespace App\Listeners;

use App\Events\ChangeAllStudentSanadsEvent;
use App\Sanad;
use App\Student;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Log;

class ChangeSanadListener
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
     * @param  \App\Events\ChangeAllStudentSanadEvent  $event
     * @return void
     */
    public function handle(ChangeAllStudentSanadsEvent $event)
    {
        // Log::info("ChangeAllStudentSanadsEvent");
        // Log::info($event->main_student_id);
        // Log::info($event->second_student_id);
        //Log::info("Sanads");
        $update_student = Sanad::where('student_id', $event->second_student_id)->update(["student_id" => $event->main_student_id]);
        //Log::info($update_student);

    }
}
