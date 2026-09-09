{{--
  Citations — author list for AJAX loading in edit form.

  Variables from controller (displayTask):
    $row — Citation model

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\Session;

  $authors = $row->relatedAuthors;
@endphp

@if(count($authors))
  @foreach($authors as $author)
    <p class="citation-author flex items-center gap-2 py-1"
       id="author_{{ $author->id }}">
      <span class="author-handle cursor-move text-base-content/30">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
             stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
          <path stroke-linecap="round" stroke-linejoin="round"
                d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
        </svg>
      </span>
      <span class="author-name flex-1">
        {{ $author->author }}
      </span>
      <span class="author-description">
        @php
          $deleteUrl = Route::url(
              'index.php?option=com_citations&controller=authors'
              . '&task=remove&citation=' . $row->id
              . '&author=' . $author->id
              . '&' . Session::getFormToken() . '=1'
          );
        @endphp
        <a class="btn btn-ghost btn-xs text-error"
           data-id="{{ $author->id }}"
           href="{{ $deleteUrl }}">
          {{ Lang::txt('JACTION_DELETE') }}
        </a>
      </span>
    </p>
  @endforeach
@endif
