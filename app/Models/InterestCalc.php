<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InterestCalc extends Model
{
    use HasFactory;
    protected $guarded=['id'];

    public function period(){
    return $this->belongsTo(PeroidGlobel::class,'period_globle_id');
    }

}
