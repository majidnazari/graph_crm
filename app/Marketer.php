<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;


class Marketer extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    public function user()
    {
        return $this->hasOne('App\User', 'id', 'users_id');
    }
}
