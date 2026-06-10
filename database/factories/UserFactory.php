<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        $dateOfBirth = fake()->dateTimeBetween('-60 years', '-18 years');

        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'gender' => fake()->randomElement(['laki-laki', 'perempuan']),
            'phone' => '08'.fake()->unique()->numerify('##########'),
            'date_of_birth' => $dateOfBirth->format('Y-m-d'),
            'age' => (int) now()->diffInYears($dateOfBirth),
            'weight' => fake()->randomFloat(1, 45, 95),
            'height' => fake()->randomFloat(1, 150, 185),
            'address' => fake()->address(),
            'occupation' => fake()->jobTitle(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
