<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserControler extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index(Request $request)
    {
        $users = $this->userService->getAllUsers($request->search);

        return view('user.index', [
            'users' => $users
        ]);
    }

    public function create()
    {
        return view('user.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => ['required', 'max:100'],
            'username' => ['required', 'max:100', 'unique:users'],
            'role' => ['required', 'in:admin,petugas'],
            'password' => ['required', 'max:100', 'confirmed']
        ]);

        $this->userService->createUser($request->all());

        return redirect()->route('user.index')->with('store', 'success');
    }

    public function show(User $user)
    {
        abort(404);
    }

    public function edit(User $user)
    {
        return view('user.edit', [
            'user' => $user
        ]);
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'nama' => ['required', 'max:100'],
            'username' => ['required', 'max:100', 'unique:users,username,' . $user->id],
            'role' => ['required', 'in:admin,petugas'],
            'password_baru' => ['nullable', 'max:100', 'confirmed']
        ]);

        $this->userService->updateUser($user, $request->all());

        return redirect()->route('user.index')->with('update', 'success');
    }

    public function destroy(User $user)
    {
        // Cek apakah user yang akan dihapus adalah user yang sedang login
        if (auth()->id() === $user->id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun yang sedang Anda gunakan!');
        }

        $this->userService->deleteUser($user);

        return back()->with('destroy', 'success');
    }
}
