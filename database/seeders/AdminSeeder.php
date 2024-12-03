<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * 
     * php artisan db:seed --class=AdminSeeder
     */
    public function run(): void
    {
        DB::table('admins')->insert([
            [
                'fname' => 'Jane',
                'lname' => 'De Vera',
                'mname' => 'Fernandez',
                'role' => 'DSF',
                'address' => 'Pangasinan',
                'admin_pic' => null,
                'email' => 'janes.dv@gmail.com',
                'password' => Hash::make('password123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // [
            //     'fname' => 'Russ',
            //     'lname' => 'Del Castillo',
            //     'mname' => 'B',
            //     'role' => 'Principal',
            //     'address' => 'Pangasinan',
            //     'admin_pic' => null,
            //     'email' => 'Russ.dc@gmail.com',
            //     'password' => Hash::make('password123'),
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
            // [
            //     'fname' => 'Wilfredo',
            //     'lname' => 'Canilang',
            //     'mname' => 'C',
            //     'role' => 'Teacher',
            //     'address' => 'Artacho, Sison, Pangasinan',
            //     'admin_pic' => null,
            //     'email' => 'Wil.canilang@gmail.com',
            //     'password' => Hash::make('password123'),
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
            // [
            //     'fname' => 'Marjorie',
            //     'lname' => 'Arriesgado',
            //     'mname' => 'C',
            //     'role' => 'Teacher',
            //     'address' => 'Artacho, Sison, Pangasinan',
            //     'admin_pic' => null,
            //     'email' => 'Mar.Arriesgado@gmail.com',
            //     'password' => Hash::make('password123'),
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
            // [
            //     'fname' => 'Dorvin',
            //     'lname' => 'Camba',
            //     'mname' => null,
            //     'role' => 'Teacher',
            //     'address' => 'Artacho, Sison, Pangasinan',
            //     'admin_pic' => null,
            //     'email' => 'Dorvin.Camba@gmail.com',
            //     'password' => Hash::make('password123'),
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
            // [
            //     'fname' => 'Kezeah',
            //     'lname' => 'Agdeppa',
            //     'mname' => null,
            //     'role' => 'Teacher',
            //     'address' => 'Sison, Pangasinan',
            //     'admin_pic' => null,
            //     'email' => 'Kezeah.Agdeppa@gmail.com',
            //     'password' => Hash::make('password123'),
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
            // [
            //     'fname' => 'Reynard',
            //     'lname' => 'Cargando',
            //     'mname' => 'A',
            //     'role' => 'Teacher',
            //     'address' => 'Sison, Pangasinan',
            //     'admin_pic' => null,
            //     'email' => 'Reynard.Cargando@gmail.com',
            //     'password' => Hash::make('password123'),
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
            // [
            //     'fname' => 'Tindale',
            //     'lname' => 'Abalos',
            //     'mname' => null,
            //     'role' => 'Teacher',
            //     'address' => 'Sison, Pangasinan',
            //     'admin_pic' => null,
            //     'email' => 'Abalos.Tindale@gmail.com',
            //     'password' => Hash::make('password123'),
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
            // [
            //     'fname' => 'Selma',
            //     'lname' => 'Mendijar',
            //     'mname' => 'B',
            //     'role' => 'Teacher',
            //     'address' => 'Sison, Pangasinan',
            //     'admin_pic' => null,
            //     'email' => 'Selma.Mendijar@gmail.com',
            //     'password' => Hash::make('password123'),
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
            // [
            //     'fname' => 'Ma. Roselyn',
            //     'lname' => 'Valles',
            //     'mname' => null,
            //     'role' => 'Teacher',
            //     'address' => 'Caurinan, Sison, Pangasinan',
            //     'admin_pic' => null,
            //     'email' => 'Roselyn.Valles@gmail.com',
            //     'password' => Hash::make('password123'),
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
            // [
            //     'fname' => 'Chino Luis',
            //     'lname' => 'Del Castillo',
            //     'mname' => null,
            //     'role' => 'Teacher',
            //     'address' => 'Pangasinan',
            //     'admin_pic' => null,
            //     'email' => 'Chino.delCastillo@gmail.com',
            //     'password' => Hash::make('password123'),
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
            // [
            //     'fname' => 'Raenel',
            //     'lname' => 'Manangan',
            //     'mname' => null,
            //     'role' => 'Teacher',
            //     'address' => 'Sagunto, Sison, Pangasinan',
            //     'admin_pic' => null,
            //     'email' => 'Raenel.Manangan@gmail.com',
            //     'password' => Hash::make('password123'),
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
            // [
            //     'fname' => 'Samantha',
            //     'lname' => 'Lucagan',
            //     'mname' => null,
            //     'role' => 'Teacher',
            //     'address' => 'Camp 1 La Union',
            //     'admin_pic' => null,
            //     'email' => 'Samantha.Lucagan@gmail.com',
            //     'password' => Hash::make('password123'),
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
            // [
            //     'fname' => 'Melina Anne',
            //     'lname' => 'Manlongat',
            //     'mname' => 'V',
            //     'role' => 'Teacher',
            //     'address' => 'Nlac Artacho Sison Pangasinan',
            //     'admin_pic' => null,
            //     'email' => 'Melina.Manlongat@gmail.com',
            //     'password' => Hash::make('password123'),
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
            // [
            //     'fname' => 'Alayla',
            //     'lname' => 'Del Castillo',
            //     'mname' => null,
            //     'role' => 'Teacher',
            //     'address' => 'Artacho Sison Pangasinan',
            //     'admin_pic' => null,
            //     'email' => 'Alayla.delCastillo@gmail.com',
            //     'password' => Hash::make('password123'),
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
            // [
            //     'fname' => 'Catherine',
            //     'lname' => 'Daza',
            //     'mname' => null,
            //     'role' => 'Teacher',
            //     'address' => 'Artacho Sison Pangasinan',
            //     'admin_pic' => null,
            //     'email' => 'Catherine.Daza@gmail.com',
            //     'password' => Hash::make('password123'),
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
            // [
            //     'fname' => 'John Lester',
            //     'lname' => 'Diamsay',
            //     'mname' => 'R',
            //     'role' => 'Teacher',
            //     'address' => 'Nlac Artacho Sison Pangasinan',
            //     'admin_pic' => null,
            //     'email' => 'jl.Diamsay@gmail.com',
            //     'password' => Hash::make('password123'),
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
            // [
            //     'fname' => 'Mark Alexi',
            //     'lname' => 'Manlongat',
            //     'mname' => null,
            //     'role' => 'Teacher',
            //     'address' => 'Nlac Artacho Sison Pangasinan',
            //     'admin_pic' => null,
            //     'email' => 'ma.Manlongat@gmail.com',
            //     'password' => Hash::make('password123'),
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
            // [
            //     'fname' => 'Angelica',
            //     'lname' => 'Verania',
            //     'mname' => 'C',
            //     'role' => 'Teacher',
            //     'address' => 'Nlac Artacho Sison Pangasinan',
            //     'admin_pic' => null,
            //     'email' => 'Angelica.Verania@gmail.com',
            //     'password' => Hash::make('password123'),
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
            // [
            //     'fname' => 'Jocelyn',
            //     'lname' => 'Mendoza',
            //     'mname' => 'P',
            //     'role' => 'Teacher',
            //     'address' => 'Artacho Sison Pangasinan',
            //     'admin_pic' => null,
            //     'email' => 'Jocelyn.Mendoza@gmail.com',
            //     'password' => Hash::make('password123'),
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
            // [
            //     'fname' => 'Jacquiline',
            //     'lname' => 'Mendoza',
            //     'mname' => null,
            //     'role' => 'Teacher',
            //     'address' => 'Artacho Sison Pangasinan',
            //     'admin_pic' => null,
            //     'email' => 'Jacquiline.Mendoza@gmail.com',
            //     'password' => Hash::make('password123'),
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
            // [
            //     'fname' => 'Jo',
            //     'lname' => 'Bagagnan',
            //     'mname' => 'M',
            //     'role' => 'Teacher',
            //     'address' => 'Bicol',
            //     'admin_pic' => null,
            //     'email' => 'Jo.Bagagnan@gmail.com',
            //     'password' => Hash::make('password123'),
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
            // [
            //     'fname' => 'May Nicole',
            //     'lname' => 'De leon',
            //     'mname' => null,
            //     'role' => 'Teacher',
            //     'address' => 'Aguilar',
            //     'admin_pic' => null,
            //     'email' => 'mc.dl@gmail.com',
            //     'password' => Hash::make('password123'),
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
            // [
            //     'fname' => 'Rowena',
            //     'lname' => 'Borromeo',
            //     'mname' => null,
            //     'role' => 'Teacher',
            //     'address' => 'Aguilar',
            //     'admin_pic' => null,
            //     'email' => 'Rowena.Borromeo@gmail.com',
            //     'password' => Hash::make('password123'),
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
            // [
            //     'fname' => 'Henzelle',
            //     'lname' => 'Palaruan',
            //     'mname' => null,
            //     'role' => 'Teacher',
            //     'address' => 'Pangasinan',
            //     'admin_pic' => null,
            //     'email' => 'Palaruan.Henzelle@gmail.com',
            //     'password' => Hash::make('password123'),
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
            // [
            //     'fname' => 'Gladys Joy',
            //     'lname' => 'Cargando',
            //     'mname' => null,
            //     'role' => 'Teacher',
            //     'address' => 'Pangasinan',
            //     'admin_pic' => null,
            //     'email' => 'gj.Cargando@gmail.com',
            //     'password' => Hash::make('password123'),
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
            // [
            //     'fname' => 'Lorna',
            //     'lname' => 'Bernal',
            //     'mname' => 'P',
            //     'role' => 'Teacher',
            //     'address' => 'Esperanza, La Union',
            //     'admin_pic' => null,
            //     'email' => 'Lorna.Bernal@gmail.com',
            //     'password' => Hash::make('password123'),
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
            // [
            //     'fname' => 'Jonathan',
            //     'lname' => 'Puyawan',
            //     'mname' => 'P',
            //     'role' => 'Teacher',
            //     'address' => 'La Union',
            //     'admin_pic' => null,
            //     'email' => 'Puyawan.Jonathan@gmail.com',
            //     'password' => Hash::make('password123'),
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
            // [
            //     'fname' => 'Aimee',
            //     'lname' => 'Bulatao',
            //     'mname' => 'P',
            //     'role' => 'Teacher',
            //     'address' => 'Pangasinan',
            //     'admin_pic' => null,
            //     'email' => 'Aimee.Bulatao@gmail.com',
            //     'password' => Hash::make('password123'),
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
            // [
            //     'fname' => 'Elvie',
            //     'lname' => 'Padua',
            //     'mname' => null,
            //     'role' => 'Teacher',
            //     'address' => 'Baguio',
            //     'admin_pic' => null,
            //     'email' => 'Elvie.Padua@gmail.com',
            //     'password' => Hash::make('password123'),
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
            // [
            //     'fname' => 'Junel',
            //     'lname' => 'De Vera',
            //     'mname' => 'P',
            //     'role' => 'Teacher',
            //     'address' => 'Pangasinan',
            //     'admin_pic' => null,
            //     'email' => 'Junel.dv@gmail.com',
            //     'password' => Hash::make('password123'),
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
            // [
            //     'fname' => 'Jomar',
            //     'lname' => 'Bacani',
            //     'mname' => null,
            //     'role' => 'Registrar',
            //     'address' => 'Pangasinan',
            //     'admin_pic' => null,
            //     'email' => 'Jomar.Bacani@gmail.com',
            //     'password' => Hash::make('password123'),
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
            // [
            //     'fname' => 'Jeffrey',
            //     'lname' => 'Moskito',
            //     'mname' => 'A',
            //     'role' => 'Teacher',
            //     'address' => 'Pangasinan',
            //     'admin_pic' => null,
            //     'email' => 'Jeffrey.Moskito@gmail.com',
            //     'password' => Hash::make('password123'),
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
            // [
            //     'fname' => 'Ervie',
            //     'lname' => 'Failma',
            //     'mname' => 'B',
            //     'role' => 'Teacher',
            //     'address' => 'Pangasinan',
            //     'admin_pic' => null,
            //     'email' => 'Ervie.Failma@gmail.com',
            //     'password' => Hash::make('password123'),
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
            
        ]);
    }
}
