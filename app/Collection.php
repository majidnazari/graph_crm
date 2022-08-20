<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;


class Collection extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    public function parent(){
        return $this->hasOne('App\Collection', 'id', 'parent_id');
    }

    public function parents(){
        $parents = "";
        $ths = $this;
        $parent = $ths->parent()->first();
        while($parent){
            if($parents!="")
                $parents = $parent->name . "->" . $parents;
            else
                $parents = $parent->name;
            $ths = $parent;
            $parent = $ths->parent()->first();
        }
        return $parents;
    }
}
