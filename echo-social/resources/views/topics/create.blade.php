<x-app-layout>
    <div class="max-w-2xl mx-auto">

        <div class="echo-card">
            <h1 class="text-xl font-bold text-white mb-1">Nuovo Topic del Giorno</h1>
            <p class="text-gray-500 text-sm mb-6">Imposta il topic di oggi per la community</p>

            <form method="POST" action="{{ route('topics.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1.5">
                        Titolo
                    </label>
                    <input type="text" name="title" value="{{ old('title') }}"
                           class="echo-input" placeholder="Topic di oggi..." required>
                    @error('title')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1.5">
                        Descrizione
                    </label>
                    <textarea name="description" rows="3"
                              class="echo-input resize-none"
                              placeholder="Descrivi il topic...">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('posts.index') }}" class="echo-btn-ghost">
                        Annulla
                    </a>
                    <button type="submit" class="echo-btn">
                        Pubblica Topic
                    </button>
                </div>
            </form>
        </div>

    </div>
</x-app-layout>