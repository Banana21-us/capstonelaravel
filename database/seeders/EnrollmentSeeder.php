<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class EnrollmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $schools = [
            'Young Achievers International School, Inc.',
            'Claret School of Maluso',
            'San Isidro High School of Balabagan',
            'Our Lady of Peace High School',
            'Mindanao State University-Marawi',
            'Xavier School',
            'Reedley International School',
            'Davao Wisdom Academy Inc.',
            'Samar National School',
            'Tagum City National High School',
            'Holy Cross School of Lagangilang, Inc.',
            'Saint Joseph High School of Flora, Inc.',
            'Santo Rosario School of Pudtol, Inc.',
            'Felix A. Panganiban Academy of the Philippines, Inc.',
            'Jamiatul Philippine Al-Islamia',
            'Dansalan College Foundation',
            'Notre Dame of Jolo College',
            'Cavite National Science High School',
            'Philippine Science High School',
            'Sisters of Mary School',
            'Dona Montserrat Lopez Memorial High School',
            'Santa Rosa Science and Technology High School',
            'Cagayan National High School',
            'Don Bosco High School'
        ];

        $strands = [
            'STEM',
            'ABM',
            'HUMMS'
        ];

        for ($i = 0; $i < 100; $i++) { // Generate 100 enrollments
            $grade_level = rand(7, 12);
            $lifetime = rand(100000000000, 999999999999); // Generate a 12-digit LRN

            DB::table('enrollments')->insert([
                'LRN' => $lifetime,
                'regapproval_date' => '2024-07-15',
                'payment_approval' => '2024-07-15',
                'grade_level' => (string)$grade_level,
                'guardian_name' => fake()->name(),
                'last_attended' => $schools[array_rand($schools)],
                'public_private' => rand(0, 1) ? 'Public' : 'Private',
                'date_register' => '2024-07-01',
                'strand' => ($grade_level >= 11) ? $strands[array_rand($strands)] : null,
                'school_year' => '2024-2025',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
