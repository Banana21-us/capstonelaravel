<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;
    protected $table = 'subjects';
    protected $primaryKey = 'subject_id';
    public $incrementing = false; 
    protected $fillable = ['subject_id','subject_name', 'grade_level', 'strand','image'];
    public function classes()
    {
        return $this->hasMany(Classes::class);
    }
}
