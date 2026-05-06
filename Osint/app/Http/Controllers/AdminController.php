<?php

namespace App\Http\Controllers;

use App\Models\LoginAttempt;
use App\Models\SearchLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AdminController extends Controller
{
    public function users()
    {
        $users = User::withCount('searchLogs')->latest()->get();
        return view('admin.users', compact('users'));
    }

    public function createUser()
    {
        return view('admin.user-form', ['user' => null]);
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'username'   => 'required|string|max:100|unique:users',
            'email'      => 'required|email|unique:users',
            'password'   => ['required', Password::min(8)->mixedCase()->numbers()],
            'role'       => 'required|in:admin,operator,viewer',
            'api_token'  => 'nullable|string|max:100',
        ]);

        User::create([
            'name'      => $request->name,
            'username'  => $request->username,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'role'      => $request->role,
            'api_token' => $request->api_token,
            'is_active' => true,
        ]);

        return redirect()->route('admin.users')->with('success', 'User berhasil dibuat.');
    }

    public function editUser(int $id)
    {
        $user = User::findOrFail($id);
        return view('admin.user-form', compact('user'));
    }

    public function updateUser(Request $request, int $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'      => 'required|string|max:255',
            'username'  => 'required|string|max:100|unique:users,username,' . $id,
            'email'     => 'required|email|unique:users,email,' . $id,
            'password'  => ['nullable', Password::min(8)->mixedCase()->numbers()],
            'role'      => 'required|in:admin,operator,viewer',
            'is_active' => 'boolean',
            'api_token' => 'nullable|string|max:100',
        ]);

        $data = [
            'name'      => $request->name,
            'username'  => $request->username,
            'email'     => $request->email,
            'role'      => $request->role,
            'is_active' => $request->boolean('is_active'),
            'api_token' => $request->api_token,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users')->with('success', 'User berhasil diperbarui.');
    }

    public function deleteUser(int $id)
    {
        if ($id === auth()->id()) {
            return back()->withErrors(['error' => 'Tidak bisa menghapus akun sendiri.']);
        }

        User::findOrFail($id)->delete();
        return redirect()->route('admin.users')->with('success', 'User berhasil dihapus.');
    }

    public function logs()
    {
        $searchLogs  = SearchLog::with('user')->latest()->paginate(50, ['*'], 'search_page');
        $loginLogs   = LoginAttempt::latest()->paginate(50, ['*'], 'login_page');
        return view('admin.logs', compact('searchLogs', 'loginLogs'));
    }
}
