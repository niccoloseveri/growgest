<?php

namespace App\Filament\Resources\FondosResource\Pages;

use App\Filament\Resources\FondosResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFondos extends ListRecords
{
    protected static string $resource = FondosResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
