<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['name'];


    //role has many users
    public function users()
    {
        return $this->hasMany(User::class,'role_id');
    }

    //role has many personas
    public function personas()
    {
        return $this->hasMany(Personas::class,'role_id');
    }

}
