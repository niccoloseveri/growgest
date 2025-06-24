<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Mokhosh\FilamentKanban\Concerns\IsKanbanStatus;

class PipelineStage extends Model
{
    use HasFactory;
    //use IsKanbanStatus;

    protected $fillable = [
        'name',
        'position',
        'is_default',
    ];



    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }
}
