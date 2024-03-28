<?php

namespace App\GraphQL\Entities;

use App\Student as StudentModel;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Nuwave\Lighthouse\Execution\ErrorHandler;
use App\Exceptions\CustomException;
use Log;

final class ConsultantDefinitionDetailFlatModel
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($args)
    {
        $student = StudentModel::whereId($args["student_id"])->first();
        
        return ["__typename" => "ConsultantDefinitionDetailFlatModel", "student" => $student];
    }

}