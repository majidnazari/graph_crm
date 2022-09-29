<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;


class Message extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    public function flows(){
        return $this->hasMany("App\MessageFlow", "messages_id", "id");
    }

    public function user(){
        return $this->hasOne('App\User', 'id', 'users_id');
    }
}
