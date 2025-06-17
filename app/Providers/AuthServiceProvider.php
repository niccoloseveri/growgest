<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Filament\Pages\AppointmentCalendar;
use App\Filament\Resources\FieraCustomerResource;
use App\Models\PersonalTrainerDate;
use App\Models\Product;
use App\Models\ProductQuote;
use App\Models\Quote;
use App\Models\TechnicalAssistence;
use App\Models\Ticket;
use App\Policies\NewFeaturesPolicy;
use App\Policies\UserActivityPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        \Edwink\FilamentUserActivity\Models\UserActivity::class => UserActivityPolicy::class,
        //
        Ticket::class => NewFeaturesPolicy::class,
        TechnicalAssistence::class => NewFeaturesPolicy::class,
        //AppointmentCalendar::class => NewFeaturesPolicy::class,
        //FieraCustomerResource::class => NewFeaturesPolicy::class,
        PersonalTrainerDate::class => NewFeaturesPolicy::class,
        Product::class => NewFeaturesPolicy::class,
        ProductQuote::class => NewFeaturesPolicy::class,
        Quote::class => NewFeaturesPolicy::class,

    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
