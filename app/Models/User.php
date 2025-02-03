<?php 
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable 
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'employee_id',
        'full_name',
        'phone',
        'email',
        'password',
        'role',
        'branch',
        'country',
        'status',
        'designation',
        'latitude',
        'longitude',
        'last_login_at',
        'check_in_time',
        'check_out_time',
        'check_in_latitude',
        'check_in_longitude',
        'check_out_latitude',
        'check_out_longitude'
    ];

    protected $hidden = [
        'password',
        'remember_token',
        
    ];

    
    protected $dates = [
        'last_login_at',
        'check_in_time',
        'check_out_time'
    ];

    public function projects()
{
    return $this->belongsToMany(Project::class, 'project_users');
}
}