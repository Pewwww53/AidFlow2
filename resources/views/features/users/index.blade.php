@extends('features.layout')

@section('title', 'Users Management - AidFlow')
@section('page-title', 'Users')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-900">User Management</h2>
        <button id="addUserBtn" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            <i class="fas fa-plus mr-2"></i> Add User
        </button>
    </div>

    <!-- Filters -->
    <form method="GET" action="{{ route('users.index') }}" class="bg-white rounded-lg shadow p-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <input type="text" name="search" placeholder="Search users..." value="{{ request('search') }}" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <select name="role" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Roles</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>User</option>
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                <i class="fas fa-search mr-2"></i> Filter
            </button>
        </div>
    </form>

    <!-- Users Table -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b">
                        <th class="text-left py-3 px-4 font-medium text-gray-700">Name</th>
                        <th class="text-left py-3 px-4 font-medium text-gray-700">Username</th>
                        <th class="text-left py-3 px-4 font-medium text-gray-700">Email</th>
                        <th class="text-left py-3 px-4 font-medium text-gray-700">Role</th>
                        <th class="text-left py-3 px-4 font-medium text-gray-700">Created</th>
                        <th class="text-left py-3 px-4 font-medium text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-3 px-4 text-gray-900">{{ $user->name }}</td>
                            <td class="py-3 px-4 text-gray-900">{{ $user->username }}</td>
                            <td class="py-3 px-4 text-gray-900">{{ $user->email }}</td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-1 rounded text-xs font-medium {{ $user->role === 'admin' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800' }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-gray-900">{{ $user->created_at->format('Y-m-d') }}</td>
                            <td class="py-3 px-4">
                                <button type="button" class="edit-user-btn text-blue-600 hover:text-blue-800 mr-2" data-id="{{ $user->id }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                @if(Auth::id() !== $user->id)
                                    <form method="POST" action="{{ route('users.destroy', $user) }}" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Delete this user?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-4 px-4 text-center text-gray-600">No users found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="flex justify-center">
        {{ $users->links() }}
    </div>
</div>

<!-- Add/Edit User Modal -->
<div id="userModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-lg max-w-md w-full p-6 shadow-xl">
        <h3 id="userModalTitle" class="text-lg font-bold text-gray-900 mb-4">Add New User</h3>
        <form id="userForm" method="POST" action="{{ route('users.store') }}" class="space-y-4">
            @csrf
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                <input type="text" name="name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                <input type="text" name="username" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div id="passwordField">
                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" name="password" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                <select name="role" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            
            <div class="flex gap-3">
                <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Save</button>
                <button type="button" id="closeUserModal" class="flex-1 px-4 py-2 bg-gray-300 text-gray-900 rounded-lg hover:bg-gray-400">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
    const userModal = document.getElementById('userModal');
    const addUserBtn = document.getElementById('addUserBtn');
    const closeUserModalBtn = document.getElementById('closeUserModal');
    const userForm = document.getElementById('userForm');
    const userModalTitle = document.getElementById('userModalTitle');

    addUserBtn.addEventListener('click', () => {
        userModalTitle.textContent = 'Add New User';
        userForm.action = '{{ route("users.store") }}';
        userForm.method = 'POST';
        document.getElementById('passwordField').style.display = 'block';
        const passwordInput = document.querySelector('[name="password"]');
        if (passwordInput) passwordInput.required = true;
        userForm.reset();
        userModal.classList.remove('hidden');
    });

    closeUserModalBtn.addEventListener('click', () => {
        userModal.classList.add('hidden');
    });

    document.querySelectorAll('.edit-user-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const userId = btn.dataset.id;
            userModalTitle.textContent = 'Edit User';
            userForm.action = `{{ route('users.index') }}/${userId}`;
            
            // Add method override for PUT
            let methodInput = userForm.querySelector('input[name="_method"]');
            if (!methodInput) {
                methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                userForm.appendChild(methodInput);
            }
            methodInput.value = 'PUT';
            
            document.getElementById('passwordField').style.display = 'none';
            const passwordInput = document.querySelector('[name="password"]');
            if (passwordInput) passwordInput.required = false;
            
            userModal.classList.remove('hidden');
        });
    });

    // Close modal when clicking outside
    userModal.addEventListener('click', (e) => {
        if (e.target === userModal) {
            userModal.classList.add('hidden');
        }
    });
</script>
@endsection
