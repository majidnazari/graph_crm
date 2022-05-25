<?php

namespace App\GraphQL\Queries\Student;

use App\Student;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Nuwave\Lighthouse\Execution\ErrorHandler;
use App\Exceptions\CustomException;


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
        $Student=Student::find($args['id']);
        return $Student;
        
    }
    
}
