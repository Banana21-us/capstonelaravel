<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Student; // Import the Student model

class StudentSeeder extends Seeder
{
    /**
     * php artisan db:seed --class=StudentSeeder
     */
    public function run(): void
    {
        // Array of student data
        $students = [
            [
                'LRN' => 400238150125,
                'fname' => 'Maria',
                'lname' => 'Garcia',
                'mname' => 'Santos',
                'suffix' => '',
                'bdate' => '2000-01-15',
                'bplace' => 'Manila',
                'gender' => 'Female',
                'religion' => 'Catholic',
                'address' => '123 Main St, Manila',
                'contact_no' => '09123456789',
                'email' => 'maria.garcia@gmail.com',
                'password' => bcrypt('password123'), // Use bcrypt for password hashing
            ],
            [
                'LRN' => 400238150126,
                'fname' => 'Juan',
                'lname' => 'Dela Cruz',
                'mname' => 'Reyes',
                'suffix' => '',
                'bdate' => '1999-05-20',
                'bplace' => 'Quezon City',
                'gender' => 'Male',
                'religion' => 'Catholic',
                'address' => '456 Elm St, Quezon City',
                'contact_no' => '09234567890',
                'email' => 'juan.delacruz@yahoo.com',
                'password' => bcrypt('password456'),
            ],
            [
                'LRN' => 400238150127,
                'fname' => 'Ana',
                'lname' => 'Santos',
                'mname' => '',
                'suffix' => '',
                'bdate' => '2001-03-30',
                'bplace' => 'Cebu City',
                'gender' => 'Female',
                'religion' => '',
                'address' => '789 Oak St, Cebu City',
                'contact_no' => '',
                'email' => 'ana.santos@gmail.com',
                'password' => bcrypt('password789'),
            ],
            [
                // Additional student records
                'LRN' => 400238150128,
                'fname' => 'Luis',
                'lname' => 'Alvarez',
                'mname' => '',
                'suffix' => '',
                'bdate' => '2002-07-12',
                'bplace' => 'Davao City',
                'gender' => 'Male',
                'religion' => '',
                'address' => '',
                'contact_no' => '',
                'email' => 'luis.alvarez@gmail.com',
                'password' => bcrypt('password101'),
            ],
            [
                // Another example
                'LRN' => 400238150129,
                'fname' => "Clara",
                "lname" => "Mendoza",
                "mname" => "Isabel",
                "suffix" => "Jr.",
                "bdate" => "2003-11-22",
                "bplace" => "Iloilo City",
                "gender" => "Female",
                "religion" => "Christian",
                "address" => "321 Pine St, Iloilo City",
                "contact_no" => "09345678901",
               "email"  =>'clara.mendoza@example.com', 
               'password' => bcrypt('password120'), // Use bcrypt for password hashing
            ],
            // Add more student records as needed
        ];

        // Insert multiple records into the students table
        foreach ($students as $student) {
            Student::create($student);
        }
    }
}