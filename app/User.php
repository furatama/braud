<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable implements JWTSubject
{
    use \App\BDSM\ModelHelper;
    use SoftDeletes;
    use Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'name','password','username','role','isconfirmed','isactive'
    ];
    
    protected $hidden = [
        'password', 'remember_token','created_at','updated_at'
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function getJWTCustomClaims()
    {
        return [];
    }
}
