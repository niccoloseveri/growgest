<?php

namespace App\Models;

use App\Models\Document;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone_number',
        'description',
        'gia_cliente',
        'prima_fattura',
        'lead_source_id',
        'pipeline_stage_id',
        'employee_id',
        'nome_az',
        'is_azienda',
        'cf_azienda',
        'piva',
        'email_az',
        'tel_az',
        'website',
        'cod_univoco',
        'stato_az',
        'prov_az',
        'citta_az',
        'cap_az',
        'via_az',
        'stato_r',
        'prov_r',
        'citta_r',
        'cap_r',
        'via_r',
        'stato_c',
        'prov_c',
        'citta_c',
        'cap_c',
        'via_c',
        'stato_f',
        'prov_f',
        'citta_f',
        'cap_f',
        'via_f',
        'same_as_fatt',
        'note_spedizione',
        'settore_id',
        'mat_inps',
        'n_dipendenti',
        'aderente',
        'fondo',
    ];

    protected $casts = [
        'prima_fattura' => 'date',
    ];

    public function leadSource(): BelongsTo
    {
        return $this->belongsTo(LeadSource::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function pipelineStage(): BelongsTo
    {
        return $this->belongsTo(PipelineStage::class);
    }

    public function pipelineStageLogs(): HasMany
    {
        return $this->hasMany(CustomerPipelineStage::class);
    }

    public static function booted(): void
    {

        self::created(function (Customer $customer) {
            $customer->pipelineStageLogs()->create([
                'pipeline_stage_id' => $customer->pipeline_stage_id,
                'employee_id' => $customer->employee_id,
                'user_id' => auth()->check() ? auth()->id() : null
            ]);
        });

        self::updated(function (Customer $customer) {
            $lastLog = $customer->pipelineStageLogs()->whereNotNull('employee_id')->latest()->first();

            // Here, we will check if the employee has changed, and if so - add a new log
            if ($lastLog && $customer->employee_id !== $lastLog?->employee_id) {
                $customer->pipelineStageLogs()->create([
                    'employee_id' => $customer->employee_id,
                    'notes' => is_null($customer->employee_id) ? 'Employee removed' : '',
                    'user_id' => auth()->id()
                ]);
            }
        });
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function customFields(): HasMany
    {
        return $this->hasMany(CustomFieldCustomer::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function segnalatore(): BelongsTo
    {
        return $this->belongsTo(Personas::class);
    }

    public function fondo(): BelongsTo
    {
        return $this->belongsTo(Fondos::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function completedTasks(): HasMany
    {
        return $this->hasMany(Task::class)->where('is_completed', true);
    }

    public function incompleteTasks(): HasMany
    {
        return $this->hasMany(Task::class)->where('is_completed', false);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function completedAppointments(): HasMany
    {
        return $this->hasMany(Appointment::class)->where('is_completed', true);
    }

    public function incompleteAppointments(): HasMany
    {
        return $this->hasMany(Appointment::class)->where('is_completed', false);
    }

    /**
     * Get all of the quotes for the Customer
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function quotes(): HasMany
    {
        return $this->hasMany(Quote::class);
    }

    /**
     * Get the settore that owns the Customer
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function settore(): BelongsTo
    {
        return $this->belongsTo(Settore::class);
    }

    /**
     * The courses that belong to the customer.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Courses::class, 'courses_customers')
            ->withPivot('attivato');
    }

}
