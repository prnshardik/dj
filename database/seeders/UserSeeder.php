<?php

    namespace Database\Seeders;
    use App\Models\User;

    use Illuminate\Database\Seeder;

    class UserSeeder extends Seeder{

        public function run(){
            User::create([
                'name' => 'Super Admin',
                'phone' => '1234567890',
                'email' => 'superadmin@mail.com',
                'password' => bcrypt('Admin@123'),
                'is_admin' => 'y',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => 1,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => 1
            ]);

            User::create([
                'name' => 'Mitul Gajjar',
                'phone' => '9879879871',
                'email' => 'mitul@yopmail.com',
                'password' => bcrypt('Admin@123'),
                'is_admin' => 'n',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => 1,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => 1
            ]);

            User::create([
                'name' => 'HardIk Patel',
                'phone' => '9879879872',
                'email' => 'hardik@yopmail.com',
                'password' => bcrypt('Admin@123'),
                'is_admin' => 'n',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => 1,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => 1
            ]);

            User::create([
                'name' => 'Mustan',
                'phone' => '9879879873',
                'email' => 'mustan@yopmail.com',
                'password' => bcrypt('Admin@123'),
                'is_admin' => 'n',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => 1,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => 1
            ]);

            User::create([
                'name' => 'Raju Bhai',
                'phone' => '9879879874',
                'email' => 'rajubhai@yopmail.com',
                'password' => bcrypt('Admin@123'),
                'is_admin' => 'n',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => 1,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => 1
            ]);

            User::create([
                'name' => 'Kiran',
                'phone' => '9879879875',
                'email' => 'kiran@yopmail.com',
                'password' => bcrypt('Admin@123'),
                'is_admin' => 'n',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => 1,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => 1
            ]);

            User::create([
                'name' => 'Manish',
                'phone' => '9879879876',
                'email' => 'manish@yopmail.com',
                'password' => bcrypt('Admin@123'),
                'is_admin' => 'n',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => 1,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => 1
            ]);

            User::create([
                'name' => 'Vipul',
                'phone' => '9879879877',
                'email' => 'vipul@yopmail.com',
                'password' => bcrypt('Admin@123'),
                'is_admin' => 'n',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => 1,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => 1
            ]);

            User::create([
                'name' => 'Pritesh',
                'phone' => '98798798778',
                'email' => 'pritesh@yopmail.com',
                'password' => bcrypt('Admin@123'),
                'is_admin' => 'n',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => 1,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => 1
            ]);

            User::create([
                'name' => 'Chinjal',
                'phone' => '98798798779',
                'email' => 'chinjal@yopmail.com',
                'password' => bcrypt('Admin@123'),
                'is_admin' => 'n',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => 1,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => 1
            ]);

            User::create([
                'name' => 'Niranjan',
                'phone' => '98798798710',
                'email' => 'niranjan@yopmail.com',
                'password' => bcrypt('Admin@123'),
                'is_admin' => 'n',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => 1,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => 1
            ]);
        }
    }
