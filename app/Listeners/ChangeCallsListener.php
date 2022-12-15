<?php

namespace App\Listeners;

use App\Call;
use App\Events\ChangeAllStudentCallsEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Log;

class ChangeCallsListener
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
     * @param  \App\Events\ChangeAllStudentCallsEvent  $event
     * @return void
     */
    public function handle(ChangeAllStudentCallsEvent $event)
    {
        //Log::info("ChangeAllStudentCallsEvent");
        $update_student = Call::where('students_id', $event->second_student_id)->update(["students_id" => $event->main_student_id]);
    }
}
