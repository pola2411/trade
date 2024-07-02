<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use function Pest\Laravel\put;

class Program extends Model
{
    use HasFactory,SoftDeletes;
    protected $guarded=['id'];

    public function program_type(){
        return $this->belongsTo(ProgramTypes::class,'program_type_id');
    }
    public function periods(){
        return $this->hasMany(Program_period::class,'program_id');
    }
    public function fields(){
        return $this->hasMany(ProgramFields::class,'program_id');

    }
    public function contracts(){
        return $this->hasMany(ContractProgram::class,'program_id');
    }

}
