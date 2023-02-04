<x-super-admin-layout>

    <div class="py-2 w-full">
        <div class="max-w-9xl mx-auto -mt-2 sm:px-4 lg:px-2">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-2">
                <div class="flex p-2">
                    <a href="{{ route('super-admin.admins.assign') }}" class="px-4 py-2 bg-green-500 hover:bg-green-700 rounded-md">Add Admin</a>
                </div>                
                
                <div class="flex-col lg:px-4">
                    <div class="py-4 -my-4 overflow-x-auto sm:-mx-6 sm:px-8 lg:-mx-8 lg:px-8">
                        <div class="inline-block min-w-full overflow-hidden align-middle border-b border-gray-200 shadow sm:rounded-lg">

                            <table class="min-w-full">
                                <thead>
                                    <tr>
                                        <th class="px-2 py-1 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">#</th>
                                        <th class="px-2 py-1 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                                            Name</th>
                                            <th class="px-2 py-1 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                                                Email</th>
                                            <th class="px-2 py-1 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                                                Created On</th>
                                        <th class="px-2 py-1 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                                        </th>
                                    </tr>
                                </thead>

                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($admins as $admin)
                                    <tr>
                                        <td class="px-2 py-1 whitespace-nowrap">{{$loop->iteration}}
                                        <td class="px-2 py-1 whitespace-no-wrap border-b border-gray-200">
                                            <div class="flex items-center">
                                                {{ $admin->firstname." ".$admin->lastname }}
                                            </div>
                                        </td>
                                        <td class="px-2 py-1 whitespace-no-wrap border-b border-gray-200">
                                            <div class="flex items-center">
                                                {{ $admin->email }}
                                            </div>
                                        </td>
                                        <td class="px-2 py-1 whitespace-no-wrap border-b border-gray-200">
                                            <div class="flex items-center">
                                                {{ $admin->created_at->toDateString() }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="flex justify-end p-0.5">
                                                <div class="flex space-x-2">
                                                    {{-- <a href="{{ route('super-admin.users.show', $admin->id) }}" class="px-4 py-1 bg-blue-400 hover:bg-blue-600 text-white rounded-md">Roles</a>
                                                    <a href="#" class="px-4 py-1 bg-blue-400 hover:bg-blue-600 text-white rounded-md">Permissions</a> --}}
                                                    
                                                    @role('super-admin')
                                                    <form class="px-2 py-0.5 bg-red-400 hover:bg-red-600 text-white rounded-md" method="POST" action="{{ route('super-admin.admins.roles.remove', $admin->id) }}" onsubmit="return confirm('Are you sure you want to deny user the role?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit">Revoke Role</button>
                                                    </form>
                                                    @endrole
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-super-admin-layout>

