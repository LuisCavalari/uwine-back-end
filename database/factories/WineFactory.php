<?php

namespace Database\Factories;

use App\Models\Wine;
use Illuminate\Database\Eloquent\Factories\Factory;

class WineFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Wine::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => 1,
            'name' => $this->faker->name,
            'description' => $this->faker->text(200),
            'year' => $this->faker->year(),
            'grade' => rand(0,10)
        ];
    }
}
