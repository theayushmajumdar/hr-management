<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceRecord extends Model
{
    protected $fillable = [
        'user_id', 
        'date', 
        'check_in_time', 
        'check_out_time', 
        'check_in_latitude', 
        'check_in_longitude', 
        'check_out_latitude', 
        'check_out_longitude'
    ];
}