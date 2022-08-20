<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;


class Help extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'name',
        'link',
        'group_id'
    ];

    public function group(){
        $this->belongsTo('App\Group','id','group_id');
    }
}
