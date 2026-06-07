<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $post->title }}
        </h2>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto sm:px-6 lg:px-8">
        @if (session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
                {{ session('success') }}
            </div>
        @endif

        <article class="bg-white shadow rounded-lg p-6 mb-6">
            <p class="text-sm text-gray-500 mb-3">
                {{ $post->author->name }} · {{ $post->created_at->diffForHumans() }}
            </p>
            <div class="text-gray-800 whitespace-pre-line">{{ $post->body }}</div>

            <div class="mt-4 flex gap-2">
                @can('update', $post)
                    <a href="{{ route('posts.edit', $post) }}"
                       class="px-3 py-1 text-sm border border-gray-300 rounded hover:bg-gray-50">
                        Редактировать
                    </a>
                @endcan
                @can('delete', $post)
                    <form action="{{ route('posts.destroy', $post) }}" method="POST"
                          onsubmit="return confirm('Удалить пост?')">
                        @csrf
                        @method('DELETE')
                        <button class="px-3 py-1 text-sm border border-red-300 text-red-600 rounded hover:bg-red-50">
                            Удалить
                        </button>
                    </form>
                @endcan
            </div>
        </article>

        <h3 class="text-lg font-semibold mb-3">Комментарии</h3>

        <div id="comments-app"
             data-post-id="{{ $post->id }}"
             data-user-name="{{ auth()->user()?->name }}"></div>
    </div>
</x-app-layout>
