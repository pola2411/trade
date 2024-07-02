<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $guarded=['id'];

    public function period(){
        return $this->belongsTo(PeroidGlobel::class,'peroid_globle');
    }
    public function customer(){
        return $this->belongsTo(Customer::class,'customer_id');
    }
    public function status(){
        return $this->belongsTo(orderStatus::class,'order_status');
    }
    public function bank(){
        return $this->belongsTo(Banks::class,'bank_id');
    }
    public function program_type(){
        return $this->belongsTo(ProgramTypes::class,'program_type_id');
    }
    public function installments(){
        return $this->hasMany(orderInstallment::class,'order_id');
    }
    public function contracts(){
        return $this->hasMany(orderContract::class,'order_id');
    }
}
