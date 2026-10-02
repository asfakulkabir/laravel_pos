<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                {{ __('User Management') }}
            </h2>
            <a href="{{ route('users.create') }}"
                class="inline-flex items-center px-4 py-2 text-sm font-semibold text-white bg-teal-600 rounded-lg hover:bg-teal-700 shadow">
                + Add Moderator
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-50 border-l-4 border-green-500 text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 text-red-700">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-slate-200">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-slate-500">
                        <thead class="text-xs text-slate-700 uppercase bg-slate-50">
                            <tr>
                                <th class="px-6 py-4 font-bold">Name</th>
                                <th class="px-6 py-4 font-bold">Email</th>
                                <th class="px-6 py-4 font-bold">Role</th>
                                <th class="px-6 py-4 font-bold text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($users as $user)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4 font-medium text-slate-900 border-b border-slate-100">
                                        {{ $user->name }}
                                        @if ($user->id === auth()->id())
                                            <span class="ml-2 px-2 py-0.5 text-xs bg-slate-100 text-slate-600 rounded-full">You</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-slate-600 border-b border-slate-100">{{ $user->email }}</td>
                                    <td class="px-6 py-4 border-b border-slate-100">
                                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full 
                                            {{ $user->role === 'admin' ? 'bg-indigo-50 text-indigo-700' : 'bg-teal-50 text-teal-700' }}">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right border-b border-slate-100">
                                        <div class="flex items-center justify-end space-x-2">
                                            <a href="{{ route('users.edit', $user) }}" class="text-teal-600 hover:text-teal-800 font-medium">Edit</a>
                                            @if ($user->id !== auth()->id())
                                                <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-800 font-medium">Delete</button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
