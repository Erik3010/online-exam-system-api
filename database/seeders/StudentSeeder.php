<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\Classroom;

use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('id_ID');

        for ($i = 0; $i < 50; $i++) {
            $classroom_ids = Classroom::pluck('id');

            Student::create([
                'name' => $faker->name(),
                'classroom_id' => $classroom_ids->random()
            ]);
        }
    }
}
