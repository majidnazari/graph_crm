<?php

namespace App\GraphQL\Mutations\Student;

use App\Student;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use GraphQL\Error\Error;

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
        
        $existed_student=Student::find($args['id']);
        
        if(!$existed_student)
        {
            return Error::createLocatedError("USER-UPDATE-RECORD_IS_NOT_EXIST");
           
        }
        // $is_exist_national_code=Student::where('nationality_code',$args['nationality_code'])
        // ->first();
        // if($is_exist_national_code)
        // {
        //     return Error::createLocatedError("USER-UPDATE-NATIONAL_NO_IS_EXIST");
        // }
        // $is_exist_phone=Student::where('phone',$args['phone'])->first();
        // if($is_exist_national_code)
        // {
        //     return Error::createLocatedError("USER-UPDATE-NATIONAL_NO_IS_EXIST");
        // }
        $student_filled= $existed_student->fill($args);
        $existed_student->save();       
       
        return $student_filled;
    }
}
