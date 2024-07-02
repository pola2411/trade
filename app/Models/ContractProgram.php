<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContractProgram extends Model
{
    use HasFactory,SoftDeletes;
    protected $guarded=['id'];

    public function program(){
        return $this->belongsTo(Program::class,'program_id');
    }

}
