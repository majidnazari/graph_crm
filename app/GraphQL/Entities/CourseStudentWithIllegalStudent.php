<?php

namespace App\GraphQL\Entities;

use App\Student as StudentModel;

final class CourseStudentWithIllegalStudent
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($args)
    {
        $student = StudentModel::whereId($args["student_id"])->first();
        return ["__typename" => "CourseStudentWithIllegalStudent", "student" => $student];
    }

}
