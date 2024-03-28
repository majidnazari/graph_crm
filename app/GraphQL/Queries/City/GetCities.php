<?php

namespace App\GraphQL\Queries\City;

use App\City;
use App\Student;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Nuwave\Lighthouse\Execution\ErrorHandler;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\DB;
use Log;

final class GetCities
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        // TODO implement the resolver
    }
    public function resolveCitiesAttribute($root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        
        $Cities = City::where('is_deleted', 0);
        
        //Log::info(json_encode($Student->toSql()));
        return $Cities;
    }
}
