<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;


class TagParentFour extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    public function tags(){
        return $this->hasMany('App\Tag', 'parent4', 'id');
    }
}
