<?php

namespace App\GraphQL\Mutations\Student;

use App\Student;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

final class UpdateStudent
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
        //$user_id=auth()->guard('api')->user()->id;
        //$user_id=auth()->guard('api')->user()->id;
        //$args["user_id_creator"]=$user_id;
        $student=Student::find($args['id']);
        
        if(!$student)
        {
            return [
                'status'  => 'Error',
                'message' => __('cannot update student'),
            ];
        }
        $student_filled= $student->fill($args);
        $student->save();       
       
        return $student;
    }
}
