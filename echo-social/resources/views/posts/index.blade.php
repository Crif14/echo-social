<x-app-layout>
    <div class="max-w-2xl mx-auto">

        {{-- Form nuovo post --}}
        <div class="echo-card mb-6">
            <form method="POST" action="{{ route('posts.store') }}">
                @csrf
                <textarea
                    name="body"
                    rows="3"
                    class="echo-input resize-none mb-3"
                    placeholder="Cosa stai pensando?"></textarea>
                @error('body')
                    <p class="text-red-400 text-xs mb-2">{{ $message }}</p>
                @enderror
                <div class="flex justify-end">
                    <button type="submit" class="echo-btn">
                        Pubblica
                    </button>
                </div>
            </form>
        </div>

        {{-- Lista post --}}
        @forelse ($posts as $post)
            <div class="echo-card mb-4">

                {{-- Header post --}}
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-gradient-to-br from-indigo-600
                                    to-purple-600 flex items-center justify-center
                                    text-white font-bold text-sm">
                            {{ strtoupper(substr($post->user->name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-white">
                                {{ $post->user->name }}
                            </p>
                            <p class="text-xs text-gray-500">
                                {{ $post->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>

                    {{-- Elimina (solo autore) --}}
                    @if ($post->userId === auth()->id())
                        <form method="POST" action="{{ route('posts.destroy', $post) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="text-gray-600 hover:text-red-400
                                           transition-colors text-xs">
                                Elimina
                            </button>
                        </form>
                    @endif
                </div>

                {{-- Corpo post --}}
                <p class="text-gray-200 leading-relaxed">{{ $post->body }}</p>

                {{-- Footer post --}}
                <div class="echo-divider"></div>
                <div class="flex items-center gap-4 text-sm text-gray-500">
                    <span>{{ $post->likes->count() }} like</span>
                    <span>{{ $post->comments->count() }} commenti</span>
                </div>

            </div>
        @empty
            <div class="text-center text-gray-600 py-16">
                <p class="text-lg">Nessun post ancora.</p>
                <p class="text-sm mt-1">Sii il primo a condividere qualcosa!</p>
            </div>
        @endforelse

    </div>
</x-app-layout>