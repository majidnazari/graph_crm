<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\CreateLogMergedStudentEvent;
use App\Student;
use App\LogMergedStudent;
use Log;

class CreateLogMergedStudentListener
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
     * @param  object  $event
     * @return void
     */
    public function handle(CreateLogMergedStudentEvent $event)
    {
        return $this->LogeMerge($event->main_student_id,$event->second_student_id,$event->user_id_updater,$event->user_updater_fullname);
    }
    function LogeMerge( $main_student_id, $second_student_id,$user_id_updater,$user_updater_fullname){
        $second_student = Student::where('id', $second_student_id)->first();
        $main_student = Student::where('id', $main_student_id)->first();
        $log_merged_student=new LogMergedStudent;

        $log_merged_student->current_student_fullname= $main_student->first_name . " " .$main_student->last_name;
        $log_merged_student->current_student_phone= $main_student->phone;
        $log_merged_student->current_student_phone1= $main_student->phone1;
        $log_merged_student->current_student_phone2= $main_student->phone2;
        $log_merged_student->current_student_phone3= $main_student->phone3;
        $log_merged_student->current_student_phone4= $main_student->phone4;
        $log_merged_student->current_student_student_phone= $main_student->student_phone;
        $log_merged_student->current_student_mother_phone= $main_student->mother_phone;
        $log_merged_student->current_student_father_phone= $main_student->father_phone;
        $log_merged_student->current_student_id= $main_student->id;


        $log_merged_student->old_student_fullname= $second_student->first_name . " " . $second_student->last_name;
        $log_merged_student->old_student_phone= $second_student->phone;
        $log_merged_student->old_student_phone1= $second_student->phone1;
        $log_merged_student->old_student_phone2= $second_student->phone2;
        $log_merged_student->old_student_phone3= $second_student->phone3;
        $log_merged_student->old_student_phone4= $second_student->phone4;
        $log_merged_student->old_student_student_phone= $second_student->student_phone;
        $log_merged_student->old_student_mother_phone= $second_student->student_phone;
        $log_merged_student->old_student_father_phone= $second_student->student_phone;
        $log_merged_student->old_student_id=$second_student->id;

        $log_merged_student->user_id_updater=$user_id_updater;
        $log_merged_student->user_fullname_updater=$user_updater_fullname;
        $result=$log_merged_student->save();

        if($result)
        {
            log::info("the merge log is:" . $result);
            return true;
        }
        return false;
    }
}
