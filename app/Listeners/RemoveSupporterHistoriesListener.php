<?php

namespace App\Listeners;

use App\Events\RemoveAllSupporterHistoriesEvent;
use App\SupporterHistory;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class RemoveSupporterHistoriesListener
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
    public function handle(RemoveAllSupporterHistoriesEvent $event)
    {
        $delete_student = SupporterHistory::where('students_id', $event->second_student_id)->delete();
    }
}
