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
                'message_reciever' => 1,
                'message_sender' => 10002,
                'message' => 'Hello! How are you?',
                'message_date' => Carbon::now()->format('Y-m-d'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'message_reciever' => 1,
                'message_sender' => 400238150125 ,
                'message' => 'Im good, thank you! How about you?',
                'message_date' => Carbon::now()->format('Y-m-d'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'message_reciever' => 1,
                'message_sender' => 10004,
                'message' => 'Meeting at 3 PM. Dont forget.',
                'message_date' => Carbon::now()->subDay()->format('Y-m-d'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
