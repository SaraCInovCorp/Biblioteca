<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\BookRequestItem;
use App\Models\Livro;
use App\Models\User;
use App\Models\BookRequest;

class BookReview extends Model
{
    /** @use HasFactory<\Database\Factories\BookReviewFactory> */
    use HasFactory;

    protected $table = 'book_reviews';

    protected $fillable = [
        'book_request_item_id',
        'livro_id',
        'user_id',
        'review_text',
        'status',
        'admin_justification',
    ];

     public function bookRequestItem()
    {
        return $this->belongsTo(BookRequestItem::class);
    }

    public function livro()
    {
        return $this->belongsTo(Livro::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
