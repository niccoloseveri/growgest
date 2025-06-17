<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class TechnicalAssistenceCalendar extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    // Our Custom heading to be displayed on the page
    protected ?string $heading = 'Calendario Assistenza';
    // Custom Navigation Link name
    protected static ?string $navigationLabel = 'Calendario Assistenza';
    protected static ?string $title= 'Calendario Assistenza';
    protected static ?int $navigationSort = 10;
    protected static ?string $navigationGroup="Assistenza";

    protected static string $view = 'filament.pages.technical-assistence-calendar';
}
