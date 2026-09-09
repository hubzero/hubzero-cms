{{--
  Citations — import step 3: results of saved citations.

  Variables from controller (savedTask):
    $title         — Page title string
    $citations     — Array of saved Citation models
    $config        — Component configuration (Registry)
    $defaultFormat — Default citation Format model
    $openurl       — OpenURL resolver data (disabled)
    $messages      — Array of notification messages

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Document;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Pathway;
  use Hubzero\Facades\Request;
  use Hubzero\Facades\Route;

  if (Pathway::count() <= 0) {
      Pathway::append(
          Lang::txt('COM_CITATIONS'),
          'index.php?option=' . $option
      );
  }
  Pathway::append(
      Lang::txt('COM_CITATIONS_IMPORT'),
      'index.php?option=' . $option . '&task=import'
  );
  Pathway::append(
      Lang::txt('COM_CITATIONS_IMPORT_SAVED'),
      'index.php?option=' . $option . '&task=import_saved'
  );

  Document::setTitle($title);

  $label = $config->get('citation_label', 'type');
  $browseUrl = Route::url('index.php?option=' . $option . '&task=browse', false);
  $importUrl = Route::url('index.php?option=' . $option . '&task=import', false);
@endphp

<x-page-container :title="Lang::txt('COM_CITATIONS_IMPORT_SAVED')">
  @slot('actions')
    <a class="btn"
       href="{{ $browseUrl }}">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
           stroke-width="1.5" stroke="currentColor" class="w-4 h-4" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
      </svg>
      {{ Lang::txt('COM_CITATIONS_BROWSE') }}
    </a>
    <a class="btn"
       href="{{ $importUrl }}">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
           stroke-width="1.5" stroke="currentColor" class="w-4 h-4" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" />
      </svg>
      {{ Lang::txt('COM_CITATIONS_IMPORT_IMPORT_MORE') }}
    </a>
  @endslot

  <x-alert-list :notifications="$messages" />

  <x-step-nav :steps="[
      Lang::txt('COM_CITATIONS_IMPORT_STEP1_NAME'),
      Lang::txt('COM_CITATIONS_IMPORT_STEP2_NAME'),
      Lang::txt('COM_CITATIONS_IMPORT_STEP3_NAME'),
  ]" :current="2" />

  @if(count($citations) > 0)
    <h2 class="text-lg font-semibold mb-4">
      {{ Lang::txt('COM_CITATIONS_IMPORT_SUCCESS') }}
    </h2>

    <ul class="list bg-base-100 rounded-box shadow-sm"
        aria-label="{{ Lang::txt('COM_CITATIONS_IMPORT_SUCCESS') }}">
      @php $counter = 1; @endphp
      @foreach($citations as $cite)
        @php
          $typeName = $cite->relatedType()->row()->get('type_title', 'Generic');
          $viewUrl = Route::url(
              'index.php?option=' . $option . '&task=view&id=' . $cite->id,
              false
          );
        @endphp
        <li class="list-row">
          @if($label != 'none')
            <div class="text-base-content/40 text-sm tabular-nums w-8 text-right">
              @if($label == 'number' || $label == 'both')
                {{ $counter }}.
              @endif
            </div>
          @endif
          <div class="list-col-grow">
            <a class="link link-hover text-primary font-semibold"
               href="{{ $viewUrl }}">
              {{ $cite->title }}
            </a>
            <div class="text-sm text-base-content/60 mt-1">
              @if($cite->author)
                <span>{{ Illuminate\Support\Str::limit($cite->author, 100) }}</span>
              @endif
              @if($cite->year)
                <span class="ml-1">({{ $cite->year }})</span>
              @endif
              @if($cite->journal)
                <span class="ml-1 italic">{{ $cite->journal }}</span>
              @endif
            </div>
            @if($cite->doi)
              <div class="text-xs text-base-content/40 mt-0.5">
                DOI:
                <a class="link link-hover"
                   href="https://doi.org/{{ $cite->doi }}"
                   rel="external">{{ $cite->doi }}</a>
              </div>
            @endif
            @if($cite->abstract)
              <p class="text-sm text-base-content/50 mt-2 line-clamp-2">
                {{ $cite->abstract }}
              </p>
            @endif
          </div>
          <div class="flex flex-col items-end gap-1">
            @if($label == 'type' || $label == 'both')
              <span class="badge badge-sm badge-ghost">{{ $typeName }}</span>
            @endif
          </div>
        </li>
        @php $counter++; @endphp
      @endforeach
    </ul>
  @else
    <x-empty-state
      :title="Lang::txt('COM_CITATIONS_NO_CITATIONS_FOUND')"
      message=""
    >
      <a class="btn btn-ghost" href="{{ $importUrl }}">
        {{ Lang::txt('COM_CITATIONS_IMPORT_IMPORT_MORE') }}
      </a>
    </x-empty-state>
  @endif

</x-page-container>
