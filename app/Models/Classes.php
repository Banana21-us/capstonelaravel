<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Classes extends Model
{
    use HasFactory;
    protected $table = 'classes';
    protected $primaryKey = 'class_id';
    public $incrementing = false;
    public $timestamps = true;
    protected $fillable =[
        'admin_id',
        'section_id',
        'subject_id',
        'room',
        'time',
        'schedule'
    ];
    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id','section_id');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id','admin_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id', 'subject_id');
    }


}
