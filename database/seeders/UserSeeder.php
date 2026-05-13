<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create();

        // Admin
        $admin = \App\Models\User::create([
            'name' => 'Admin User',
            'email' => 'admin@careerpredict.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'admin',
        ]);

        \App\Models\Admin::create([
            'user_id' => $admin->id,
            'department' => 'IT Management',
            'level' => 'superadmin',
        ]);

        \App\Models\Profile::create([
            'user_id' => $admin->id,
            'bio' => 'System administrator for CareerPredict.',
        ]);

        // Regular Users
        for ($i = 0; $i < 10; $i++) {
            $user = \App\Models\User::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role' => 'user',
            ]);

            \App\Models\Profile::create([
                'user_id' => $user->id,
                'phone' => $faker->phoneNumber,
                'address' => $faker->address,
                'bio' => $faker->sentence,
                'education' => [['school' => $faker->company . ' University', 'degree' => 'Bachelor of ' . $faker->jobTitle, 'year' => '2020']],
                'experience' => [['company' => $faker->company, 'position' => $faker->jobTitle, 'duration' => '2 years']],
            ]);

            // Random skills for some users
            $skills = \App\Models\Skill::inRandomOrder()->limit(3)->get();
            foreach ($skills as $skill) {
                $user->skills()->attach($skill->id, ['level' => rand(1, 5)]);
            }

            // Random interests
            $interests = \App\Models\Interest::inRandomOrder()->limit(2)->get();
            foreach ($interests as $interest) {
                $user->interests()->attach($interest->id);
            }
        }
    }
}
