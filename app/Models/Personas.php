<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Personas extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'surname',
        'email',
        'phone',
        'description',
        'role_id',
    ];
    /**
     * Get the customers associated with the employee.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function customers() : HasMany
    {
        return $this->hasMany(Customer::class, 'employee_id');

    }

    /**
     * Get the role associated with the persona.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }


}
