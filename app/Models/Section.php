<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;
    protected $table = 'sections'; // Optional if your table name matches the model name
    protected $primaryKey = 'section_id'; // Ensure this is set correctly
    protected $fillable =[
        'section_name',
        'grade_level',
        'strand'
    ];
    public function classes()
    {
        return $this->hasMany(Classes::class);
    }

}
