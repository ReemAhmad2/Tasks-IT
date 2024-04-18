<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(40)->create();

         \App\Models\Subject::factory(40)->create();


        // \App\Models\User::factory()->create([
        //     'name' => 'Admin',
        //     'email' => 'admin@gmail.com',
        //     'uuid' => Str::uuid(),
        //     'password' => Hash::make('admin1110'),
        //     'type' => 'admin',
        // ]);


        // for($i=1 ; $i<=5 ; $i++)
        // {
        //     for($j=1 ; $j<=8 ; $j++)
        //     {
        //         \App\Models\Category::create([
        //                 'uuid' => Str::uuid(),
        //                 'year' => $i,
        //                 'number' => $j,
        //             ]);
        //     }
        // }
    }
}
