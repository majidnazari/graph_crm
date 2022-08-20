<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;


class StudentTag extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    public function tag(){
        return $this->hasOne('App\Tag', 'id', 'tags_id');
    }
}
