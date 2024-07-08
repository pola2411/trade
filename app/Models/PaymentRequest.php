<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentRequest extends Model
{
    use HasFactory;
    protected $guarded=['id'];

    public function account(){
        return $this->belongsTo(Account::class,'account_id');
    }
    public function payment(){
        return $this->belongsTo(Payments::class,'payment_id');
    }
    public function approved_by(){
        return $this->belongsTo(User::class,'approved_by');
    }
}
