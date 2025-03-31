<table>
    <thead>
    <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Current Role</th>
        <th>Actions</th>
    </tr>
    </thead>
    <tbody>
    @foreach($users as $user)
        <tr>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            <td>
                <form method="POST" action="{{ route('admin.updateRole', $user->id) }}">
                    @csrf
                    @method('PUT')
                    <select name="role" required>
                        <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="creator" {{ $user->role === 'creator' ? 'selected' : '' }}>Creator</option>
                        <option value="member" {{ $user->role === 'member' ? 'selected' : '' }}>Member</option>
                    </select>
                    <button type="submit">Save Role</button>
                </form>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
