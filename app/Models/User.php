<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Professor;
use App\Models\Aluno;
use App\Models\Turma;

#[Fillable(['name', 'email', 'password', 'role', 'professor_id', 'aluno_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return match ($this->role) {
            'admin', 'professor' => $this->role !== 'professor' || $this->professor_id !== null,
            'professor_dt' => $this->professor_id !== null && $this->turmasComoDiretor()->exists(),
            'aluno' => $this->aluno_id !== null,
            default => false,
        };
    }

    public function isAdmin(): bool { return $this->role === 'admin'; }
    public function isProfessor(): bool { return $this->role === 'professor'; }
    public function isProfessorDt(): bool { return $this->role === 'professor_dt'; }
    public function isAluno(): bool { return $this->role === 'aluno'; }

    public function professor(): BelongsTo { return $this->belongsTo(Professor::class); }
    public function aluno(): BelongsTo { return $this->belongsTo(Aluno::class); }
    public function turmasComoDiretor()
    {
        return Turma::query()->where('diretor_turma_id', $this->professor_id);
    }
}
