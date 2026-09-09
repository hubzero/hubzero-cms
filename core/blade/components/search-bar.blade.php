@props([
    'action' => '',
    'query' => '',
    'placeholder' => '',
    'name' => 'search',
    'label' => '',
    'buttonLabel' => '',
    'clearUrl' => '',
    'clearLabel' => '',
])

<form method="get"
      action="{{ $action }}"
      role="search"
      class="mb-6">
    @if($buttonLabel)
        {{-- Search with submit button and optional clear --}}
        <label for="entry-search-field" class="sr-only">
            {{ $label ?: $placeholder }}
        </label>
        <div class="join w-full">
            <input type="search"
                   id="entry-search-field"
                   name="{{ $name }}"
                   class="input input-bordered join-item w-full h-12"
                   value="{{ $query }}"
                   placeholder="{{ $placeholder }}" />
            <button type="submit" class="btn btn-primary join-item h-12">
                {{ $buttonLabel }}
            </button>
            @if($clearUrl && $query)
                <a class="btn btn-outline join-item h-12"
                   href="{{ $clearUrl }}">
                    {{ $clearLabel ?: \Hubzero\Facades\Lang::txt('JCLEAR') }}
                </a>
            @endif
        </div>
    @else
        {{-- Simple auto-submit search input --}}
        <label for="entry-search-field" class="sr-only">
            {{ $label ?: $placeholder }}
        </label>
        <input type="search"
               id="entry-search-field"
               name="{{ $name }}"
               class="input input-bordered w-full"
               value="{{ $query }}"
               placeholder="{{ $placeholder }}" />
    @endif
    {{ $slot }}
</form>
