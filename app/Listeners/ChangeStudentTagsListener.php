<?php

namespace App\Listeners;

use App\Events\ChangeAllStudentTagsEvent;
use App\StudentTag;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Log;

class ChangeStudentTagsListener
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
     * @param  \App\Events\ChangeAllStudentTagsEvent  $event
     * @return void
     */
    public function handle(ChangeAllStudentTagsEvent $event)
    {
        //Log::info("Tags");
        //Log::info($event->second_student_id);
        //$delete_student = StudentTag::where('students_id', $event->second_student_id)->delete();
        $update_student = StudentTag::where('students_id', $event->second_student_id)->update(["students_id" => $event->main_student_id]);
    }
}
