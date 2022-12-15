<?php

namespace App\Listeners;

use App\Events\RemoveAllStudentHistoriesEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class RemoveStudentHistoriesListener
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
     * @param  \App\Events\RemoveAllStudentHistoriesEvent  $event
     * @return void
     */
    public function handle(RemoveAllStudentHistoriesEvent $event)
    {
        //$delete_student = StudentHistories::where('student_id', $event->main_student_id)->delete();
    }
}
