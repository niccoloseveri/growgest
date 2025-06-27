<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PianosResource\Pages;
use App\Filament\Resources\PianosResource\RelationManagers;
use App\Models\Pianos;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PianosResource extends Resource
{
    protected static ?string $model = Pianos::class;

    protected static ?string $navigationGroup = 'Impostazioni';
    protected static ?string $modelLabel="Piano";
    protected static ?string $pluralModelLabel="Piani";

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPianos::route('/'),
            'create' => Pages\CreatePianos::route('/create'),
            'edit' => Pages\EditPianos::route('/{record}/edit'),
        ];
    }
}
