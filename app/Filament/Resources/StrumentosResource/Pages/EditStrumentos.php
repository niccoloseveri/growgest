<?php

namespace App\Filament\Resources\StrumentosResource\Pages;

use App\Filament\Resources\StrumentosResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStrumentos extends EditRecord
{
    protected static string $resource = StrumentosResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
