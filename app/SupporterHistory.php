<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;


class SupporterHistory extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    public function user(){
        return $this->hasOne('App\User', 'id', 'users_id');
    }
    public function student(){
        return $this->hasOne('App\Student', 'id', 'students_id');
    }
    public function supporter(){
        return $this->hasOne('App\User', 'id', 'supporters_id');
    }
}
