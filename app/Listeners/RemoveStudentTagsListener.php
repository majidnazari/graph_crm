<?php

namespace App\Listeners;

use App\Events\RemoveAllStudentTagsEvent;
use App\StudentTag;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Log;

class RemoveStudentTagsListener
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
     * @param  \App\Events\RemoveAllStudentTagsEvent  $event
     * @return void
     */
    public function handle(RemoveAllStudentTagsEvent $event)
    {
        //Log::info("Tags");
        //Log::info($event->second_student_id);
        $delete_student = StudentTag::where('students_id', $event->second_student_id)->delete();
    }
}
