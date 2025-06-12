<?php

namespace App\Filament\Admin\Resources;

use Illuminate\Support\Facades\Auth;
use App\Filament\Admin\Resources\TransactionResource\Pages;
use App\Filament\Admin\Resources\TransactionResource\RelationManagers;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-c-document-currency-dollar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('category_id')
                    ->relationship('category', 'name', function (Builder $query) {
                        return $query->where(['user_id' => Auth::id()]);
                    })
                ->searchable()
                ->required(),
                Forms\Components\DatePicker::make('date_transaction')
                    ->required(),
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->numeric(),
                Forms\Components\Textarea::make('note')
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('image')
                    ->image(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(Transaction::where('user_id', Auth::id())->with('category'))
            ->columns([
                Tables\Columns\ImageColumn::make('category.image'),
                Tables\Columns\TextColumn::make('category.name')
                    ->searchable()
                    ->description(fn (Transaction $record): string => $record->name)
                    ->label('Transaction'),
                Tables\Columns\IconColumn::make('category.is_expense')
                    ->boolean()
                    ->sortable()
                    ->trueIcon('heroicon-c-arrow-up-right')
                    ->falseIcon('heroicon-c-arrow-down-left')
                    ->trueColor('danger')
                    ->falseColor('success')
                    ->label('Type'),
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
                Tables\Actions\Action::make('New Transaction')
                    ->modal()
                    ->form([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('category_id')
                            ->relationship('category', 'name', function (Builder $query) {return $query->where(['user_id' => Auth::id()]);})
                            ->searchable()
                            ->required(),
                        Forms\Components\DatePicker::make('date_transaction')
                            ->required(),
                        Forms\Components\TextInput::make('amount')
                            ->required()
                            ->numeric(),
                        Forms\Components\Textarea::make('note')
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('image')
                            ->image(),
            ])
                    ->action(fn(array $data) => Transaction::create([...$data, 'user_id' => Auth::id()])),
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
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(?Model $model = null): Builder
    {
        return parent::getEloquentQuery()->where(['user_id' => Auth::id()]);
    }
}
