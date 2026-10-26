<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;
    protected function getHeaderActions(): array { return [DeleteAction::make()]; }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if ($this->record->is(Auth::user()) && $this->record->isAdmin() && ($data['role'] ?? null) !== 'admin') {
            abort_unless(\App\Models\User::query()->where('role', 'admin')->count() > 1, 403, 'Não é possível remover o único administrador do sistema.');
        }

        if ($this->record->is(Auth::user()) && ! $this->record->isAdmin()) {
            abort(403, 'Não é possível alterar o próprio tipo de conta.');
        }

        return $data;
    }
}
