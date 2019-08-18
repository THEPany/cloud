<?php

use App\Organization;
use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Laravel\Cashier\Subscription;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = factory(User::class)->create(['email' => 'admin@admin.com']);

        factory(User::class)->times(1000)->create();

        factory(Subscription::class)->state('active')->create([
            'user_id' => $admin
        ]);

        factory(Organization::class)->times(500)->create([
            'user_id' => $admin
        ]);
    }
}
