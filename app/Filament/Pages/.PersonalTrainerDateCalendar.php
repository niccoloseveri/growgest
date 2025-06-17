<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class PersonalTrainerDateCalendar extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
 // Our Custom heading to be displayed on the page
 protected ?string $heading = 'Calendario PT';
 // Custom Navigation Link name
 protected static ?string $navigationGroup="Personal Trainer";

 protected static ?string $navigationLabel = 'Calendario PT';
 protected static ?string $title= 'Calendario PT';
 protected static ?int $navigationSort = 8;

    protected static string $view = 'filament.pages.personal-trainer-date-calendar';
}
