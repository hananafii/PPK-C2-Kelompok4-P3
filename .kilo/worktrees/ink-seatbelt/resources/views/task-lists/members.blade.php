<!DOCTYPE html>
<html>
<head><title>JARA - Collaboration</title></head>
<body>
    <h1>Collaboration - {{ $taskList->name }}</h1>

    @if(session('success'))
        <p>{{ session('success') }}</p>
    @endif

    @if(session('error'))
        <p>{{ session('error') }}</p>
    @endif

    <h2>Progress</h2>
    <p>{{ $progress }}% task selesai</p>

    <h2>Tambah Member</h2>

    <form method="POST" action="{{ route('task-lists.members.store', $taskList) }}">
        @csrf

        <select name="user_id" required>
            <option value="">Pilih user</option>
            @foreach($availableUsers as $user)
                <option value="{{ $user->id }}">{{ $user->name }} - {{ $user->email }}</option>
            @endforeach
        </select>

        <button type="submit">Tambah</button>
    </form>

    <h2>Member</h2>

    <p>Owner: {{ $taskList->owner->name }}</p>

    <ul>
        @forelse($taskList->members as $member)
            <li>
                {{ $member->name }} - {{ $member->email }}

                <form method="POST"
                      action="{{ route('task-lists.members.destroy', [$taskList, $member]) }}"
                      style="display:inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit">Hapus</button>
                </form>
            </li>
        @empty
            <li>Belum ada member.</li>
        @endforelse
    </ul>

    <a href="{{ route('task-lists.show', $taskList) }}">Kembali ke Task List</a>
</body>
</html>
