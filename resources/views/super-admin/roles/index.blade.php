<x-super-admin-layout>

    <div class="py-2 w-full">
        <div class="max-w-9xl mx-auto -mt-2 sm:px-4 lg:px-2">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-2">
                <div class="flex p-2">
                    <a href="{{ route('super-admin.roles.create') }}" class="px-4 py-2 bg-green-500 hover:bg-green-700 rounded-md">Create Role</a>
                </div>
                {{-- <div class="justify-end p-1">
                    @include('partials._rolesearch')
                </div> --}}
                <div class="flex flex-col">
                    <div class="py-2 -my-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
                        <div class="inline-block min-w-full overflow-hidden align-middle border-b border-gray-200 shadow sm:rounded-lg">
                            <table class="min-w-full">
                                <thead>
                                    <tr>
                                        <th class="px-2 py-1 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">#</th>
                                        <th class="px-2 py-1 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                                            Name</th>
                                        <th class="px-2 py-1 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                                        </th>
                                    </tr>
                                </thead>

                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($roles as $role)
                                    <tr>
                                        <td class="px-2 py-0.5 whitespace-nowrap">{{$loop->iteration}}
                                        <td class="px-2 py-0.5 whitespace-no-wrap border-b border-gray-200">
                                            <div class="flex items-center">
                                                {{ $role->name }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="flex justify-start p-1">
                                                <div class="flex space-x-2">
                                                    <a href="{{ route('super-admin.roles.edit', $role->id) }}" class="px-2 py-0.5 bg-blue-400 hover:bg-blue-600 text-white rounded-md">Edit</a>
                                                    
                                                    <form class="px-2 py-0.5 bg-red-400 hover:bg-red-600 text-white rounded-md" method="POST" action="{{ route('super-admin.roles.destroy', $role->id) }}" onsubmit="return confirm('Are you sure?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit">Delete</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach

                                </tbody>
                            </table>                            
                        </div>
                        {{-- <div class="mt-6 px-4 p-4">{{ $roles->links() }}</div> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-super-admin-layout>

