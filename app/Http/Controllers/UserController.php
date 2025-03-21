<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Exception;
use Illuminate\Database\QueryException;

class UserController extends Controller
{
    public function index()
    {
        try {
            $users = User::all();
            return view('users.index', compact('users'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error retrieving users: ' . $e->getMessage());
        }
    }

    public function create()
    {
        try {
            return view('users.create');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error loading create form: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'username' => 'required|unique:users',
                'password' => 'required|min:6',
                'name' => 'required',
                'email' => 'required|email|unique:users',
                'phone' => 'required',
            ]);

            User::create([
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
            ]);

            return redirect()->route('users.index')
                ->with('success', 'User created successfully.');
        } catch (QueryException $e) {
            return redirect()->back()->with('error', 'Database error: ' . $e->getMessage())->withInput();
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error creating user: ' . $e->getMessage())->withInput();
        }
    }

    public function show(User $user)
    {
        try {
            return view('users.show', compact('user'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error showing user: ' . $e->getMessage());
        }
    }

    public function edit(User $user)
    {
        try {
            return view('users.edit', compact('user'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error loading edit form: ' . $e->getMessage());
        }
    }

    public function update(Request $request, User $user)
    {
        try {
            $request->validate([
                'username' => 'sometimes|unique:users,username,' . $user->id,
                'name' => 'sometimes',
                'email' => 'sometimes|email|unique:users,email,' . $user->id,
                'phone' => 'sometimes',
            ]);

            $user->update($request->all());

            return redirect()->route('users.index')
                ->with('success', 'User updated successfully');
        } catch (QueryException $e) {
            return redirect()->back()->with('error', 'Database error: ' . $e->getMessage())->withInput();
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error updating user: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(User $user)
    {
        try {
            $user->delete();
            return redirect()->route('users.index')
                ->with('success', 'User deleted successfully');
        } catch (QueryException $e) {
            return redirect()->back()->with('error', 'Database error: ' . $e->getMessage());
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error deleting user: ' . $e->getMessage());
        }
    }
}