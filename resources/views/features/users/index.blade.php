@extends('features.layout')

@section('title', 'Users Management - AidFlow')
@section('page-title', 'Users')

@section('content')
    <div class="flex flex-col gap-4 min-h-[calc(100vh-120px)] w-full border-4 rounded-2xl border-red-600 p-4">
        <div class="flex flex-row gap-4">
            <h1 class="text-2xl font-bold text-red-800">Users Management</h1>
            <input type="text" placeholder="Search users..."
                class="flex-1 border border-gray-300 rounded-full px-4 py-2 w-full">
            <button
                class="cursor-pointer bg-red-300 text-red-500 border border-red-500 font-bold px-4 py-2 rounded-full w-50"
                onclick="document.getElementById('addUserModal').classList.remove('hidden')">
                + Add User</button>
        </div>
        <table class="w-full border border-gray-300 rounded-lg">
            <thead>
                <tr class="bg-red-300 text-red-500 font-bold">
                    <th class="px-4 py-2 text-left">Full Name</th>
                    <th class="px-4 py-2 text-left">Birth Date</th>
                    <th class="px-4 py-2 text-left">Gender</th>
                    <th class="px-4 py-2 text-left">Address</th>
                    <th class="px-4 py-2 text-left">Email</th>
                    <th class="px-4 py-2 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr class="border-t border-gray-300">
                        <td class="px-4 py-2">{{ $user->fullName }}</td>
                        <td class="px-4 py-2">{{ $user->birthDate }}</td>
                        <td class="px-4 py-2">{{ $user->gender }}</td>
                        <td class="px-4 py-2">{{ $user->address }}</td>
                        <td class="px-4 py-2">{{ $user->email }}</td>
                        <td class="flex flex-row gap-2 px-4 py-2">
                            <button type="button" class="cursor-pointer text-red-500 font-bold px-4 py-2 rounded"
                                onclick="openEditModal('{{ $user->username }}','{{ $user->fullName }}','{{ $user->email }}','{{ $user->password }}','{{ $user->birthDate }}','{{ $user->address }}','{{ $user->gender }}','{{ $user->role }}')">
                                <i class="fa fa-edit"></i>
                            </button>
                            <form action="{{ route('users.destroy', $user->username) }}" method="POST"
                                onsubmit="return confirm('Delete this user?')">
                                @csrf
                                @method('DELETE')

                                <button type="submit" class="cursor-pointer text-red-500 font-bold px-4 py-2 rounded">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div id="addUserModal"
        class="hidden fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="w-full max-w-5xl rounded-2xl border-4 border-red-600 bg-white shadow-2xl overflow-hidden">
            <div class="flex flex-col gap-4 p-4">
                <h2 class="text-2xl font-bold text-red-800">Add User</h2>
                <form action="{{ route('users.store') }}" method="POST" class="flex flex-col gap-4">
                    @csrf
                    <div class="flex flex-row gap-4">
                        <div class="flex-1">
                            <input type="text" name="fullName" placeholder="Full Name" value="{{ old('fullName') }}"
                                class="w-full border rounded-full px-4 py-2 {{ $errors->has('fullName') ? 'border-red-600 ring-1 ring-red-200' : 'border-gray-300' }}">
                            @if($errors->has('fullName'))
                                <p class="mt-1 text-sm text-red-600 flex items-center gap-2"><i
                                        class="fa fa-exclamation-circle"></i> {{ $errors->first('fullName') }}</p>
                            @endif
                        </div>
                        <div class="flex-1">
                            <input type="password" name="password" placeholder="Password"
                                class="w-full border rounded-full px-4 py-2 {{ $errors->has('password') ? 'border-red-600 ring-1 ring-red-200' : 'border-gray-300' }}">
                            @if($errors->has('password'))
                                <p class="mt-1 text-sm text-red-600 flex items-center gap-2"><i
                                        class="fa fa-exclamation-circle"></i> {{ $errors->first('password') }}</p>
                            @endif
                        </div>
                    </div>

                    <div>
                        <input type="email" name="email" placeholder="Email" value="{{ old('email') }}"
                            class="w-full border rounded-full px-4 py-2 {{ $errors->has('email') ? 'border-red-600 ring-1 ring-red-200' : 'border-gray-300' }}">
                        @if($errors->has('email'))
                            <p class="mt-1 text-sm text-red-600 flex items-center gap-2"><i
                                    class="fa fa-exclamation-circle"></i> {{ $errors->first('email') }}</p>
                        @endif
                    </div>

                    <div>
                        <input type="date" name="birthDate" placeholder="Birth Date" value="{{ old('birthDate') }}"
                            class="w-full border rounded-full px-4 py-2 {{ $errors->has('birthDate') ? 'border-red-600 ring-1 ring-red-200' : 'border-gray-300' }}">
                        @if($errors->has('birthDate'))
                            <p class="mt-1 text-sm text-red-600 flex items-center gap-2"><i
                                    class="fa fa-exclamation-circle"></i> {{ $errors->first('birthDate') }}</p>
                        @endif
                    </div>

                    <div>
                        <input type="text" name="address" placeholder="Address" value="{{ old('address') }}"
                            class="w-full border rounded-full px-4 py-2 {{ $errors->has('address') ? 'border-red-600 ring-1 ring-red-200' : 'border-gray-300' }}">
                        @if($errors->has('address'))
                            <p class="mt-1 text-sm text-red-600 flex items-center gap-2"><i
                                    class="fa fa-exclamation-circle"></i> {{ $errors->first('address') }}</p>
                        @endif
                    </div>

                    <div class="flex flex-row gap-4">
                        <div class="flex-1">
                            <select name="role"
                                class="w-full border rounded-full px-4 py-2 {{ $errors->has('role') ? 'border-red-600 ring-1 ring-red-200' : 'border-gray-300' }}">
                                <option value="">Role</option>
                                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>User</option>
                            </select>
                            @if($errors->has('role'))
                                <p class="mt-1 text-sm text-red-600 flex items-center gap-2"><i
                                        class="fa fa-exclamation-circle"></i> {{ $errors->first('role') }}</p>
                            @endif
                        </div>

                        <div class="flex-1">
                            <select name="gender"
                                class="w-full border rounded-full px-4 py-2 {{ $errors->has('gender') ? 'border-red-600 ring-1 ring-red-200' : 'border-gray-300' }}">
                                <option value="">Gender</option>
                                <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                            </select>
                            @if($errors->has('gender'))
                                <p class="mt-1 text-sm text-red-600 flex items-center gap-2"><i
                                        class="fa fa-exclamation-circle"></i> {{ $errors->first('gender') }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="flex flex-row gap-4 justify-end">
                        <button type="button"
                            class="cursor-pointer bg-gray-300 text-gray-500 border border-gray-500 font-bold px-4 py-2 rounded-full w-50"
                            onclick="document.getElementById('addUserModal').classList.add('hidden')">Cancel</button>
                        <button type="submit"
                            class="cursor-pointer bg-red-300 text-red-500 border border-red-500 font-bold px-4 py-2 rounded-full w-50">Add
                            User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="editUserModal"
        class="hidden fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="w-full max-w-5xl rounded-2xl border-4 border-red-600 bg-white shadow-2xl overflow-hidden">
            <div class="flex flex-col gap-4 p-4">
                <h2 class="text-2xl font-bold text-red-800">Update User</h2>
                <form action="{{ route('users.update', $user->username) }}" method="POST" class="flex flex-col gap-4">
                    @csrf
                    @method('PUT')
                    <div class="flex flex-row gap-4">
                        <div class="flex-1">
                            <input id="editFullName" type="text" name="fullName" placeholder="Full Name"
                                value="{{ old('fullName') }}"
                                class="w-full border rounded-full px-4 py-2 {{ $errors->has('fullName') ? 'border-red-600 ring-1 ring-red-200' : 'border-gray-300' }}">
                            @if($errors->has('fullName'))
                                <p class="mt-1 text-sm text-red-600 flex items-center gap-2"><i
                                        class="fa fa-exclamation-circle"></i> {{ $errors->first('fullName') }}</p>
                            @endif
                        </div>
                        <div class="flex-1">
                            <input id="editPassword" type="text" name="password" placeholder="Password"
                                class="w-full border rounded-full px-4 py-2 {{ $errors->has('password') ? 'border-red-600 ring-1 ring-red-200' : 'border-gray-300' }}">
                            @if($errors->has('password'))
                                <p class="mt-1 text-sm text-red-600 flex items-center gap-2"><i
                                        class="fa fa-exclamation-circle"></i> {{ $errors->first('password') }}</p>
                            @endif
                        </div>
                    </div>

                    <div>
                        <input id="editEmail" type="email" name="email" placeholder="Email" value="{{ old('email') }}"
                            class="w-full border rounded-full px-4 py-2 {{ $errors->has('email') ? 'border-red-600 ring-1 ring-red-200' : 'border-gray-300' }}">
                        @if($errors->has('email'))
                            <p class="mt-1 text-sm text-red-600 flex items-center gap-2"><i
                                    class="fa fa-exclamation-circle"></i> {{ $errors->first('email') }}</p>
                        @endif
                    </div>

                    <div>
                        <input id="editBirthDate" type="date" name="birthDate" placeholder="Birth Date"
                            value="{{ old('birthDate') }}"
                            class="w-full border rounded-full px-4 py-2 {{ $errors->has('birthDate') ? 'border-red-600 ring-1 ring-red-200' : 'border-gray-300' }}">
                        @if($errors->has('birthDate'))
                            <p class="mt-1 text-sm text-red-600 flex items-center gap-2"><i
                                    class="fa fa-exclamation-circle"></i> {{ $errors->first('birthDate') }}</p>
                        @endif
                    </div>

                    <div>
                        <input id="editAddress" type="text" name="address" placeholder="Address"
                            value="{{ old('address') }}"
                            class="w-full border rounded-full px-4 py-2 {{ $errors->has('address') ? 'border-red-600 ring-1 ring-red-200' : 'border-gray-300' }}">
                        @if($errors->has('address'))
                            <p class="mt-1 text-sm text-red-600 flex items-center gap-2"><i
                                    class="fa fa-exclamation-circle"></i> {{ $errors->first('address') }}</p>
                        @endif
                    </div>

                    <div class="flex flex-row gap-4">
                        <div class="flex-1">
                            <select id="editRole" name="role"
                                class="w-full border rounded-full px-4 py-2 {{ $errors->has('role') ? 'border-red-600 ring-1 ring-red-200' : 'border-gray-300' }}">
                                <option value="">Role</option>
                                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>User</option>
                            </select>
                            @if($errors->has('role'))
                                <p class="mt-1 text-sm text-red-600 flex items-center gap-2"><i
                                        class="fa fa-exclamation-circle"></i> {{ $errors->first('role') }}</p>
                            @endif
                        </div>

                        <div class="flex-1">
                            <select id="editGender" name="gender"
                                class="w-full border rounded-full px-4 py-2 {{ $errors->has('gender') ? 'border-red-600 ring-1 ring-red-200' : 'border-gray-300' }}">
                                <option value="">Gender</option>
                                <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                            </select>
                            @if($errors->has('gender'))
                                <p class="mt-1 text-sm text-red-600 flex items-center gap-2"><i
                                        class="fa fa-exclamation-circle"></i> {{ $errors->first('gender') }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="flex flex-row gap-4 justify-end">
                        <button type="button"
                            class="cursor-pointer bg-gray-300 text-gray-500 border border-gray-500 font-bold px-4 py-2 rounded-full w-50"
                            onclick="closeEditModal()">Cancel</button>
                        <button type="submit"
                            class="cursor-pointer bg-red-300 text-red-500 border border-red-500 font-bold px-4 py-2 rounded-full w-50">Update
                            User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        function openEditModal(username, fullName, email, password, birthDate, address, gender, role) {

            document.getElementById("editFullName").value = fullName;
            document.getElementById("editEmail").value = email;
            document.getElementById("editBirthDate").value = birthDate;
            document.getElementById("editAddress").value = address;
            document.getElementById("editGender").value = gender;
            document.getElementById("editRole").value = role;
            document.getElementById("editPassword").value = password;

            document.getElementById("editUserModal").classList.remove("hidden");
        }

        function closeEditModal() {
            document.getElementById("editUserModal").classList.add("hidden");
        }
    </script>
@endsection