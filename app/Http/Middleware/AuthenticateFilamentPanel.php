<?php

namespace App\Http\Middleware;

use Filament\Facades\Filament;
use Filament\Http\Middleware\Authenticate;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Auth\AuthenticationException;

class AuthenticateFilamentPanel extends Authenticate
{
    protected function authenticate($request, array $guards): void
    {
        $guard = Filament::auth();

        if (! $guard->check()) {
            $this->unauthenticated($request, $guards);
            return;
        }

        $user = $guard->user();
        $panel = Filament::getCurrentOrDefaultPanel();
        $isUnauthorized = $user instanceof FilamentUser
            ? (! $user->canAccessPanel($panel))
            : (config('app.env') !== 'local');

        if ($isUnauthorized) {
            $guard->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw new AuthenticationException('Unauthenticated.', $guards, Filament::getLoginUrl());
        }

        $this->auth->shouldUse(Filament::getAuthGuard());
    }
}
