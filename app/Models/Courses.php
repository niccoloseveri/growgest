<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Courses extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'pianos_id',
        'strumentos_id',
        'partecipanti_previsti',
        'ore_totali',
        'data_inizio',
        'data_fine',
        'stato',
        'valore_corso',
        'budget_lordo_cfe',
        'description',
        'scadenza',
    ];
    /**
     * Get the piano associated with the course.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function piano(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Pianos::class, 'pianos_id');
    }
    /**
     * Get the strumento associated with the course.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function strumento(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Strumentos::class, 'strumentos_id');
    }
}
