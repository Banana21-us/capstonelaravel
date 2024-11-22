<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ParentGuardian;
class ParentGuardianSeeder extends Seeder
{
    /**
     * php artisan db:seed --class=ParentGuardianSeeder
     */
    public function run(): void
    {
        $parentGuardians = [
            [   
                'guardian_id' => 10001,
                'LRN' => 400238150125,
                'fname' => 'Carlos',
                'lname' => 'Garcia',
                'mname' => 'Santos',
                'address' => '123 Main St, Manila',
                'relationship' => 'Father',
                'contact_no' => '09123456789',
                'email' => 'carlos.garcia@example.com',
                'password' => bcrypt('password123'), // Use bcrypt for password hashing
            ],
            [
                'guardian_id' => 10002,
                'LRN' => 400238150126,
                'fname' => 'Maria',
                'lname' => 'Dela Cruz',
                'mname' => '',
                'address' => '456 Elm St, Quezon City',
                'relationship' => 'Mother',
                'contact_no' => '09234567890',
                'email' => 'maria.delacruz@example.com',
                'password' => bcrypt('password456'),
            ],
            [
                'guardian_id' => 10003,
                'LRN' => 400238150127,
                'fname' => 'Anita',
                'lname' => 'Santos',
                'mname' => '',
                'address' => '789 Oak St, Cebu City',
                'relationship' => 'Aunt',
                'contact_no' => '09234567891',
                'email' => 'ana.santos@example.com',
                'password' => bcrypt('password789'),
            ],
            [
                'guardian_id' => 10004,
                'LRN' => 400238150128,
                'fname' => 'Luisang',
                'lname' => 'Alvarez',
                'mname' => '',
                'address' => '789 Oak St, Cebu City',
                'relationship' => 'Aunt',
                'contact_no' => '09234567845',
                'email' => 'Luisang.Alvarez@example.com',
                'password' => bcrypt('password789'),
            ],
            [
                // Another example
                'guardian_id' => 10005,
                'LRN' => 400238150129,
                'fname' => "Clarica",
                "lname" => "Mendoza",
                "mname" => "Isabel",
                'address' => '789 Oak St, Cebu City',
                'relationship' => 'Aunt',
                'contact_no' => '09234567841',
                'email' => 'Clarica.Mendoza@example.com',
                'password' => bcrypt('password789'), // Use bcrypt for password hashing
            ],
            // Add more parent guardian records as needed
        ];

        // Insert multiple records into the parent_guardians table
        foreach ($parentGuardians as $guardian) {
            ParentGuardian::create($guardian);
        }
    
    }
}
