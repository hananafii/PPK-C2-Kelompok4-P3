<!DOCTYPE html>
<html><body>
    <h1>JARA - Kelola Tugas Pribadi dan Tim</h1>
    <p>Buat daftar tugas, atur prioritas dan deadline, tambahkan anggota, lalu pantau progress tugas bersama.</p>
    @auth
        <p><a href="{{ route('lists.index') }}">Buka Daftar Tugas Saya</a></p>
    @else
        <p><a href="{{ route('login') }}">Login</a> | <a href="{{ route('register') }}">Register</a></p>
    @endauth
</body></html>
