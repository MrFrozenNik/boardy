<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Лента</h2>
            @auth
                <a href="{{ route('posts.create') }}"
                   class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                    Новый пост
                </a>
            @endauth
        </div>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto sm:px-6 lg:px-8">
        @if (session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
                {{ session('success') }}
            </div>
        @endif

        @forelse ($posts as $post)
            <article class="bg-white shadow rounded-lg p-6 mb-4">
                <h3 class="text-xl font-bold mb-2">
                    <a href="{{ route('posts.show', $post) }}" class="hover:underline">
                        {{ $post->title }}
                    </a>
                </h3>
                <p class="text-gray-700 mb-2">{{ Str::limit($post->body, 200) }}</p>
                <small class="text-gray-500">
                    {{ $post->author->name }} · {{ $post->created_at->diffForHumans() }} · {{ $post->created_at->isoFormat('D MMMM YYYY') }}
                </small>
            </article>
        @empty
            <p class="text-gray-500">Постов пока нет.</p>
        @endforelse

        <div class="mt-4">
            {{ $posts->links() }}
        </div>
    </div>
</x-app-layout>
