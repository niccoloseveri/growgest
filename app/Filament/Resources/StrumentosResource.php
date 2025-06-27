<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StrumentosResource\Pages;
use App\Filament\Resources\StrumentosResource\RelationManagers;
use App\Models\Strumentos;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StrumentosResource extends Resource
{
    protected static ?string $model = Strumentos::class;

    protected static ?string $navigationGroup = 'Impostazioni';
    protected static ?string $modelLabel="Strumento";
    protected static ?string $pluralModelLabel="Strumenti";

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
            'index' => Pages\ListStrumentos::route('/'),
            'create' => Pages\CreateStrumentos::route('/create'),
            'edit' => Pages\EditStrumentos::route('/{record}/edit'),
        ];
    }
}
