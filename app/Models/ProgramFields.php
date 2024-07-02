<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramFields extends Model
{
    use HasFactory;
    protected $guarded=['id'];

    public function program(){
        return $this->belongsTo(Program::class,'program_id');
    }
    public function field(){
        return $this->belongsTo(Fields::class,'field_id');
    }
}
