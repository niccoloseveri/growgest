<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FieraCustomerResource\Pages;
use App\Filament\Resources\FieraCustomerResource\RelationManagers;
use App\Models\Customer;
use App\Models\FieraCustomer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FieraCustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $modelLabel="Cliente Fiera";
    protected static ?string $pluralModelLabel="Clienti Fiera";
    protected static ?string $navigationGroup="Clienti";
    protected static ?int $navigationSort = 15;


    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->whereHas('settore', function (Builder $query){
            $query->where('name', 'like','%fiera%');
        });
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Forms\Components\Section::make()->schema([
                    Forms\Components\Toggle::make('is_azienda')->label('Azienda?')->live()->onColor('success')
                    ,
                    Forms\Components\Toggle::make('gia_cliente')->label('Già cliente?')->live()->onColor('success')
                    ,

                    Forms\Components\Select::make('settore_id')->label('Tipologia')->relationship(name:'settore',titleAttribute:'name')->default(8),

                    Forms\Components\DatePicker::make('prima_fattura')->label('Data prima fattura')
                    ->hidden(fn (Get $get): bool => !$get('gia_cliente')),
                ])->columns(),
                Forms\Components\TextInput::make('nome_az')->label('Nome Azienda - Ragione Sociale')->columnSpanFull()->hidden(fn (Get $get): bool => !$get('is_azienda')),

                Forms\Components\TextInput::make('first_name')->label('Nome')
                    ->maxLength(255),
                Forms\Components\TextInput::make('last_name')->label('Cognome')
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')->label('Email')
                    ->email()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone_number')->label('Telefono')
                    ->maxLength(255),
                Forms\Components\RichEditor::make('description')->label('Descrizione')
                    ->maxLength(65535)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('first_name')
                    ->label('Cliente')
                    //->hidden(fn ($record) : Bool => $record->is_azienda)
                    ->formatStateUsing(function ($record) {
                        //dd($record->is_azienda);
                        $tagsList = view('customer.tagsList', ['tags' => $record->tags])->render();
                        if($record->is_azienda){
                            $record->first_name = '';
                            $record->last_name = $record->nome_az;
                        }
                        return $record->first_name . ' ' . $record->last_name . ' ' . $tagsList;
                    })
                    ->html()
                    ->searchable(['first_name', 'last_name','nome_az'])
                    ,
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone_number')
                    ->searchable()
                    ->label('Telefono'),
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
            'index' => Pages\ListFieraCustomers::route('/'),
            'create' => Pages\CreateFieraCustomer::route('/create'),
            'edit' => Pages\EditFieraCustomer::route('/{record}/edit'),
        ];
    }
}
