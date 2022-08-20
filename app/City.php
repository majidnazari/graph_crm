<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;


class City extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    public function province(){
        return $this->hasOne('App\Province', 'id', 'provinces_id');
    }
}
