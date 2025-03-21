<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Book;
use App\Models\User;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Database\QueryException;

class LoanController extends Controller
{
    public function index()
    {
        try {
            $loans = Loan::with(['user', 'book'])->get();
            return view('loans.index', compact('loans'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error retrieving loans: ' . $e->getMessage());
        }
    }

    public function create()
    {
        try {
            $users = User::all();
            $books = Book::all();
            return view('loans.create', compact('users', 'books'));
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
                'loan_date' => 'required|date',
                'status' => 'required|in:borrowed,returned',
            ]);

            Loan::create([
                'book_id' => $request->book_id,
                'user_id' => $request->user_id,
                'loan_date' => $request->loan_date,
                'return_date' => $request->return_date,
                'status' => $request->status,
            ]);

            return redirect()->route('loans.index')
                ->with('success', 'Loan created successfully.');
        } catch (QueryException $e) {
            return redirect()->back()->with('error', 'Database error: ' . $e->getMessage())->withInput();
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error creating loan: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Loan $loan)
    {
        try {
            return view('loans.show', compact('loan'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error showing loan: ' . $e->getMessage());
        }
    }

    public function edit(Loan $loan)
    {
        try {
            $users = User::all();
            $books = Book::all();
            return view('loans.edit', compact('loan', 'users', 'books'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error loading edit form: ' . $e->getMessage());
        }
    }

    public function update(Request $request, Loan $loan)
    {
        try {
            $request->validate([
                'book_id' => 'sometimes|exists:books,id',
                'user_id' => 'sometimes|exists:users,id',
                'loan_date' => 'sometimes|date',
                'return_date' => 'nullable|date',
                'status' => 'sometimes|in:borrowed,returned',
            ]);

            $loan->update($request->all());

            return redirect()->route('loans.index')
                ->with('success', 'Loan updated successfully');
        } catch (QueryException $e) {
            return redirect()->back()->with('error', 'Database error: ' . $e->getMessage())->withInput();
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error updating loan: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Loan $loan)
    {
        try {
            $loan->delete();
            return redirect()->route('loans.index')
                ->with('success', 'Loan deleted successfully');
        } catch (QueryException $e) {
            return redirect()->back()->with('error', 'Database error: ' . $e->getMessage());
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error deleting loan: ' . $e->getMessage());
        }
    }
}