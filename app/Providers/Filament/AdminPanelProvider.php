<?php

namespace App\Providers\Filament;

use Edwink\FilamentUserActivity\FilamentUserActivityPlugin;
use Filament\Facades\Filament;
use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Navigation\NavigationGroup;
use Filament\Http\Middleware\Authenticate;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\SpatieLaravelTranslatablePlugin;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Saade\FilamentFullCalendar\FilamentFullCalendarPlugin;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use LaraZeus\Bolt\BoltPlugin;
use LaraZeus\Thunder\ThunderPlugin;
use TomatoPHP\FilamentNotes\Filament\Resources\NoteResource;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->brandLogo(asset('vendor/logogp.png'))
            ->darkModeBrandLogo(asset('vendor/logogpdarkmode.png'))
            ->brandLogoHeight('3.5rem')
            ->id('admin')
            ->path('admin')
            ->login()
            ->passwordReset()
            ->emailVerification()
            ->profile()
            ->colors([
                //'primary' => '#454745',
                'primary' => '#61d5ed',
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                //Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                \TomatoPHP\FilamentNotes\Filament\Widgets\NotesWidget::class,

                Widgets\AccountWidget::class,
                //Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->navigationGroups([

                NavigationGroup::make()
                    ->label('Clienti')
                    //->icon('heroicon-o-cog-6-tooth')
                    ->collapsed(),
                NavigationGroup::make()
                    ->label('Personal Trainer')
                    //->icon('heroicon-o-cog-6-tooth')
                    ->collapsed(),
                NavigationGroup::make()
                    ->label('Assistenza')
                    //->icon('heroicon-o-cog-6-tooth')
                    ->collapsed(),
                NavigationGroup::make()
                    ->label('Prodotti')
                    //->icon('heroicon-o-cog-6-tooth')
                    ->collapsed(),

                NavigationGroup::make()
                    ->label('Thunder')
                    //->icon('heroicon-o-cog-6-tooth')
                    ->collapsed()
                ,
                NavigationGroup::make()
                    ->label('Bolt')
                    //->icon('heroicon-o-cog-6-tooth')
                    ->collapsed(),
                NavigationGroup::make()
                    ->label('Impostazioni')
                    //->icon('heroicon-o-cog-6-tooth')
                    ->collapsed(),
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->plugins([
                SpatieLaravelTranslatablePlugin::make()->defaultLocales([config('app.locale')]),
                \TomatoPHP\FilamentNotes\FilamentNotesPlugin::make(),
                FilamentFullCalendarPlugin::make(),
                FilamentUserActivityPlugin::make(),
        //        BoltPlugin::make()
        //->extensions([
        //    \LaraZeus\Thunder\Extensions\Thunder::class,
        //]),
        /*->boltModels([
            'Tipo' => ['Ordinario', 'Straordinario'],
        ])
        ,*/
    //ThunderPlugin::make()

            ]);
    }

    public function boot() : void{
        // You can add any boot logic here if needed
        Filament::serving(static function(){
            NoteResource::navigationSort(19); // Set the navigation sort order for NoteResource
        });

    }
}
