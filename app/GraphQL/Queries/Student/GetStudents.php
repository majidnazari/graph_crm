<?php

namespace App\GraphQL\Queries\Student;

use App\Student;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Nuwave\Lighthouse\Execution\ErrorHandler;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\DB;
use Log;

final class GetStudents
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        // TODO implement the resolver
    }
    public function resolveStudentsAttribute($root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        //Log::info($args);
        $first_name = isset($args['first_name']) ? $args['first_name'] : "";
        $last_name = isset($args['last_name']) ? $args['last_name'] : "";
        $full_name = isset($args['full_name']) ? $args['full_name'] : "";
        $full_name=$full_name !=="" ? $full_name : $first_name . ' '.$last_name;
        //  Log::info("full name is:". $full_name );
        $phone = isset($args['phone']) ? $args['phone'] : "";
        // Log::info(json_encode($context->request()));
        $Student = Student::where('is_deleted', 0)
            ->where('archived', 0)
            ->where('banned', 0)
            ->where(DB::raw('CONCAT(first_name, \' \',last_name)'), 'like', "%" . $full_name . "%")
            //->orWhere('is_academy_student1', 1)
            ->orWhere(function ($query) use ($full_name) {
                // if (isset($args['phone']) && ($args['phone']!="")) {
                //     $query->where('phone','like',"%". $args['phone'] . "%");
                // }
                $query->where('is_academy_student', 1)
                    // ->where('archived', 0)
                    //->where('banned', 0)
                    ->where(DB::raw('CONCAT(first_name, \' \',last_name)'), 'like', "%" . $full_name . "%");
            })
            ->where(function ($query) use ($phone) {
                //Log::info("inner where in run");
                if ($phone !== "") {
                    $query->where('phone', 'like', "%" . $phone . "%")
                        ->orWhere('phone1', 'like', "%" . $phone . "%")
                        ->orWhere('phone2', 'like', "%" . $phone . "%")
                        ->orWhere('phone3', 'like', "%" . $phone . "%")
                        ->orWhere('phone4', 'like', "'%" . $phone . "%'")
                        ->orWhere('father_phone', 'like', "%" . $phone . "%")
                        ->orWhere('mother_phone', 'like', "%" . $phone . "%")
                        ->orWhere('student_phone', 'like', "%" . $phone . "%");
                }
            });


        // Log::info("the student query is: ");    
        //Log::info(json_encode($Student->toSql()));
        return $Student;
    }
}
