<?php

namespace App\Listeners;

use App\Events\ChangeAllStudentPurchasesEvent;
use App\Purchase;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Log;
class ChangePurchasesListener
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
     * @param  \App\Events\ChangeAllStudentPurchasesEvent  $event
     * @return void
     */
    public function handle(ChangeAllStudentPurchasesEvent $event)
    {
        //Log::info("Purchases");
        $update_student = Purchase::where('students_id', $event->second_student_id)->update(["students_id" => $event->main_student_id]);
    }
}
