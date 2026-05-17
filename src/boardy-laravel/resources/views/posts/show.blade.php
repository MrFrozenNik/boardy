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

        <h3 class="text-lg font-semibold mb-3">
            Комментарии ({{ $post->comments->count() }})
        </h3>

        @forelse ($post->comments as $comment)
            <div class="bg-white shadow rounded-lg p-4 mb-2">
                <p class="text-gray-800 mb-1">{{ $comment->body }}</p>
                <small class="text-gray-500">
                    {{ $comment->author->name }} · {{ $comment->created_at->diffForHumans() }}
                </small>
            </div>
        @empty
            <p class="text-gray-500">Комментариев пока нет.</p>
        @endforelse

        @auth
            <form action="{{ route('comments.store') }}" method="POST" class="mt-4">
                @csrf
                <input type="hidden" name="post_id" value="{{ $post->id }}">
                <textarea name="body" rows="3" required
                          class="w-full border-gray-300 rounded-md shadow-sm"
                          placeholder="Ваш комментарий">{{ old('body') }}</textarea>
                @error('body')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
                <button class="mt-2 px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                    Отправить
                </button>
            </form>
        @else
            <p class="text-gray-500 mt-4">
                <a href="{{ route('login') }}" class="text-indigo-600 hover:underline">Войдите</a>,
                чтобы оставить комментарий.
            </p>
        @endauth
    </div>
</x-app-layout>
