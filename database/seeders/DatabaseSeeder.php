<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        \App\Models\User::factory()->create([
            'name'     => 'María Salazar',
            'email'    => 'mariajoseguilarte@gmail.com',
            'password' => bcrypt(123456)
        ]);

        \App\Models\Event::create([
            'title'    => 'Evento de prueba',
            'date'     => now()
        ]);
    }
}
