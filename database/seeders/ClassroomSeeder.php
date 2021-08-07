<?php

namespace Database\Seeders;

use App\Models\Classroom;
use Illuminate\Database\Seeder;

class ClassroomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $classrooms = [
            "10A",
            "10B",
            "10C",
            "10D",
            "10E",
            "10F",
            "10G",
            "11A",
            "11B",
            "11C",
            "11D",
            "12A",
            "12B",
        ];

        foreach ($classrooms as $classroom) {
            Classroom::updateOrCreate(['name' => $classroom]);
        }
    }
}
