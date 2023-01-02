<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;

class LogMergedStudent extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    use SoftDeletes;
    protected $fillable=[
        "current_student_fullname",
        "current_student_phone",
        "current_student_phone1",
        "current_student_phone2",
        "current_student_phone3",
        "current_student_phone4",
        "current_student_student_phone",
        "current_student_mother_phone",
        "current_student_father_phone",
        "current_student_id",
        "old_student_fullname",
        "old_student_phone",
        "old_student_phone1",
        "old_student_phone2",
        "old_student_phone3",
        "old_student_phone4",
        "old_student_student_phone",
        "old_student_mother_phone",
        "old_student_father_phone",
        "old_student_id",        
        "user_id_updater",        
        "user_fullname_updater",        
    ];
    public function userUpdater()
    {
        return $this->belongsTo(User::class,"user_id_updater");
    }
    public function oldStudent()
    {
        return $this->belongsTo(Student::class,"old_student_id");
    }
    public function currentStudent()
    {
        return $this->belongsTo(Student::class,"current_student_id");
    }
}
