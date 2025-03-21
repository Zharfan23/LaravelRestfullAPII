<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Database\QueryException;

class CategoryController extends Controller
{
    public function index()
    {
        try {
            $categories = Category::all();
            return view('categories.index', compact('categories'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error retrieving categories: ' . $e->getMessage());
        }
    }

    public function create()
    {
        try {
            return view('categories.create');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error loading create form: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|unique:categories',
            ]);

            Category::create($request->all());

            return redirect()->route('categories.index')
                ->with('success', 'Category created successfully.');
        } catch (QueryException $e) {
            return redirect()->back()->with('error', 'Database error: ' . $e->getMessage())->withInput();
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error creating category: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Category $category)
    {
        try {
            return view('categories.show', compact('category'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error showing category: ' . $e->getMessage());
        }
    }

    public function edit(Category $category)
    {
        try {
            return view('categories.edit', compact('category'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error loading edit form: ' . $e->getMessage());
        }
    }

    public function update(Request $request, Category $category)
    {
        try {
            $request->validate([
                'name' => 'sometimes|unique:categories,name,' . $category->id,
            ]);

            $category->update($request->all());

            return redirect()->route('categories.index')
                ->with('success', 'Category updated successfully');
        } catch (QueryException $e) {
            return redirect()->back()->with('error', 'Database error: ' . $e->getMessage())->withInput();
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error updating category: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Category $category)
    {
        try {
            $category->delete();
            return redirect()->route('categories.index')
                ->with('success', 'Category deleted successfully');
        } catch (QueryException $e) {
            return redirect()->back()->with('error', 'Database error: ' . $e->getMessage());
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error deleting category: ' . $e->getMessage());
        }
    }
}