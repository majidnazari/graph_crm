<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;


class MessageFlow extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    public function user(){
        return $this->hasOne('App\User', 'id', 'users_id');
    }

    public function themessage(){
        return $this->hasOne('App\Message', 'id', 'messages_id');
    }

    public function sender(){
        return $this->hasOne('App\User', 'id', 'sender_id');
    }
}
