<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fields extends Model
{
    use HasFactory;
    protected $guarded=['id'];
    public function field_type(){
        return $this->belongsTo(FieldsType::class,'field_type');
    }
}
