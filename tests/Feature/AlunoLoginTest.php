<?php

namespace Tests\Feature;

use App\Filament\Auth\Pages\Login;
use App\Models\Aluno;
use App\Models\Turma;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AlunoLoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    private function aluno(): Aluno
    {
        $turma = Turma::create(['curso' => 'Informática', 'serie' => '1']);

        return Aluno::create([
            'nome' => 'Aluno Teste', 'matricula' => '1234567',
            'cpf' => '123.456.789-01', 'data_nascimento' => '2008-05-10',
            'turma_id' => $turma->id,
        ]);
    }

    private function login(string $nascimento = '2008-05-10')
    {
        return Livewire::test(Login::class)->fillForm([
            'tipo' => 'aluno', 'cpf' => '123.456.789-01',
            'data_nascimento' => $nascimento,
        ])->call('authenticate');
    }

    public function test_aluno_entra_sem_cadastro_e_reutiliza_a_conta(): void
    {
        $aluno = $this->aluno();
        $this->assertSame('12345678901', $aluno->cpf);
        $this->login()->assertHasNoFormErrors();
        $this->assertAuthenticated();
        $this->assertSame($aluno->id, auth()->user()->aluno_id);
        $this->assertTrue(auth()->user()->isAluno());
        auth()->logout();
        $this->login()->assertHasNoFormErrors();
        $this->assertSame(1, User::where('aluno_id', $aluno->id)->count());
    }

    public function test_data_incorreta_nao_cria_conta(): void
    {
        $this->aluno();
        $this->login('2008-05-11')->assertHasFormErrors(['cpf']);
        $this->assertGuest();
        $this->assertDatabaseCount('users', 0);
    }

    public function test_cpf_desconhecido_nao_cria_conta(): void
    {
        $this->login()->assertHasFormErrors(['cpf']);
        $this->assertGuest();
        $this->assertDatabaseCount('users', 0);
    }

    public function test_login_de_aluno_nao_autentica_conta_privilegiada(): void
    {
        $aluno = $this->aluno();
        User::factory()->create(['role' => 'admin', 'aluno_id' => $aluno->id]);
        $this->login()->assertHasFormErrors(['cpf']);
        $this->assertGuest();
    }

    public function test_equipe_continua_entrando_com_email_e_senha(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        Livewire::test(Login::class)->fillForm([
            'tipo' => 'equipe', 'email' => $user->email, 'password' => 'password',
        ])->call('authenticate')->assertHasNoFormErrors();
        $this->assertAuthenticatedAs($user);
    }
}
