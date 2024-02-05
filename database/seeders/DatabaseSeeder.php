<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\Role;
use App\Models\User;
use App\Models\Variant;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        ProductType::create([
            "name" => "Test1",
	        "description" => "Test1"
        ]);

        Role::create([
            "name" => "admin"
        ]);

        User::factory(10)->create();
        Product::factory(10)->create();
        Variant::factory(10)->create();

//         \App\Models\User::factory()->create([
//             'name' => 'Test User',
//             'email' => 'test@example.com',
//         ]);
    }
}
