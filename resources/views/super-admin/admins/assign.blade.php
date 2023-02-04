<x-super-admin-layout>

    <div class="py-2 w-full">
        <div class="max-w-9xl mx-auto -mt-2 sm:px-4 lg:px-2">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-2">
                <div class="justify-end p-2">
                    <a href="{{ route('super-admin.admins.index') }}" class="px-4 py-2 bg-green-500 hover:bg-green-700 rounded-md"><< All Admins</a>
                </div>

                <div class="flex flex-col">
                    <div class="py-4 -my-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
                        <div class="inline-block min-w-full overflow-hidden align-middle border-b border-gray-200 shadow sm:rounded-lg">
                            <table class="min-w-full">
                                <thead>
                                    <tr>
                                        <th class="px-2 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">#</th>
                                        <th class="px-2 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                                            Name</th>
                                            <th class="px-2 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                                                Email</th>
                                        <th class="px-2 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                                        </th>
                                    </tr>
                                </thead>
                                
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($users as $user)
                                    <tr>
                                        <td class="px-2 py-0.5 whitespace-nowrap">{{$loop->iteration}}
                                        <td class="px-2 py-0.5 whitespace-no-wrap border-b border-gray-200">
                                            <div class="flex items-center">
                                                {{ $user->firstname." ".$user->lastname }}
                                            </div>
                                        </td>
                                        <td class="px-2 py-0.5 whitespace-no-wrap border-b border-gray-200">
                                            <div class="flex items-center">
                                                {{ $user->email }}
                                            </div>
                                        </td>

                                        <td>
                                            <div class="flex justify-end px-0.1">
                                                <div class="flex space-x-2">
                                                    @role('super-admin')
                                                    <form class="px-4 py-0.1 bg-pink-400 hover:bg-cyan-600 text-white rounded-md" method="POST" action="{{ route('super-admin.users.role', $user->id) }}" onsubmit="return confirm('Are you sure you want to assign role Admin to user?');">
                                                        @csrf
                                                        <button type="submit">Assign Role</button>
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
                        <div class="mt-6 px-4 p-4">{{ $users->links() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-super-admin-layout>

