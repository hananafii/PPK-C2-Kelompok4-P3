<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // Melihat Daftar Pengguna
    public function index()
    {
        $users = User::orderBy('id', 'asc')->get();

        return view('users.index', compact('users'));
    }

    // Menampilkan Form Tambah Pengguna
    public function create()
    {
        return view('users.create');
    }

    // Menyimpan Pengguna Baru & Validasi
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Enkripsi password
        ]);

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil ditambahkan!');
    }

    // Menghapus Pengguna
    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('users.index')->with('success', 'Akun berhasil dihapus!');
    }
}
