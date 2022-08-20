<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;


class StudentTemperature extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    public function temperature(){
        return $this->hasOne('App\Temperature', 'id', 'temperatures_id');
    }
}
