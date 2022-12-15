<?php

namespace App\Listeners;

use App\Events\RemoveAllStudentCollectionsEvent;
use App\StudentCollection;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class RemoveStudentCollectionsListener
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
     * @param  \App\Events\RemoveAllStudentCollectionsEvent  $event
     * @return void
     */
    public function handle(RemoveAllStudentCollectionsEvent $event)
    {
        $delete_student = StudentCollection::where('students_id', $event->main_student_id)->delete();
    }
}
