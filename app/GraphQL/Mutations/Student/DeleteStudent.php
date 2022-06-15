<?php

namespace App\GraphQL\Mutations\Student;

use App\Student;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

final class DeleteStudent
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
        $student=Student::where('id',$args['id'])->where('is_deleted',0)->first();
        
        if(!$student)
        {
            return [
                'status'  => 'Error',
                'message' => __('cannot Delete student'),
            ];
        }
        $student_filled= $student->is_deleted=1;
        $student->save();       
       
        return $student;
    }
}
