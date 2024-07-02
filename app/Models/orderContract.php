<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class orderContract extends Model
{
    use HasFactory;
    protected $guarded=['id'];

    public function order(){
        return $this->hasMany(Order::class,'order_id');
    }
    public function contract(){
        return $this->belongsTo(ContractProgram::class,'contract_id');
    }
}
