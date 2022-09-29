<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;


class StudentClassRoom extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    public function student(){
        return $this->hasOne('App\Studnet', 'id', 'students_id');
    }

    public function class(){
        return $this->hasOne('App\ClassRoom', 'id', 'class_rooms_id');
    }
}
