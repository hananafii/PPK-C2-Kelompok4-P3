@extends('layouts.app')
@section('content')
<h1>Kelola Pengguna</h1><form method="POST" action="{{ route('admin.users.store') }}">@csrf <p>Nama <input name="name" required></p><p>Email <input name="email" type="email" required></p><p>Password <input name="password" type="password" required></p><button>Tambah Pengguna</button></form>
<table border="1" cellpadding="6"><tr><th>Nama</th><th>Email</th><th>Admin</th><th>Aksi</th></tr>@foreach($users as $user)<tr><td>{{ $user->name }}</td><td>{{ $user->email }}</td><td>{{ $user->is_admin ? 'Ya' : 'Tidak' }}</td><td>@if($user->id !== auth()->id())<form method="POST" action="{{ route('admin.users.destroy',$user) }}">@csrf @method('DELETE')<button>Hapus</button></form>@endif</td></tr>@endforeach</table>
@endsection
