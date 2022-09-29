<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;


class Group extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    public function gates(){
        return $this->hasMany('App\GroupGate', 'groups_id', 'id');
    }

    public static function getSupport(){
        return Group::where('type', 'support')->first();
    }

    public static function getConsultant(){
        return Group::where('type', 'consultant')->first();
    }
}
