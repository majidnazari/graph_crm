<?php

namespace App\GraphQL\Mutations\Student;

use App\Student;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

final class CreateStudent
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        // TODO implement the resolver
    }
    public function resolver($rootValue, array $args, GraphQLContext $context = null, ResolveInfo $resolveInfo)
    {     
        //return auth()->guard('api')->user()->id;   
       // $user_id=auth()->guard('api')->user()->id;
        $student=[
            // 'user_id_creator' => 1,
            // "course_id" => $args['course_id'],
            // "course_session_id" => $args['course_session_id'],            
            // 'isSMSsend' => $args['isSMSsend'],
            // "score"=> $args["score"],

            'phone' => $args['phone'],
            'first_name' => $args['first_name'],
            'last_name'=> $args['last_name'],
            //'level'=> $args['level'],
            'egucation_level'=> $args['egucation_level'],
            'parents_job_title'=> $args['parents_job_title'],
            'home_phone'=> $args['home_phone'],
            'father_phone'=> $args['father_phone'],
            'mother_phone'=> $args['mother_phone'],
            //'school'=> $args['school'],
            //'average'=> $args['average'],
            'major'=> isset($args['major']) ? $args['major'] : "other",
            //'introducing'=> $args['introducing'],
            //'student_phone'=> $args['student_phone'],
            //'citys_id'=> $args['citys_id'],
            //'sources_id'=> $args['sources_id'],
            //'supporters_id'=> $args['supporters_id'],
            //'archived'=> $args['archived'],
            'description' => $args['description']          
            
        ];
        if($existed_student=Student::where('phone',$args['phone'])->first())
                return $existed_student;
        $student_result=Student::create($student);
        return $student_result;
    }
}
