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
                            onsubmit="return confirm('Are you sure?');">
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
                </div>
            </div>
        </div>
    </div>
</x-super-admin-layout>