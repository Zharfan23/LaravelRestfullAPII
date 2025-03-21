<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Database\QueryException;

class BookController extends Controller
{
    public function index()
    {
        try {
            $books = Book::with(['user', 'category'])->get();
            return view('books.index', compact('books'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error retrieving books: ' . $e->getMessage());
        }
    }

    public function create()
    {
        try {
            $users = User::all();
            $categories = Category::all();
            return view('books.create', compact('users', 'categories'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error loading create form: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required',
                'writer' => 'required',
                'user_id' => 'required|exists:users,id',
                'category_id' => 'required|exists:categories,id',
                'publisher' => 'required',
                'year' => 'required|numeric|min:1900|max:' . date('Y'),
            ]);

            Book::create($request->all());

            return redirect()->route('books.index')
                ->with('success', 'Book created successfully.');
        } catch (QueryException $e) {
            return redirect()->back()->with('error', 'Database error: ' . $e->getMessage())->withInput();
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error creating book: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Book $book)
    {
        try {
            return view('books.show', compact('book'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error showing book: ' . $e->getMessage());
        }
    }

    public function edit(Book $book)
    {
        try {
            $users = User::all();
            $categories = Category::all();
            return view('books.edit', compact('book', 'users', 'categories'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error loading edit form: ' . $e->getMessage());
        }
    }

    public function update(Request $request, Book $book)
    {
        try {
            $request->validate([
                'title' => 'sometimes',
                'writer' => 'sometimes',
                'user_id' => 'sometimes|exists:users,id',
                'category_id' => 'sometimes|exists:categories,id',
                'publisher' => 'sometimes',
                'year' => 'sometimes|numeric|min:1900|max:' . date('Y'),
            ]);

            $book->update($request->all());

            return redirect()->route('books.index')
                ->with('success', 'Book updated successfully');
        } catch (QueryException $e) {
            return redirect()->back()->with('error', 'Database error: ' . $e->getMessage())->withInput();
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error updating book: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Book $book)
    {
        try {
            $book->delete();
            return redirect()->route('books.index')
                ->with('success', 'Book deleted successfully');
        } catch (QueryException $e) {
            return redirect()->back()->with('error', 'Database error: ' . $e->getMessage());
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error deleting book: ' . $e->getMessage());
        }
    }
}