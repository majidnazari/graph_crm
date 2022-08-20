<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;


class Product extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    public function collection(){
        return $this->hasOne('App\Collection', 'id', 'collections_id');
    }

    public function classrooms(){
        return $this->hasMany("App\ClassRoom", "products_id", "id");
    }
}
