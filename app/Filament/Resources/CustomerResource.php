<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Get;
use App\Models\Customer;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\CustomField;
use App\Models\PipelineStage;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\Tabs;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\Section;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Infolists\Components\Actions\Action;
use App\Filament\Resources\CustomerResource\Pages;
use Filament\Infolists\Components\RepeatableEntry;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\QuoteResource\Pages\CreateQuote;
use App\Filament\Resources\CustomerResource\RelationManagers;
use App\Filament\Resources\CustomerResource\RelationManagers\CoursesRelationManager;
use App\Filament\Resources\CustomerResource\RelationManagers\QuotesRelationManager;
use App\Models\Appointment;
use App\Models\TaskCategory;
use Filament\Forms\FormsComponent;
use Filament\Infolists\Components\Fieldset;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Model;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?int $navigationSort = 2;
    //protected static ?string $navigationGroup="Clienti";
    protected static ?string $modelLabel="Cliente";
    protected static ?string $pluralModelLabel="Clienti";



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Dati Generali')->schema([
                    Forms\Components\Toggle::make('is_azienda')->label('Azienda?')->live()->onColor('success')->default(true)->columnSpanFull()
                    ,
                    /*Forms\Components\Toggle::make('gia_cliente')->label('Già cliente?')->live()->onColor('success')
                    ,
                    */
                    Forms\Components\Select::make('settore_id')->label('Tipologia')->relationship(name:'settore',titleAttribute:'name')->searchable()->preload()->createOptionForm([
                        Forms\Components\TextInput::make('name')->label('Nome')->required(),
                        Forms\Components\Textarea::make('description')->label('Descrizione')->autosize(),
                    ])->columnSpanFull(),
                    Forms\Components\Select::make('employee_id')->label('Segnalatore')->relationship(name:'segnalatore',titleAttribute:'name')
                        //nome e cognome del segnalatore
                    ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->name} {$record->surname}")->searchable()->preload()
                    /*->createOptionForm([
                        Forms\Components\TextInput::make('name')->label('Nome')->required(),
                        Forms\Components\Textarea::make('description')->label('Descrizione')->autosize(),
                    ])*/,

                    Forms\Components\DatePicker::make('prima_fattura')->label('Data prima fattura')
                    ->hidden(fn (Get $get): bool => !$get('gia_cliente')),
                ])->columns(),
                /*Forms\Components\Section::make('Informazioni Dipendente')
                    ->schema([
                        Forms\Components\Select::make('employee_id')->label('Nome')
                            ->options(User::where('role_id', Role::where('name', 'Employee')->first()->id)->orWhere('role_id',Role::where('name','Admin')->first()->id)->pluck('name', 'id'))
                    ])
                    ->hidden(!auth()->user()->isAdmin()),*/

                Forms\Components\Section::make('Anagrafica')->label('Anagrafica')
                ->schema([
                    Forms\Components\TextInput::make('nome_az')->label('Nome Azienda - Ragione Sociale')->columnSpanFull(),
                    Forms\Components\TextInput::make('cf_azienda')->label('Codice Fiscale'),
                    Forms\Components\TextInput::make('piva')->label('Partita IVA'),
                    Forms\Components\TextInput::make('email_az')->label('Email'),
                    Forms\Components\TextInput::make('tel_az')->label('Telefono'),
                    //Forms\Components\TextInput::make('website')->label('Sito Web'),
                    //Forms\Components\TextInput::make('cod_univoco')->label('Codice Univoco'),
                    Forms\Components\Fieldset::make('Indirizzo Azienda')->schema([
                        Forms\Components\TextInput::make('regione_az')->label('Regione'),
                        Forms\Components\TextInput::make('prov_az')->label('Provincia'),
                        Forms\Components\TextInput::make('citta_az')->label('Città'),
                        Forms\Components\TextInput::make('cap_az')->label('CAP'),
                        Forms\Components\TextInput::make('via_az')->label('Via')->columnSpanFull(),
                    ])->columns(),
                    Forms\Components\Fieldset::make('Rappresentante Legale')->schema([]),

                ])
                ->columns()
                ->hidden(fn (Get $get): bool => !$get('is_azienda')),
                Forms\Components\Section::make('Iscrizione al Fondo')->label('Iscrizione al Fondo')
                ->schema([
                    Forms\Components\TextInput::make('mat_inps')->label('Matricola INPS'),
                    Forms\Components\TextInput::make('n_dipendenti')->label('Numero Dipendenti')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(2000)
                        ->default(0),
                    //select stato (aderente o non aderente)
                    Forms\Components\Select::make('aderente')->label('Stato')
                        ->options([
                            1 => 'Aderente',
                            0 => 'Non Aderente',
                            2 => 'Standby',
                        ])
                        ->default(true),
                    Forms\Components\Select::make('fondo_id')->label('Fondo Interprofessionale')->relationship(name:'fondo',titleAttribute:'name')->searchable()->preload()->createOptionForm([
                        Forms\Components\TextInput::make('name')->label('Nome')->required(),
                        Forms\Components\Textarea::make('description')->label('Descrizione')->autosize(),
                    ]),
                    Forms\Components\RichEditor::make('az_note')->label('Note')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                ])
                ->columns()
                ->hidden(fn (Get $get): bool => !$get('is_azienda')),


                Forms\Components\Section::make('Dettagli Referente')->label('Dettagli Referente')
                    ->schema([
                        Forms\Components\TextInput::make('first_name')->label('Nome')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('last_name')->label('Cognome')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')->label('Email')
                            ->email()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone_number')->label('Telefono')
                            ->maxLength(255),
                        Forms\Components\Fieldset::make('Informazioni Residenza')->schema([
                            Forms\Components\TextInput::make('stato_r')->label('Nazione Residenza'),
                            Forms\Components\TextInput::make('prov_r')->label('Provincia Residenza'),
                            Forms\Components\TextInput::make('citta_r')->label('Città Residenza'),
                            Forms\Components\TextInput::make('cap_r')->label('CAP Residenza'),
                            Forms\Components\TextInput::make('via_r')->label('Via Residenza'),
                        ])->columns(),

                        Forms\Components\RichEditor::make('description')->label('Descrizione')
                            ->maxLength(65535)

                            ->columnSpanFull(),
                    ])
                    ->columns(),

                Forms\Components\Section::make('Fatturazione e Spedizione')->label('Fatturazione e Consegna')
                    ->schema([
                        Forms\Components\Fieldset::make('Indirizzo di Fatturazione')->schema([
                            Forms\Components\TextInput::make('stato_f')->label('Nazione'),
                            Forms\Components\TextInput::make('prov_f')->label('Provincia'),
                            Forms\Components\TextInput::make('citta_f')->label('Città'),
                            Forms\Components\TextInput::make('cap_f')->label('CAP'),
                            Forms\Components\TextInput::make('via_f')->label('Via')->columnSpanFull(),
                        ])->columnSpan(1),

                        Forms\Components\Fieldset::make('Indirizzo di Spedizione')->schema([
                            Forms\Components\Checkbox::make('same_as_fatt')->label('Usa indirizzo di Fatturazione')->columnSpanFull()
                            ->live()->default(true),

                            Forms\Components\Group::make([
                                Forms\Components\TextInput::make('stato_c')->label('Nazione'),
                                Forms\Components\TextInput::make('prov_c')->label('Provincia'),
                                Forms\Components\TextInput::make('citta_c')->label('Città'),
                                Forms\Components\TextInput::make('cap_c')->label('CAP'),
                                Forms\Components\TextInput::make('via_c')->label('Via')->columnSpanFull(),
                            ])->columnSpanFull()->columns(2)->hidden(fn (Get $get) => $get('same_as_fatt')),
                            Forms\Components\Textarea::make('note_spedizione')->label('Note')->columnSpanFull(),

                        ])->columnSpan(1),
                        ])->columns(2),

                Forms\Components\Section::make('Dettagli Lead')->label('Dettagli Lead')
                    ->schema([
                        Forms\Components\Select::make('lead_source_id')->label('Fonte Lead')
                            ->relationship('leadSource', 'name'),
                        Forms\Components\Select::make('tags')->label('Tags')
                            ->relationship('tags', 'name')
                            ->multiple(),
                        Forms\Components\Select::make('pipeline_stage_id')->label('Step nella pipeline')
                            ->relationship('pipelineStage', 'name', function ($query) {
                                $query->orderBy('position', 'asc');
                            })
                            ->default(PipelineStage::where('is_default', true)->first()?->id)
                    ])
                    ->columns(3),
                Forms\Components\Section::make('Documenti')->label('Documenti')
                    // This will make the section visible only on the edit page
                    ->visibleOn('edit')
                    ->schema([
                        Forms\Components\Repeater::make('documents')->label('Documenti')
                            ->relationship('documents')
                            ->hiddenLabel()
                            ->reorderable(false)
                            ->addActionLabel('Aggiungi Documento')
                            ->schema([
                                Forms\Components\FileUpload::make('file_path')->label('Percorso file')->disk('s3')->directory('Clienti')->visibility('private')
                                    ->required(),
                                Forms\Components\Textarea::make('comments')->label('Commenti'),
                            ])
                            ->columns()
                    ]),
                Forms\Components\Section::make('Campi Extra')->label('Campi Extra')
                    ->schema([
                        Forms\Components\Repeater::make('fields')->label('Extra')
                            ->hiddenLabel()
                            ->relationship('customFields')
                            ->schema([
                                Forms\Components\Select::make('custom_field_id')
                                    ->label('Tipo Extra')
                                    ->options(CustomField::pluck('name', 'id')->toArray())
                                    // We will disable already selected fields
                                    ->disableOptionWhen(function ($value, $state, Get $get) {
                                        return collect($get('../*.custom_field_id'))
                                            ->reject(fn ($id) => $id === $state)
                                            ->filter()
                                            ->contains($value);
                                    })
                                    ->required()
                                    // Adds search bar to select
                                    ->searchable()
                                    // Live is required to make sure that the options are updated
                                    ->live(),
                                Forms\Components\TextInput::make('value')->label('Valore')
                                    ->required()
                            ])
                            ->defaultItems(0)
                            ->addActionLabel('Aggiungi altro')
                            ->columns(),
                    ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nome_az')
                    ->label('Cliente')
                    //->hidden(fn ($record) : Bool => $record->is_azienda)
                    ->formatStateUsing(function ($record) {
                        //dd($record->is_azienda == 1);
                        $tagsList = view('customer.tagsList', ['tags' => $record->tags])->render();

                        return $record->nome_az . ' ' . $tagsList;
                    })
                    ->html()
                    ->searchable(['nome_az'])
                    ,

                Tables\Columns\TextColumn::make('settore.name')->label('Tipologia')->default('–'),

                Tables\Columns\TextColumn::make('employee.name')->label('Referente')->default('–')
                    ->hidden(!auth()->user()->isAdmin()),
               /* Tables\Columns\TextColumn::make('first_name')
                    ->label('Nome'),
                    */

                Tables\Columns\TextColumn::make('course_count')->label('Corsi')
                    ->counts('courses')
                    ->sortable()

                ,
                Tables\Columns\TextColumn::make('email')->toggleable(isToggledHiddenByDefault:true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone_number')->toggleable(isToggledHiddenByDefault:true)
                    ->searchable()
                    ->label('Telefono'),
                Tables\Columns\TextColumn::make('leadSource.name')->label('Lead da')->toggleable(isToggledHiddenByDefault:true),
                Tables\Columns\TextColumn::make('pipelineStage.name')->label('Step Pipeline'),
                Tables\Columns\TextColumn::make('created_at')->label('Creato')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')->label('Modificato')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')->label('Eliminato')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])->deferLoading()->paginated([10, 25, 50, 100])
            ->filters([
                // Tables\Filters\TrashedFilter::make(),
                Filter::make('gia_cliente')->label('Già cliente')->query(fn (Builder $query): Builder => $query->where('gia_cliente', true))->toggle(),
                SelectFilter::make('settore')->label('Tipologia')->relationship(name:'settore',titleAttribute:'name')->preload()->searchable()->multiple(),
                SelectFilter::make('pipelineStage')->label('Step Pipeline')->relationship(name:'pipelineStage',titleAttribute:'name')->preload()->searchable()->multiple(),

                SelectFilter::make('employee')->label('Referente')->relationship(name:'employee',titleAttribute:'name')->preload()->searchable()->multiple()->hidden(!auth()->user()->isAdmin()),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([

                    Tables\Actions\EditAction::make()->hidden(fn ($record) => $record->trashed()),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\ForceDeleteAction::make(),
                    Tables\Actions\RestoreAction::make(),
                    Tables\Actions\Action::make('Muovi a altro Step')
                        ->hidden(fn ($record) => $record->trashed())
                        ->icon('heroicon-m-pencil-square')
                        ->form([
                            Forms\Components\Select::make('pipeline_stage_id')
                                ->label('Stato')
                                ->options(PipelineStage::pluck('name', 'id')->toArray())
                                ->default(function (Customer $record) {
                                    $currentPosition = $record->pipelineStage->position;
                                    return PipelineStage::where('position', '>', $currentPosition)->first()?->id;
                                }),
                            Forms\Components\Textarea::make('notes')
                                ->label('Note')
                        ])
                        ->action(function (Customer $customer, array $data): void {
                            $customer->pipeline_stage_id = $data['pipeline_stage_id'];
                            $customer->save();

                            $customer->pipelineStageLogs()->create([
                                'pipeline_stage_id' => $data['pipeline_stage_id'],
                                'notes' => $data['notes'],
                                'user_id' => auth()->id()
                            ]);

                            Notification::make()
                                ->title('Pipeline Cliente Aggiornata')
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\Action::make('Add Task')->label('Aggiungi Task')
                        ->icon('heroicon-s-clipboard-document')
                        ->form([
                            Forms\Components\Select::make('taskcategory_id')->label('Categoria')
                                ->preload()
                                ->searchable()

                                ->options(TaskCategory::all()->pluck('name','id')->unique())
                                //->relationship('tasks.taskcategory','name')
                               ,
                            Forms\Components\RichEditor::make('description')->label('Descrizione')
                                ->required(),
                            Forms\Components\Select::make('user_id')->label('Dipendente')
                                ->preload()
                                ->searchable()
                                ->relationship('employee', 'name'),
                            Forms\Components\DatePicker::make('due_date')->label('Scadenza')
                                ->native(false),

                        ])
                        ->action(function (Customer $customer, array $data) {
                            $customer->tasks()->create([
                                'user_id' => $data['user_id'],
                                'taskcategory_id' => $data['taskcategory_id'],
                                'description' => $data['description'],
                                'due_date' => $data['due_date'],
                            ]);

                            Notification::make()
                                ->title('Task creato con successo')
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\Action::make('Crea Ordine')
                        ->icon('heroicon-m-book-open')
                        ->url(function ($record) {
                            return CreateQuote::getUrl(['customer_id' => $record->id]);
                        })
                ])
            ])
            ->recordUrl(function ($record) {
                // If the record is trashed, return null
                if ($record->trashed()) {
                    // Null will disable the row click
                    return null;
                }

                // Otherwise, return the edit page URL
                return Pages\ViewCustomer::getUrl([$record->id]);
            })
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function infoList(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Fieldset::make('Company Information')->label('Informazioni Azienda')
                ->schema([
                    Section::make('Company Information')->heading('Anagrafica Azienda')
                    ->schema([
                        TextEntry::make('nome_az')->label('Nome Azienda - Ragione Sociale')->columnSpanFull(),
                        TextEntry::make('cf_azienda')->label('Codice Fiscale'),
                        TextEntry::make('piva')->label('Partita IVA'),
                        TextEntry::make('email_az')->label('Email'),
                        TextEntry::make('tel_az')->label('Telefono'),
                        TextEntry::make('website')->label('Sito Web'),
                        TextEntry::make('cod_univoco')->label('Cod. Univoco'),
                    ])->columns()
                    ->collapsible(),
                    Section::make('Company Address')->heading('Indirizzo Azienda')
                    ->schema([
                        TextEntry::make('stato_az')->label('Nazione'),
                        TextEntry::make('prov_az')->label('Provincia'),
                        TextEntry::make('citta_az')->label('Città'),
                        TextEntry::make('cap_az')->label('CAP'),
                        TextEntry::make('via_az')->label('Via'),

                    ])->collapsible()->collapsed()->columns(),
                ])->columns()->visible(fn ($record) => $record->nome_az != ''),

                Fieldset::make('Contact Information')->label('Informazioni di Contatto')
                    ->schema([
                        Section::make('Personal Information')->heading('Informazioni Personali')
                        ->schema([
                            TextEntry::make('first_name')->label('Nome'),
                            TextEntry::make('last_name')->label('Cognome'),
                            TextEntry::make('email')->label('Email'),
                            TextEntry::make('phone_number')->label('Telefono'),
                        ])
                        ->columns()
                        ->collapsible()
                        //->collapsed(fn ($record) => $record->nome_az != '')
                        ,

                        Section::make('Contact Address')->heading('Informazioni Residenza')
                        ->schema([
                            TextEntry::make('stato_r')->label('Nazione'),
                            TextEntry::make('prov_r')->label('Provincia'),
                            TextEntry::make('citta_r')->label('Città'),
                            TextEntry::make('cap_r')->label('CAP'),
                            TextEntry::make('via_r')->label('Via'),
                        ])
                        ->columns()
                        ->collapsible()
                        ->collapsed(fn ($record) => $record->nome_az != ''),
                    ])->columns(),

                Section::make('Shipping Address')->heading('Indirizzo di Consegna')
                    ->schema([
                        TextEntry::make('stato_c')->label('Nazione'),
                        TextEntry::make('prov_c')->label('Provincia'),
                        TextEntry::make('citta_c')->label('Città'),
                        TextEntry::make('cap_c')->label('CAP'),
                        TextEntry::make('via_c')->label('Via'),
                        TextEntry::make('note_spedizione')->columnSpanFull()->html(),
                    ])
                    ->columns(),

                Section::make('Additional Details')->heading('Informazioni Aggiuntive')
                    ->schema([
                        TextEntry::make('description')->label('Descrizione')->html(),
                    ]),
                Section::make('Lead and Stage Information')->heading('Informazioni stato Lead')
                    ->schema([
                        TextEntry::make('leadSource.name')->label('Nome Lead'),
                        TextEntry::make('pipelineStage.name')->label('Step'),
                    ])
                    ->columns(),
                Section::make('Additional fields')->heading('Campi Addizionali')
                    ->hidden(fn ($record) => $record->customFields->isEmpty())
                    ->schema(
                        // We are looping within our relationship, then creating a TextEntry for each Custom Field
                        fn ($record) => $record->customFields->map(function ($customField) {
                            return TextEntry::make($customField->customField->name)
                                ->label($customField->customField->name)
                                ->default($customField->value);
                        })->toArray()
                    )
                    ->columns(),
                Section::make('Documents')->heading('Documenti')
                    // This will hide the section if there are no documents
                    ->hidden(fn ($record) => $record->documents->isEmpty())
                    ->schema([
                        RepeatableEntry::make('documents')->label('Documenti')
                            ->hiddenLabel()
                            ->schema([
                                TextEntry::make('file_path')
                                    ->label('Documento')
                                    // This will rename the column to "Download Document" (otherwise, it's just the file name)
                                    ->formatStateUsing(fn () => "Download Documento")
                                    ->url(fn ($record) =>
                                    //Storage::url($record->file_path), true)
                                    Storage::temporaryUrl($record->file_path, now()->addHours(1)), true)
                                    // This will make the link look like a "badge" (blue)
                                    ->badge()
                                    ->color(Color::Blue),
                                TextEntry::make('comments')->label('Commenti'),
                            ])
                            ->columns()
                    ]),

                Section::make('Pipeline Stage History and Notes')->heading('Storia Pipeline e Note')
                    ->schema([
                        ViewEntry::make('pipelineStageLogs')
                            ->label('')
                            ->view('infolists.components.pipeline-stage-history-list')
                    ])
                    ->collapsible(),
                Section::make('Tasks')->schema([
                Tabs::make('Tasks')->label('Tasks')->activeTab(2)
                    ->tabs([
                        Tabs\Tab::make('Completed')->label('Completati')
                            ->badge(fn ($record) => $record->completedTasks->count())
                            ->schema([
                                RepeatableEntry::make('completedTasks')
                                    ->hiddenLabel()
                                    ->schema([
                                        TextEntry::make('description')->label('Descrizione')
                                            ->html()
                                            ->columnSpanFull(),
                                        TextEntry::make('employee.name')->label('Referente')
                                            ->hidden(fn ($state) => is_null($state)),
                                        TextEntry::make('due_date')->label('Data Scadenza')
                                            ->hidden(fn ($state) => is_null($state))
                                            ->date('d/m/Y'),
                                        TextEntry::make('due_time')->label('Ora Scadenza')
                                            ->hidden(fn ($state) => is_null($state))
                                            ->date('H:i'),
                                        //Carbon::createFromFormat('Y-d-m H:i:s',$task->due_date->format('Y-d-m').' '.$task->due_time->format('H:i:s'))
                                    ])
                                    ->columns(3)
                            ]),
                        Tabs\Tab::make('Incomplete')->label('Incompleti')
                            ->badge(fn ($record) => $record->incompleteTasks->count())
                            ->schema([
                                RepeatableEntry::make('incompleteTasks')
                                    ->hiddenLabel()
                                    ->schema([
                                        TextEntry::make('description')->label('Descrizione')
                                            ->html()
                                            ->columnSpanFull(),
                                        TextEntry::make('employee.name')->label('Commerciale')
                                            ->hidden(fn ($state) => is_null($state)),
                                        TextEntry::make('due_date')->label('Data Scadenza')
                                            ->hidden(fn ($state) => is_null($state))
                                            ->date('d/m/Y'),
                                        TextEntry::make('due_time')->label('Ora Scadenza')
                                            ->hidden(fn ($state) => is_null($state))
                                            ->date('H:i'),
                                        TextEntry::make('is_completed')->label('Completo?')
                                            ->formatStateUsing(function ($state) {
                                                return $state ? 'Yes' : 'No';
                                            })
                                            ->suffixAction(
                                                Action::make('complete')
                                                    ->label('Completa')
                                                    ->button()
                                                    ->requiresConfirmation()
                                                    ->modalHeading('Segnala task completato')
                                                    ->modalDescription('Sei sicuro di aver completato il task?')
                                                    ->action(function (Task $record) {
                                                        $record->is_completed = true;
                                                        $record->save();

                                                        Notification::make()
                                                            ->title('Task Completato.')
                                                            ->success()
                                                            ->send();
                                                    })
                                            ),
                                    ])
                                    ->columns(4)
                            ])
                    ])
                    ->columnSpanFull(),
                ]),
                Section::make('Appointments')->heading('Appuntamenti')->schema([
                    Tabs::make('Appointments')->activeTab(2)
                        ->tabs([
                            Tabs\Tab::make('Completed')->label('Completati')
                                ->badge(fn ($record) => $record->completedAppointments->count())
                                ->schema([
                                    RepeatableEntry::make('completedAppointments')
                                    ->hiddenLabel()
                                    ->schema([
                                        TextEntry::make('description')->label('Descrizione')
                                        ->html()
                                        ->columnSpanFull(),
                                    TextEntry::make('employee.name')->label('Referente')
                                        ->hidden(fn ($state) => is_null($state)),
                                    TextEntry::make('due_date')->label('Data Scadenza')
                                        ->hidden(fn ($state) => is_null($state))
                                        ->date('d/m/Y'),
                                    TextEntry::make('due_time')->label('Ora Scadenza')
                                        ->hidden(fn ($state) => is_null($state))
                                        ->date('H:i'),
                                    ])
                                    ->columns(3)
                                ]),
                            Tabs\Tab::make('Incomplete')->label('Incompleti')
                                ->badge(fn ($record) => $record->incompleteAppointments->count())
                                ->schema([
                                    RepeatableEntry::make('incompleteAppointments')
                                    ->hiddenLabel()
                                    ->schema([
                                        TextEntry::make('description')->label('Descrizione')
                                            ->html()
                                            ->columnSpanFull(),
                                        TextEntry::make('employee.name')->label('Commerciale')
                                            ->hidden(fn ($state) => is_null($state)),
                                        TextEntry::make('due_date')->label('Data Scadenza')
                                            ->hidden(fn ($state) => is_null($state))
                                            ->date('d/m/Y'),
                                        TextEntry::make('due_time')->label('Ora Scadenza')
                                            ->hidden(fn ($state) => is_null($state))
                                            ->date('H:i'),
                                        TextEntry::make('is_completed')->label('Completo?')
                                            ->formatStateUsing(function ($state) {
                                                return $state ? 'Yes' : 'No';
                                            })
                                            ->suffixAction(
                                                Action::make('complete')
                                                    ->label('Completa')
                                                    ->button()
                                                    ->requiresConfirmation()
                                                    ->modalHeading('Segnala appuntamento completato')
                                                    ->modalDescription("Sei sicuro di aver completato l'appuntamento?")
                                                    ->action(function (Appointment $record) {
                                                        $record->is_completed = true;
                                                        $record->save();

                                                        Notification::make()
                                                            ->title('Appuntamento Completato.')
                                                            ->success()
                                                            ->send();
                                                    })
                                            ),
                                    ])
                                    ->columns(4)
                                ])
                        ]),


                ]),

            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
            CoursesRelationManager::class,
            QuotesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
            'view' => Pages\ViewCustomer::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {

        if(auth()->user()->isAdmin()){
            return parent::getEloquentQuery()->whereDoesntHave('settore',function (Builder $query){
                $query->where('name','like','fiera');
            })
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
        } else
        return parent::getEloquentQuery()->whereRelation('employee','employee_id', '=', auth()->user()->id )->whereDoesntHave('settore',function (Builder $query){
            $query->where('name','like','fiera');
        })
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);

        /*return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
            */
    }
}
