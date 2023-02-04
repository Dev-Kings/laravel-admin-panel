<x-super-admin-layout>

    <div class="py-12 w-full">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-2">
                <div class="flex p-2">
                    <a href="{{ route('super-admin.roles.index') }}" class="px-4 py-2 bg-green-500 hover:bg-green-700 text-slate-50 rounded-md">Roles Homepage</a>
                </div>
                <div class="flex flex-col p-2 bg-slate-100">
                    <div class="space-y-8 divide-y divide-gray-200 w-1/4 mt-10">
                        <form method="POST" action="{{ route('super-admin.roles.update', $role->id) }}">
                            @csrf
                            @method('PUT')
                            <div class="sm:col-span-6">
                                <label for="name" class="block text-sm font-medium text-gray-700"> Role Name </label>
                                <div class="mt-1">
                                    <input type="text" id="name" name="name" class="block w-full appearance-none bg-white border border-gray-400 rounded-md py-2 px-3 text-base leading-normal transition duration-150 ease-in-out sm:text-sm sm:leading-5" value="{{ $role->name }}" />
                                </div>
                                @error('name')
                                <span class="text-red-400 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="sm:col-span-6 pt-5">
                                <button type="submit" class="px-4 py-2 bg-green-400 hover:bg-green-600 rounded-md">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="mt-6 p-2 bg-slate-100">
                    <h2 class="text-2xl font-semibold">Role Permissions</h2>
                    <div class="space-x-2 mt-4 p-2">
                        <div class="grid grid-cols-8 gap-4">
                        @if($role->permissions)
                        @foreach($role->permissions as $role_permission)
                        <form class="text-center bg-cyan-600 hover:bg-red-600 text-white rounded-md" method="POST" action="{{ route('super-admin.roles.permissions.revoke', [$role->id, $role_permission->id]) }}" onsubmit="return confirm('Are you sure you want to revoke?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit">{{ $role_permission->name }}</button>
                        </form>
                        @endforeach

                        @endif
                        </div>
                    </div>
                    <div class="max-w-xl mt-6 w-1/4">
                        <form method="POST" action="{{ route('super-admin.roles.permissions', $role->id) }}">
                            @csrf
                            <div class="sm:col-span-6">
                                <label for="permission" class="block text-sm font-medium text-gray-700">Permission</label>
                                <select id="permission" required name="permission" autocomplete="permission-name" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="" hidden>Select a permission</option>
                                    @foreach($permissions as $permission)
                                    <option value="{{ $permission->name }}">{{ $permission->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('name')
                            <span class="text-red-400 text-sm">{{ $message }}</span>
                            @enderror
                    </div>
                    <div class="sm:col-span-6 pt-5">
                        <button type="submit" class="px-4 py-2 bg-green-400 hover:bg-green-600 rounded-md">Assign</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>
</x-super-admin-layout>

