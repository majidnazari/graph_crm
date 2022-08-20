<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;


class TagParentTwo extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    public function tags(){
        return $this->hasMany('App\Tag', 'parent2', 'id');
    }
}
