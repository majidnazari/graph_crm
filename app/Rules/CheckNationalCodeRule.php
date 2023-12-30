<?php

namespace App\Rules;

use App\Http\Resources\StudentResource;
use App\Student;
use Illuminate\Contracts\Validation\Rule;
use Log;
use stdClass;

class CheckNationalCodeRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    private $phone;
    private $err;
    private $id;
    public function __construct($phone, $id)
    {
        $this->phone = $phone;
        $this->id = $id;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // Log::info("the id is : " .$this->id ."the attribute is: ".$attribute .   "the value is: " . $value . "  and the type is : " . $this->user_type );

        return $this->CheckNationalCode($this->phone, $value, $this->id);
    }
    public function CheckNationalCode($phone, $national_code, $id)
    {        
        //Log::info("the id is :" . $id );
        $student = Student::where('nationality_code', $national_code)
        ->where('nationality_code','!=','1151847593')
        ->first();
        if ($student && $student['id'] != $id) {
            $date['id'] = $student['id'];
            $date['first_name'] = $student['first_name'];
            $date['last_name'] = $student['last_name'];

            $this->err =
                [
                    // "data" => $date // the object of student
                    "data" => "THIS NATIONAL CODE IS FOR STUDENT:". $student['first_name'] . " " . $student['last_name'],  // the string of student
                ];

            return false;
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->err;
    }
}
