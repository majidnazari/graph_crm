<?php

namespace App\GraphQL\Entities;

use App\Student as StudentModel;

final class StudentContact
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($args)
    {
        $student = StudentModel::whereId($args["student_id"])->first();
        return ["__typename" => "StudentContact", "student" => $student];
    }

}
