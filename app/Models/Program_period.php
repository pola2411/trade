<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program_period extends Model
{
    use HasFactory;
    protected $guarded=['id'];

    public function period(){
        return $this->belongsTo(PeroidGlobel::class,'period_globel_id');
    }
    public function program(){
        return $this->belongsTo(Program::class,'program_id');
    }
}
