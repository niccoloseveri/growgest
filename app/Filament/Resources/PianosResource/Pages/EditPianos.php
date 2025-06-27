<?php

namespace App\Filament\Resources\PianosResource\Pages;

use App\Filament\Resources\PianosResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPianos extends EditRecord
{
    protected static string $resource = PianosResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
