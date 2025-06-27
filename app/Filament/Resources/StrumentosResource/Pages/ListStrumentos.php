<?php

namespace App\Filament\Resources\StrumentosResource\Pages;

use App\Filament\Resources\StrumentosResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStrumentos extends ListRecords
{
    protected static string $resource = StrumentosResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
