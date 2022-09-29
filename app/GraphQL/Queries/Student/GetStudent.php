<?php

namespace App\GraphQL\Queries\Student;

use App\Student;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Nuwave\Lighthouse\Execution\ErrorHandler;
use App\Exceptions\CustomException;
use Log;

final class GetStudent
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        // TODO implement the resolver
    }
    public function resolveStudentAttribute($root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        // $t=Student::find(1);
        // $user_role=auth()->guard('api')->user()->group->type; 
        // return  $user_role;   
        //Log::info(json_encode($context->request()));
        $Student=Student::find($args['id']);
        return $Student;
        
    }
    
}
