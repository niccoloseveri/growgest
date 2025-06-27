<?php

namespace App\Filament\Resources\FondosResource\Pages;

use App\Filament\Resources\FondosResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFondos extends EditRecord
{
    protected static string $resource = FondosResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
