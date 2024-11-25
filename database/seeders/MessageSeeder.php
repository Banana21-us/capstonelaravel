<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MessageSeeder extends Seeder
{
    /**
     * php artisan db:seed --class=MessageSeeder
     */
    public function run(): void
    {
        DB::table('messages')->insert([
            [
                'message_reciever' => 33,
                'message_sender' => 10004,
                'message' => 'hello im luisang?',
                'message_date' => Carbon::now()->format('Y-m-d'),
                'created_at' => '2024-11-22 11:07:25', // Literal timestamp
                'updated_at' => '2024-11-22 11:07:25', // Literal timestamp
            ],
            // [
            //     'message_reciever' => 1,
            //     'message_sender' => 400238150125,
            //     'message' => 'RUSS',
            //     'message_date' => Carbon::now()->format('Y-m-d'),
            //     'created_at' => '2024-11-22 11:07:26',
            //     'updated_at' => '2024-11-22 11:07:26',
            // ],
            // [
            //     'message_reciever' => 1,
            //     'message_sender' => 10005,
            //     'message' => 'RUSS.',
            //     'message_date' => Carbon::now()->format('Y-m-d'),
            //     'created_at' => '2024-11-22 11:07:28',
            //     'updated_at' => '2024-11-22 11:07:28',
            // ],
            // [
            //     'message_reciever' => 2,
            //     'message_sender' => 10001,
            //     'message' => 'WILL',
            //     'message_date' => Carbon::now()->format('Y-m-d'),
            //     'created_at' => '2024-11-22 11:07:30', // Literal timestamp
            //     'updated_at' => '2024-11-22 11:07:30', // Literal timestamp
            // ],
            // [
            //     'message_reciever' => 2,
            //     'message_sender' => 400238150126,
            //     'message' => 'WILL',
            //     'message_date' => Carbon::now()->format('Y-m-d'),
            //     'created_at' => '2024-11-22 11:07:31',
            //     'updated_at' => '2024-11-22 11:07:31',
            // ],
            // [
            //     'message_reciever' => 2,
            //     'message_sender' => 10002,
            //     'message' => 'WILL',
            //     'message_date' => Carbon::now()->format('Y-m-d'),
            //     'created_at' => '2024-11-22 11:07:32',
            //     'updated_at' => '2024-11-22 11:07:32',
            // ],
        ]);
        
    }
}
