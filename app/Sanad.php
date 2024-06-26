<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;


class Sanad extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table="sanads";
    protected $appends = [
        'total_price',
        'total_debtor',
        'total_creditor',
        'total_total_cost',
        'total_supporter'
    ];
    
    protected $fillable=[
        "number",
        "description",
        "student_fullname",
        "total",
        "receipt_date",
        "total_cost",
        "supporter_percent" ,
        "supporter_id",
        "student_id",
        "user_id",
        "status",
        "type"
    ];
    public function getTotalPriceAttribute()
    {
        if($this->type>0)
            return $this->total;
        return 0;    
       // return $this->total;
    }
    public function getTotalTotalCostAttribute()
    {
        return $this->total_cost;
    }
    public function getTotalDebtorAttribute()
    {
        if($this->type<0)
            return $this->total;
        return 0;    
    }
    public function getTotalSupporterAttribute()
    {
        if($this->type>0)
            return ($this->supporter_percent/100 * $this->total);
        return 0;    
    }
    public function getTotalCreditorAttribute()
    {
        if($this->type<0)
            return $this->total;
        return 0;   
       // return $this->total;
    }
    public function supporter()
    {
        return $this->hasOne('App\User', 'id', 'supporter_id');
    }
    public function student()
    {
        return $this->hasOne('App\Student','id','student_id');
    }
}
