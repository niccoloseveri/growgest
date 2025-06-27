<?php

namespace App\Filament\Resources\PianosResource\Pages;

use App\Filament\Resources\PianosResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPianos extends ListRecords
{
    protected static string $resource = PianosResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
