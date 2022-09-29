<?php

namespace App\GraphQL\Queries\Student;

use App\Student;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Nuwave\Lighthouse\Execution\ErrorHandler;
use App\Exceptions\CustomException;
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
       // Log::info(json_encode($context->request()));
        $Student=Student::where('is_deleted', 0);
        return $Student;
        
    }
}
