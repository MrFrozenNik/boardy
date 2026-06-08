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

        <div id="posts-feed">
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
        </div>

        <div class="mt-4">
            {{ $posts->links() }}
        </div>
    </div>

    <script>
        console.log('Скрипт успешно загружен на страницу!');

	const wsUrl = (location.protocol === 'https:' ? 'wss://' : 'ws://') + location.host + '/ws'
        console.log('Попытка подключиться к:', wsUrl);

        function connect() {
            const ws = new WebSocket(wsUrl);

            ws.onopen = () => console.log('✓ WS connected');

            ws.onmessage = (e) => {
                const msg = JSON.parse(e.data);
                if (msg.type === 'new_post') prependPost(msg.post);
            };

            ws.onclose = () => {
                console.log('WS closed, reconnecting in 3s...');
                setTimeout(connect, 3000);
            };

            ws.onerror = (err) => console.error('WS error:', err);
        }

        function prependPost(post) {
            const feed = document.getElementById('posts-feed');
            if (!feed) return;
            const el = document.createElement('article');
            el.className = 'bg-white shadow rounded-lg p-6 mb-4';
            el.innerHTML = `
                <h3 class="text-xl font-bold mb-2">
                    <span class="hover:underline">${escapeHtml(post.title)}</span>
                </h3>
                <p class="text-gray-700 mb-2">${escapeHtml(post.body)}</p>
                <small class="text-gray-500">${escapeHtml(post.author || 'Аноним')}</small>
            `;
            feed.prepend(el);
        }

        function escapeHtml(str) {
            const d = document.createElement('div');
            d.textContent = str;
            return d.innerHTML;
        }

        connect();
    </script>
</x-app-layout>
