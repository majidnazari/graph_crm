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

    //    $first_name=isset($args['first_name']) ? $args['first_name'] : "";
    //    $last_name=isset($args['last_name']) ? $args['last_name'] : "";
       $full_name=isset($args['full_name']) ? $args['full_name'] : "";
    //  Log::info("full name is:". $full_name );

        // Log::info(json_encode($context->request()));
        $Student=Student::where('is_deleted', 0)
        ->where(DB::raw('CONCAT(first_name, \' \',last_name)'), 'like', "%".$full_name . "%");
        return $Student;
        
    }
}
