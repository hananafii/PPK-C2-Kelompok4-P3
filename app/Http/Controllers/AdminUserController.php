<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function index(): View
    {
        $this->authorizeAdmin();

        return view('admin.users', ['users' => User::query()->orderBy('name')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAdmin();
        $data = $request->validate(['name' => ['required', 'max:255'], 'email' => ['required', 'email', 'unique:users,email'], 'password' => ['required', 'min:6']]);
        User::create($data);

        return to_route('admin.users');
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->authorizeAdmin();
        abort_if($user->id === auth()->id(), 422, 'Admin tidak dapat menghapus akun sendiri.');
        $user->delete();

        return to_route('admin.users');
    }

    private function authorizeAdmin(): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
    }
}
