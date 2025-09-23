<?php

namespace App\Http\Controllers;

use App\Models\BookReview;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\BookRequestItem;
use App\Models\BookRequest;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = BookReview::with('livro', 'bookRequestItem', 'user');

        if ($request->search_review_id) {
            $query->where('id', $request->search_review_id);
        }

        if ($request->search_request_id) {
            $query->whereHas('bookRequestItem', function($q) use ($request) {
                $q->where('book_request_id', $request->search_request_id);
            });
        }

        if ($request->search_user) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search_user . '%');
            });
        }

        if ($request->search_book) {
            $query->whereHas('livro', function($q) use ($request) {
                $q->where('titulo', 'like', '%' . $request->search_book . '%');
            });
        }

        if ($request->search_status) {
            $query->where('status', $request->search_status);
        }

        $bookReviews = $query->paginate(10)->appends($request->all());

        return view('reviews.index', compact('bookReviews'));
    }


    public function bulkUpdate(Request $request)
    {
        $data = $request->validate([
            'review_ids' => 'required|array',
            'review_ids.*' => 'integer|exists:book_reviews,id',
            'new_status' => 'required|in:ativo,recusado,suspenso',
            'admin_justification' => 'nullable|string',
        ]);

        foreach ($data['review_ids'] as $id) {
            $review = BookReview::find($id);
            $review->status = $data['new_status'];
            $review->admin_justification = $data['admin_justification'] ?? null;
            $review->save();
        }

        return redirect()->route('reviews.index')->with('success', 'Status atualizados com sucesso.');
    }

    /**
     * Display the specified resource.
     */
    public function show(BookReview $bookReview)
    {
        return view('reviews.show', compact('bookReview'));
    }

    public function edit(BookReview $bookReview)
    {
        return view('reviews.edit', ['bookReview' => $bookReview]);
    }

    public function update(Request $request, BookReview $bookReview)
    {
        $data = $request->validate([
            'status' => 'required|in:ativo,recusado,suspenso',
            'admin_justification' => 'nullable|string',
        ]);

        $bookReview->update($data);

        return redirect()->route('reviews.index')->with('success', 'Review atualizada com sucesso.');
    }


}
