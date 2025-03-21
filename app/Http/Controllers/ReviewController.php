<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Book;
use App\Models\User;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Database\QueryException;

class ReviewController extends Controller
{
    public function index()
    {
        try {
            $reviews = Review::with(['user', 'book'])->get();
            return view('reviews.index', compact('reviews'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error retrieving reviews: ' . $e->getMessage());
        }
    }

    public function create()
    {
        try {
            $users = User::all();
            $books = Book::all();
            return view('reviews.create', compact('users', 'books'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error loading create form: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'book_id' => 'required|exists:books,id',
                'user_id' => 'required|exists:users,id',
                'rating' => 'required|integer|min:1|max:5',
                'comment' => 'required',
            ]);

            Review::create($request->all());

            return redirect()->route('reviews.index')
                ->with('success', 'Review created successfully.');
        } catch (QueryException $e) {
            return redirect()->back()->with('error', 'Database error: ' . $e->getMessage())->withInput();
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error creating review: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Review $review)
    {
        try {
            return view('reviews.show', compact('review'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error showing review: ' . $e->getMessage());
        }
    }

    public function edit(Review $review)
    {
        try {
            $users = User::all();
            $books = Book::all();
            return view('reviews.edit', compact('review', 'users', 'books'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error loading edit form: ' . $e->getMessage());
        }
    }

    public function update(Request $request, Review $review)
    {
        try {
            $request->validate([
                'book_id' => 'sometimes|exists:books,id',
                'user_id' => 'sometimes|exists:users,id',
                'rating' => 'sometimes|integer|min:1|max:5',
                'comment' => 'sometimes',
            ]);

            $review->update($request->all());

            return redirect()->route('reviews.index')
                ->with('success', 'Review updated successfully');
        } catch (QueryException $e) {
            return redirect()->back()->with('error', 'Database error: ' . $e->getMessage())->withInput();
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error updating review: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Review $review)
    {
        try {
            $review->delete();
            return redirect()->route('reviews.index')
                ->with('success', 'Review deleted successfully');
        } catch (QueryException $e) {
            return redirect()->back()->with('error', 'Database error: ' . $e->getMessage());
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error deleting review: ' . $e->getMessage());
        }
    }
}
