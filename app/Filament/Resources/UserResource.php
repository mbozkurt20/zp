<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    public static function getNavigationLabel(): string
    {
        return 'Kullanıcılar';
    }
    public static function getPluralModelLabel(): string
    {
        return 'Kullanıcılar';
    }
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->label('İsim Soyisim')->required(),
                TextInput::make('phone')->label('Telefon')->required(),
                TextInput::make('email')->label('Email')->required(),
                TextInput::make('password')->label('Şifre')->required(),
                Textarea::make('address')->label('Adres')->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
               Tables\Columns\TextColumn::make('name')->label('İsim Soyisim')->sortable()->searchable(),
                TABLEs\Columns\TextColumn::make('phone')->label('Telefon')->sortable()->searchable(),
                TABLEs\Columns\TextColumn::make('email')->label('E-posta')->sortable()->searchable(),
                TABLEs\Columns\TextColumn::make('phone')->label('Telefon')->sortable()->searchable(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
