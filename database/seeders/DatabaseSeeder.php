<?php

namespace Database\Seeders;

use App\Models\Aluno;
use App\Models\Turma;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(AdminUserSeeder::class);

        $turma = Turma::create([
            'curso' => 'Informática',
            'serie' => '1',
        ]);

        $faker = \Faker\Factory::create('pt_BR');
        $alunos = collect(range(1, 45))
            ->map(fn (): array => [
                'nome' => $faker->name(),
                'matricula' => $faker->unique()->numerify('#######'),
            ])
            ->sortBy('nome', SORT_NATURAL | SORT_FLAG_CASE)
            ->values();

        foreach ($alunos as $index => $aluno) {
            Aluno::create([
                ...$aluno,
                'numero' => $index + 1,
                'turma_id' => $turma->id,
            ]);
        }
    }
}
