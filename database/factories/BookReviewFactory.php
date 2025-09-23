<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\BookReview;
use App\Models\BookRequestItem;
use App\Models\Livro;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BookReview>
 */
class BookReviewFactory extends Factory
{
    protected $model = BookReview::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $bookRequestItemId = BookRequestItem::inRandomOrder()->value('id') ?? null;
        $livroId = Livro::inRandomOrder()->value('id') ?? null;
        $userId = User::inRandomOrder()->value('id') ?? null;

        return [
            'book_request_item_id' => $bookRequestItemId,
            'livro_id' => $livroId,
            'user_id' => $userId,
            'review_text' => $this->faker->paragraph(),
            'status' => $this->faker->randomElement(['suspenso', 'ativo', 'recusado']),
            'admin_justification' => $this->faker->optional()->sentence(),
        ];
    }
}
