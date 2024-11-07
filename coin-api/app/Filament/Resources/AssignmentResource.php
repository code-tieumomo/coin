<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AssignmentResource\Pages;
use App\Filament\Resources\AssignmentResource\RelationManagers;
use App\Models\Assignment;
use App\Models\Subnet;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Validation\Rule;

class AssignmentResource extends Resource
{
    protected static ?string $model = Assignment::class;

    protected static ?string $navigationIcon = 'heroicon-o-bookmark-square';

    public static function form(Form $form): Form
    {
        $userSuggestions = User::select(['id', 'name'])->get()
            ->mapWithKeys(function (User $user) {
                $displayedText = '[' . str_pad($user->id, 4, '0', STR_PAD_LEFT) . '] ' . $user->name;
                return [$user->id => $displayedText];
            })->toArray();

        return $form
            ->columns(2)
            ->schema([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                MarkdownEditor::make('description')
                    ->hint('Markdown')
                    ->columnSpanFull(),
                DateTimePicker::make('start_date')
                    ->required(),
                DateTimePicker::make('end_date'),
                Repeater::make('subnets')
                    ->columns(2)
                    ->schema([
                        Select::make('subnet')
                            ->options(
                                Subnet::select('name', 'id')
                                    ->get()
                                    ->pluck('name', 'id')
                                    ->toArray()
                            )
                            ->searchable()
                            ->required()
                            ->distinct()
                            ->columnSpanFull(),
                        TextInput::make('weight')
                            ->type('number')
                            ->default(1)
                            ->required()
                            ->hint('To order and calculate final grades'),
                        TextInput::make('needed')
                            ->type('number')
                            ->inputMode('decimal')
                            ->default(1)
                            ->required()
                            ->hint('Needed points to pass'),
                    ])
                    ->minItems(1)
                    ->required()
                    ->columnSpanFull(),
                Toggle::make('is_public')
                    ->label('Assign to all users')
                    ->onIcon('heroicon-m-lock-open')
                    ->offIcon('heroicon-m-lock-closed')
                    ->default(true)
                    ->live()
                    ->columnSpanFull(),
                Select::make('users')
                    ->label('Users')
                    ->multiple()
                    ->hint('Select users to assign the assignment to.')
                    ->options($userSuggestions)
                    ->hidden(fn(Get $get): bool => $get('is_public'))
                    ->columnSpanFull()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\ViewColumn::make('is_public')
                    ->label('Visibility')
                    ->view('filament.pages.assignments.visibility'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListAssignments::route('/'),
            'create' => Pages\CreateAssignment::route('/create'),
            'edit' => Pages\EditAssignment::route('/{record}/edit'),
        ];
    }
}
