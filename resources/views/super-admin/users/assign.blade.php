<x-super-admin-layout>

    <div class="py-12 w-full">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-2">
                <div class="flex p-2">
                    <a href="{{ route('super-admin.users.data') }}"
                        class="px-4 py-2 bg-green-500 hover:bg-green-700 text-slate-50 rounded-md">All Users</a>
                </div>
                <div class="flex flex-col p-2 bg-slate-100">
                    <div>Name : {{ $user->firstname." ".$user->lastname }}</div>
                    <div>Email : {{ $user->email }}</div>
                </div>
                <div class="mt-6 p-2 bg-slate-100">
                    <h2 class="text-2xl font-semibold">Roles</h2>
                    <div class="flex space-x-2 mt-4 p-2">
                        @if($user->roles)
                        @foreach($user->roles as $user_role)
                        <form class="px-4 py-2 bg-red-400 hover:bg-red-600 text-white rounded-md" method="POST"
                            action="{{ route('super-admin.users.roles.remove', [$user->id, $user_role->id]) }}"
                            onsubmit="return confirm(`Are you sure you want to revoke the role from {{ $user->firstname.' '.$user->lastname }}?`);">
                            @csrf
                            @method('DELETE')
                            @if($user_role->name == 'super-admin' || $user_role->name == 'admin')
                            <button type="submit">{{ $user_role->name }}</button>
                            @else
                            <button disabled type="submit">{{ $user_role->name }}</button>
                            @endif
                        </form>
                        @endforeach

                        @endif
                    </div>
                    <div class="max-w-xl mt-6">
                        <form method="POST" action="{{ route('super-admin.users.roles', $user->id) }}">
                            @csrf
                            <div class="sm:col-span-4">
                                <label for="role" class="block text-sm font-medium text-gray-700">Roles</label>
                                <select required id="role" name="role" autocomplete="role-name"
                                    class="mt-1 block w-1/2 py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="" hidden>Select a role</option>
                                    @foreach($roles as $role)
                                    <option value="{{ $role->name }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('role')
                            <span class="text-red-400 text-sm">{{ $message }}</span>
                            @enderror
                    </div>
                    <div class="sm:col-span-6 pt-5">
                        <button type="submit"
                            class="px-4 py-2 bg-green-400 hover:bg-green-600 rounded-md">Assign</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-super-admin-layout>