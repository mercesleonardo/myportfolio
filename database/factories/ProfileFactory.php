<?php

namespace Database\Factories;

use App\Models\{Profile, User};
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Profile>
 */
class ProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id'            => User::factory(),
            'professional_title' => fake()->jobTitle(),
            'bio'                => fake()->paragraphs(asText: true),
            'avatar_path'        => null,
            'resume_path'        => null,
        ];
    }
}
