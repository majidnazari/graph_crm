<?php

namespace App;

//use Attribute;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use SoftDeletes;
    protected $fillable = [
        
        'phone',
        'is_academy_student',
        'first_name',
        'last_name',
        'nationality_code',
        'concours_year',
        'level',
        'egucation_level',
        'parents_job_title',
        'home_phone',
        'father_phone',
        'mother_phone',
        'school',
        'average',
        'major',
        'introducing',
        'student_phone',
        'cities_id',
        'sources_id',
        'supporters_id',
        'archived',
        'description',
       
    ];
    protected $columns = ['id', 'level', 'first_name', 'last_name','last_year_grade','consultants_id','parents_job_title','home_phone','mother_phone','father_phone','phone','school','created_at','updated_at','introducing','student_phone','sources_id','supporters_id','is_deleted','users_id','marketers_id','average','password','viewed','major','egucation_level','concours_year','provinces_id','is_from_site','description','supporter_seen','saloon','supporter_start_date','banned','cities_id','archived','own_purchases','other_purchases','today_purchases']; // add all columns from you table

    public function scopeExclude($query, $value = [])
    {
        return $query->select(array_diff($this->columns, (array) $value));
    }

    public function user()
    {
        return $this->hasOne('App\User', 'id', 'users_id');
    }
    public function user_editor()
    {
        return $this->hasOne('App\User', 'id', 'users_id_editor');
    }

    public function source()
    {
        return $this->hasOne('App\Source', 'id', 'sources_id');
    }

    public function studenttags()
    {
        return $this->hasMany('App\StudentTag', 'students_id', 'id')->where('is_deleted', false);
    }

    public function studenttemperatures()
    {
        return $this->hasMany('App\StudentTemperature', 'students_id', 'id')->where('is_deleted', false);
    }

    public function consultant()
    {
        return $this->hasOne('App\User', 'id', 'consultants_id');
    }

    public function supporter()
    {
        return $this->hasOne('App\User', 'id', 'supporters_id');
    }

    public function purchases()
    {
        return $this->hasMany('App\Purchase', 'students_id', 'id')->where('is_deleted', false);
    }
    public function studentcollections()
    {
        return $this->hasMany('App\StudentCollection', 'students_id', 'id')->where('is_deleted', false);
    }

    public function calls() 
    {
        return $this->hasMany('App\Call', 'students_id', 'id')->where('is_deleted', false)->orderBy('created_at', 'desc');
    }

    public function remindercalls()
    {
        return $this->hasMany('App\Call', 'students_id', 'id')->where('is_deleted', false)->where('next_call', '!=', null);
    }


    public function studentclasses()
    {
        return $this->hasMany('App\StudentClassRoom', 'students_id', 'id');
    }


    public function mergestudent()
    {
        return $this->hasOne('App\MergeStudents', 'main_students_id', 'id')->where('is_deleted', false);
    }

    public function mergeauxilarystudent()
    {
        return $this->hasOne('App\MergeStudents', 'auxilary_students_id', 'id')->where('is_deleted', false);
    }
    public function mergesecondauxilarystudent()
    {
        return $this->hasOne('App\MergeStudents', 'second_auxilary_students_id', 'id')->where('is_deleted', false);
    }
    public function mergethirdauxilarystudent()
    {
        return $this->hasOne('App\MergeStudents', 'third_auxilary_students_id', 'id')->where('is_deleted', false);
    }
    
    // protected function phone(): Attribute
    // {
    //     return Attribute::make(
    //         get: fn ($value) => substr_replace($value,"****",4,4),
    //     );
    // }
}
