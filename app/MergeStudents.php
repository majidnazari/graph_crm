<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;


class MergeStudents extends Model  implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'middle_table_for_merged_students';
    protected $fillable = [
       'main_students_id',
       'auxilary_students_id',
       'second_auxilary_students_id',
       'third_auxilary_students_id'
      ];

    public function mainStudent(){
        return $this->hasOne('App\Student', 'id', 'main_students_id');
    }
    public function auxilaryStudent(){
        return $this->hasOne('App\Student', 'id', 'auxilary_students_id');
    }
    public function secondAuxilaryStudent(){
        return $this->hasOne('App\Student', 'id', 'second_auxilary_students_id');
    }
    public function thirdAuxilaryStudent(){
        return $this->hasOne('App\Student', 'id', 'third_auxilary_students_id');
    }
    public function mainpurchases(){
        return $this->hasMany('App\Purchase', 'students_id', 'main_students_id')->where('is_deleted', false);
    }
    public function auxilarypurchases(){
        return $this->hasMany('App\Purchase', 'students_id', 'auxilary_students_id')->where('is_deleted', false);
    }
    public function secondAuxilarypurchases(){
        return $this->hasMany('App\Purchase', 'students_id', 'second_auxilary_students_id')->where('is_deleted', false);
    }
    public function thirdAuxilarypurchases(){
        return $this->hasMany('App\Purchase', 'students_id', 'third_auxilary_students_id')->where('is_deleted', false);
    }
}
