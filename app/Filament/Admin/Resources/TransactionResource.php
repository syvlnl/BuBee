<?php

namespace App\Filament\Admin\Resources;

use Illuminate\Support\Facades\Auth;
use App\Filament\Admin\Resources\TransactionResource\Pages;
use App\Models\Transaction;
use App\Models\Target;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;
    protected static ?string $navigationIcon = 'heroicon-c-document-currency-dollar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Toggle::make('is_saving')
                    ->label('Add to Savings Target?')
                    ->live()
                    ->dehydrated(true)
                    ->columnSpanFull(),

                Forms\Components\Select::make('target_id')
                    ->label('Savings Target')
                    ->options(Target::where('user_id', Auth::id())->pluck('name', 'target_id'))
                    ->searchable()
                    ->required()
                    ->visible(fn(Get $get): bool => $get('is_saving')),

                Forms\Components\Select::make('category_id')
                    ->relationship('category', 'name', fn(Builder $query) => $query->where('user_id', Auth::id()))
                    ->searchable()
                    ->required()
                    ->visible(fn(Get $get): bool => !$get('is_saving')),

                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                Forms\Components\DatePicker::make('date_transaction')
                    ->required(),

                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->numeric(),

                Forms\Components\Textarea::make('note')
                    ->columnSpanFull()
                    ->visible(fn(Get $get): bool => !$get('is_saving')),

                Forms\Components\FileUpload::make('image')
                    ->image()
                    ->visible(fn(Get $get): bool => !$get('is_saving')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(Transaction::where('user_id', Auth::id())->with(['category', 'target']))
            ->columns([
                TextColumn::make('category_display')
                    ->label('Category')
                    ->sortable()
                    ->searchable(query: function ($query, $search) {
                        $query->whereHas('category', fn($q) => $q->where('name', 'like', "%{$search}%"))
                            ->orWhere(function ($q) use ($search) {
                                $q->where('is_saving', 1)
                                    ->whereRaw("'Saving' LIKE ?", ["%{$search}%"]);
                            });
                    })
                    ->getStateUsing(function ($record) {
                        return $record->is_saving ? ($record->target->name) : ($record->category->name ?? '-');
                    }),

                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->formatStateUsing(function (Transaction $record) {
                        $name = e($record->name);
                        return $name;
                    })
                    ->html()
                    ->wrap()
                    ->sortable(),

                Tables\Columns\IconColumn::make('type')
                    ->label('Type')
                    ->getStateUsing(
                        fn(Transaction $record): string =>
                        $record->is_saving ? 'saving' : ($record->category?->is_expense ? 'expense' : 'income')
                    )
                    ->icon(fn(string $state): string => match ($state) {
                        'saving' => 'heroicon-c-arrow-down-on-square',
                        'expense' => 'heroicon-c-arrow-up-right',
                        'income' => 'heroicon-c-arrow-down-left',
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'saving' => 'info',
                        'expense' => 'danger',
                        'income' => 'success',
                    })
                    ->tooltip(fn(string $state): string => match ($state) {
                        'saving' => 'Saving',
                        'expense' => 'Expense',
                        'income' => 'Income',
                    }),

                Tables\Columns\TextColumn::make('amount')
                    ->numeric()
                    ->money('IDR', locale: 'id')
                    ->sortable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->date()
                    ->label('Updated at')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('date_transaction')
                    ->date()
                    ->sortable()
                    ->label('Created at'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->label('New Transaction'),
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

    public static function afterCreate(Transaction $record): void
    {
        if ($record->is_saving && $record->target_id && $record->amount) {
            $target = $record->target;
            $target->amount_collected += $record->amount;
            $target->save();
        }
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
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(?Model $model = null): Builder
    {
        return parent::getEloquentQuery()->where('user_id', Auth::id());
    }
}
