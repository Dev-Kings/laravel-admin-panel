<x-admin-layout>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    @if(time() >= strtotime("12:00 PM") && time() <= strtotime("03:59 PM")) <p>Good Afternoon Admin {{
                        Auth::user()->firstname }}</p>
                        @elseif(time() >= strtotime("04:00 PM") && time() <= strtotime("11:59 PM")) <p>Good Evening
                            Admin {{ Auth::user()->firstname }}</p>
                            @else
                            <p>Good Morning Admin {{ Auth::user()->firstname }}</p>
                            @endif
                </div>

                <div class="grid grid-cols-4 gap-5">

                    <x-bladewind.card class="cursor-pointer hover:shadow-gray-300">
                        <a href="/admin/users">
                            <img src="{{ asset('image/users.svg') }}" class="w-14 h-14 rounded"><br>
                            <span class="text-center ...">Users</span><br>
                            <span class="text-center ...">
                                {{ App\Models\User::all()->count() }}
                            </span>
                        </a>
                    </x-bladewind.card>

                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
