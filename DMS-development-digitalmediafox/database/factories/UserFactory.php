<?php

namespace Database\Factories;

use App\Enums\{Gender, Salutation};
use App\Models\{Branch, Country, Department, Designation, EmploymentType, Language, Role};
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static string $name = 'Zain Ali';
    protected static string $email = 'zain@ilab.sa';
    protected static string $password = 'zain@1234';
    protected static string $salutation = Salutation::Mr->value;
    protected static string $gender = Gender::Male->value;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'designation_id' => Designation::find(1)->id,
            'department_id' => Department::find(1)->id,
            'role_id' => Role::factory()->create()->id,
            'employment_type_id' => EmploymentType::find(1)->id,
            'branch_id' => Branch::find(1)->id,
            'country_id' => Country::find(1)->id,
            'language_id' => Language::find(1)->id,
            'user_id' => fake()->uuid(),
            'salutation' => static::$salutation,
            'gender' => static::$gender,
            'name' => static::$name ??= fake()->name(),
            'email' => static::$email ??= fake()->email(),
            'password' => Hash::make(static::$password),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
