<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\MergeStudents as AppMergeStudents;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{   
    use HasApiTokens, HasFactory, Notifiable;   

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function group(){
        return $this->hasOne('App\Group', 'id', 'groups_id');
    }
    public function arrOfAuxilaries($input, $arr)
    {
        if ($input) {
            $arr[] = $input;
        }
        return $arr;
    }

    public function students(){
        $megeStudents = AppMergeStudents::where('is_deleted', false)->get();
        $arr_of_auxilaries = [];
        foreach ($megeStudents as $index => $student) {
            $arr_of_auxilaries = $this->arrOfAuxilaries($student->auxilary_students_id, $arr_of_auxilaries);
            $arr_of_auxilaries = $this->arrOfAuxilaries($student->second_auxilary_students_id, $arr_of_auxilaries);
            $arr_of_auxilaries = $this->arrOfAuxilaries($student->third_auxilary_students_id, $arr_of_auxilaries);
        }
        return $this->hasMany('App\Student', 'supporters_id', 'id')->where('is_deleted', false)->where('banned', false)->where('archived', false)->whereNotIn('id', $arr_of_auxilaries);
    }
    // function calls is used for users that are supporter not others
    public function calls(){
        return $this->hasMany('App\Call','users_id','id')->where('is_deleted',false);
    }
    public function callresult()
    {
        return $this->hasOneThrough(
            'App\CallResult',
            'App\Call',
            'users_id',//Foreign key on calls table
            'id',//Foreign key on callresults table
            'id',//Local key on users table
            'call_results_id'//local key on calls table
            );
    }
}
