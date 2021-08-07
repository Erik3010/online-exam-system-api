<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $students = Student::all();
        foreach ($students as $student) {
            User::create([
                'username' => $student->name,
                'password' => bcrypt($student->name),
                'student_id' => $student->id,
            ]);
        }

        $teachers = Teacher::all();
        foreach ($teachers as $teacher) {
            User::create([
                'username' => $teacher->name,
                'password' => bcrypt($teacher->name),
                'teacher_id' => $teacher->id
            ]);
        }
    }
}
