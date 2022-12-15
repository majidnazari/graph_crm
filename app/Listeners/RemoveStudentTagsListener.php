<?php

namespace App\Listeners;

use App\Events\RemoveAllStudentTagsEvent;
use App\StudentTag;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

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
        $delete_student = StudentTag::where('students_id', $event->main_student_id)->delete();
    }
}
