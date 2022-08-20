<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;


class Tag extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    public function parent(){
        return $this->hasOne('App\Tag', 'id', 'parent_id');
    }

    public function user()
    {
        return $this->hasOne('App\User', 'id', 'users_id');
    }

    public function parent_one(){
        return $this->hasOne('App\TagParentOne', 'id', 'parent1');
    }

    public function parent_two(){
        return $this->hasOne('App\TagParentTwo', 'id', 'parent2');
    }

    public function parent_three(){
        return $this->hasOne('App\TagParentThree', 'id', 'parent3');
    }

    public function parent_four(){
        return $this->hasOne('App\TagParentFour', 'id', 'parent4');
    }

    public function need_parent_one(){
        return $this->hasOne('App\NeedTagParentOne', 'id', 'need_parent1');
    }

    public function need_parent_two(){
        return $this->hasOne('App\NeedTagParentTwo', 'id', 'need_parent2');
    }

    public function need_parent_three(){
        return $this->hasOne('App\NeedTagParentThree', 'id', 'need_parent3');
    }

    public function need_parent_four(){
        return $this->hasOne('App\NeedTagParentFour', 'id', 'need_parent4');
    }

    public function product(){
        return $this->hasOne('App\Product', 'id', 'products_id');
    }
}
