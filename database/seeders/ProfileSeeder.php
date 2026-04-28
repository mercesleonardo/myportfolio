<?php

namespace Database\Seeders;

use App\Models\{Profile, User};
use Illuminate\Database\Seeder;

class ProfileSeeder extends Seeder
{
    public function run(): void
    {
        User::query()
            ->doesntHave('profile')
            ->each(function (User $user): void {
                Profile::factory()->for($user)->create();
            });
    }
}
