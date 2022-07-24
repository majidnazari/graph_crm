<?php

namespace App\GraphQL\Entities;

use App\Student as StudentModel;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Nuwave\Lighthouse\Execution\ErrorHandler;
use App\Exceptions\CustomException;
use Log;

final class CourseStudent
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($args)
    {
        $student = StudentModel::whereId($args["student_id"])->first();
        // Log::info("RES : " . $args["student_id"] . " " . json_encode($student));
        // TODO implement the resolver
        // Log::info("COURSE STUDENTS : " . json_encode($args));
        return ["__typename" => "CourseStudent", "student" => $student];
    }

}
