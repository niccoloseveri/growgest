<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CoursesResource\Pages;
use App\Filament\Resources\CoursesResource\RelationManagers;
use App\Models\Courses;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CoursesResource extends Resource
{
    protected static ?string $model = Courses::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?int $navigationSort = 4;
    protected static ?string $navigationGroup="Clienti";
    protected static ?string $modelLabel="Corso";
    protected static ?string $pluralModelLabel="Corsi";

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                    Forms\Components\TextInput::make('name')->label('Nome Corso')->required()->maxLength(255)->columnSpanFull(),

                    Forms\Components\Select::make('pianos_id')->label('Piano Formativo')
                        ->relationship(name: 'piano', titleAttribute: 'name')
                        ->searchable()
                        ->preload()
                        ->createOptionForm([
                            Forms\Components\TextInput::make('name')->label('Nome Piano Formativo')->required()->maxLength(255),
                            Forms\Components\RichEditor::make('description')->label('Descrizione Piano Formativo')->maxLength(65535)->columnSpanFull(),
                        ])
                        ->required(),
                    Forms\Components\Select::make('strumentos_id')->label('Strumento')
                        ->relationship(name: 'strumento', titleAttribute: 'name')
                        ->searchable()
                        ->preload()
                        ->createOptionForm([
                            Forms\Components\TextInput::make('name')->label('Nome Strumento')->required()->maxLength(255),
                            Forms\Components\RichEditor::make('description')->label('Descrizione Strumento')->maxLength(65535)->columnSpanFull(),
                        ])
                        ->required(),

                    Forms\Components\TextInput::make('partecipanti_previsti')->label('Partecipanti Previsti')->numeric()->required()->maxLength(255),
                    Forms\Components\TextInput::make('ore_totali')->label('Durata (in ore)')->numeric()->required(),
                    DatePicker::make('data_inizio')->label('Data Inizio Corso')->required(),
                    DatePicker::make('data_fine')->label('Data Fine Corso')->required(),
                    DatePicker::make('scadenza')->label('Scadenza Corso')->required(),
                    Forms\Components\Select::make('stato')->label('Stato Corso')
                        ->options([
                            'in_programma' => 'In Programma',
                            'in_corso' => 'In Corso',
                            'concluso' => 'Concluso',
                        ])
                        //->default('in_programma')
                        ->native(false)
                        ->required(),
                    Forms\Components\TextInput::make('valore_corso')->label('Valore Corso (€)')
                        ->numeric()
                        ->required()
                        ->suffix('€')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('budget_lordo_cfe')->label('Budget Lordo CFE (€)')
                        ->numeric()
                        ->required()
                        ->suffix('€')
                        ->maxLength(255),
                    Forms\Components\RichEditor::make('description')->label('Descrizione Corso')->required()->maxLength(65535)->columnSpanFull(),


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome Corso')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('piano.name')
                    ->label('Piano Formativo')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('strumento.name')
                    ->label('Strumento')
                    ->searchable()
                    ->sortable(),


                Tables\Columns\TextColumn::make('partecipanti_previsti')
                    ->label('Partecipanti Previsti')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('stato')
                    ->label('Stato Corso')
                    ->sortable(),
                Tables\Columns\TextColumn::make('data_inizio')
                    ->label('Data Inizio Corso')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('customers_count')
                    ->label('Numero Partecipanti')
                    ->counts('customers')
                    ->sortable(),
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
            'index' => Pages\ListCourses::route('/'),
            'create' => Pages\CreateCourses::route('/create'),
            'edit' => Pages\EditCourses::route('/{record}/edit'),
        ];
    }
}
