<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FondosResource\Pages;
use App\Filament\Resources\FondosResource\RelationManagers;
use App\Models\Fondos;
use Filament\Forms;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FondosResource extends Resource
{
    protected static ?string $model = Fondos::class;

    protected static ?string $navigationGroup="Impostazioni";
    protected static ?string $modelLabel="Fondo";
    protected static ?string $pluralModelLabel="Fondi";

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Nome')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                RichEditor::make('description')
                    ->label('Descrizione')
                    ->maxLength(65535)
                    ->columnSpanFull(),
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Descrizione')
                    ->searchable()
                    ->limit(50)
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
            'index' => Pages\ListFondos::route('/'),
            'create' => Pages\CreateFondos::route('/create'),
            'edit' => Pages\EditFondos::route('/{record}/edit'),
        ];
    }
}
