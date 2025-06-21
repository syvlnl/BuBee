<?php

namespace App\Filament\Admin\Resources;

use Illuminate\Support\Facades\Auth;
use App\Filament\Admin\Resources\TargetResource\Pages;
use App\Filament\Admin\Resources\TargetResource\RelationManagers;
use App\Models\Target;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;


class TargetResource extends Resource
{
    protected static ?string $model = Target::class;

    protected static ?string $navigationIcon = 'heroicon-c-rocket-launch';

    protected static ?string $recordKey = 'target_id';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('amount_needed')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->rules(['numeric', 'min:1'])
                    ->prefix('Rp')
                    ->label('Amount Needed'),
                Forms\Components\TextInput::make('amount_collected')
                    ->disabled()
                    ->prefix('Rp')
                    ->label('Amount Collected')
                    ->dehydrated(false) 
                    ->default(fn(?Target $record) => $record?->amount_collected ?? 0),
                Forms\Components\DatePicker::make('deadline')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            Tables\Columns\TextColumn::make('name')
                ->searchable(),
            Tables\Columns\TextColumn::make('amount_needed')
                ->numeric()
                ->money('IDR', locale: 'id')
                ->sortable(),
            Tables\Columns\TextColumn::make('amount_collected')
                ->state(fn(Target $record) => $record->amount_collected)
                ->numeric()
                ->money('IDR', locale: 'id')
                ->sortable(),
            Tables\Columns\TextColumn::make('deadline')
                ->date()
                ->sortable(),
            Tables\Columns\TextColumn::make('progress')
                ->state(fn(Target $record) => $record->status === 'completed' ? '100%' : (min(round(($record->amount_collected / max($record->amount_needed, 1)) * 100, 2), 100) . '%'))
                ->label('Progress')
                ->sortable(),
            Tables\Columns\TextColumn::make('status')
                ->state(fn(Target $record) => ($record->amount_collected >= $record->amount_needed) ? 'Completed' : 'On Progress')
                ->badge()
                ->label('Status')
                ->color(fn (string $state): string => match ($state) {
                        'On Progress' => 'warning',
                        'Completed' => 'success',
                        default => 'gray',
                    }),
                    
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->headerActions([
                Tables\Actions\Action::make('New Target')
                    ->modal()
                    ->form([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('amount_needed')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->rules(['numeric', 'min:1'])
                            ->prefix('Rp')
                            ->label('Amount Needed'),
                        Forms\Components\DatePicker::make('deadline')
                            ->required(),
                    ])
                    ->action(fn(array $data) => Target::create([
                        ...$data,
                        'user_id' => Auth::id(),
                    ]))
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->url(fn (Target $record): string => static::getUrl('edit', ['record' => $record])),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListTargets::route('/'),
            'create' => Pages\CreateTarget::route('/create'),
            'edit' => Pages\EditTarget::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(?Model $model = null): Builder
    {
        return parent::getEloquentQuery()->where(['user_id' => Auth::id()]);
    }
}
