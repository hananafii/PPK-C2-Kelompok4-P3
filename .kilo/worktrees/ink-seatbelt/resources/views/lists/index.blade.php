@extends('layouts.app')
@section('content')
<h1>Daftar Tugas Saya</h1><form method="POST" action="{{ route('lists.store') }}">@csrf <input name="name" placeholder="Nama daftar tugas" required><button>Buat Daftar</button></form>
<h2>Daftar</h2><ul>@forelse($projects as $project)<li><a href="{{ route('lists.show',$project) }}">{{ $project->name }}</a> — {{ $project->tasks_count }} tugas</li>@empty<li>Belum ada daftar.</li>@endforelse</ul>
@endsection
