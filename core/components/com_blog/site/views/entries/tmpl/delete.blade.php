{{--
  Blog entry delete confirmation.

  Variables from controller (deleteTask):
    $entry    — Entry model instance
    $archive  — Archive model instance
    $config   — Component params (Registry)

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Document;
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Pathway;
  use Hubzero\Facades\Route;

  // Breadcrumbs
  if (Pathway::count() <= 0) {
      Pathway::append(
          Lang::txt('COM_BLOG'),
          'index.php?option=' . $option
      );
  }
  Pathway::append(
      Lang::txt('COM_BLOG_DELETE'),
      $entry->link('delete')
  );

  Document::setTitle(Lang::txt('COM_BLOG') . ': ' . Lang::txt('JACTION_DELETE'));

  $__view->css();
  $__view->js();

  $deleteUrl  = Route::url($entry->link('delete'), false);
  $cancelUrl  = Route::url($entry->link(), false);
  $archiveUrl = Route::url('index.php?option=' . $option, false);

  $entryTitle = $entry->get('title');
@endphp

{{-- Page header --}}
<x-page-header :title="Lang::txt('COM_BLOG') . ': ' . Lang::txt('JACTION_DELETE')">
    <a class="btn" href="{{ $archiveUrl }}">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
           stroke-width="1.5" stroke="currentColor" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75m-8.69-6.44-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z" />
      </svg>
      {{ Lang::txt('COM_BLOG_ARCHIVE') }}
    </a>
</x-page-header>

<section class="confirm-delete" role="alertdialog"
         aria-labelledby="confirm-title" aria-describedby="confirm-desc">
  <div class="confirm-card">

    <div class="confirm-icon" aria-hidden="true">
      <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64"
           fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
      </svg>
    </div>

    <h2 class="confirm-title" id="confirm-title">
      Delete "{{ $entryTitle }}"?
    </h2>

    <p class="confirm-desc" id="confirm-desc">
      This will permanently delete the blog entry and all associated
      comments. This action cannot be undone.
    </p>

    <div class="confirm-impact">
      <h3>This will remove:</h3>
      <ul>
        <li>The blog entry "{{ $entryTitle }}"</li>
        <li>All associated comments</li>
        <li>All file attachments</li>
      </ul>
    </div>

    @if($__view->getError())
      <div class="alert alert-error" role="alert">
        {{ $__view->getError() }}
      </div>
    @endif

    <form method="post" action="{{ $deleteUrl }}">
      <div class="confirm-actions">
        <button class="btn btn-danger" type="submit">Delete Entry</button>
        <a class="btn btn-ghost" href="{{ $cancelUrl }}">Cancel</a>
      </div>

      <input type="hidden" name="id" value="{{ $entry->get('id') }}" />
      <input type="hidden" name="task" value="delete" />
      <input type="hidden" name="process" value="1" />
      <input type="hidden" name="confirmdel" value="1" />
      <input type="hidden" name="option" value="{{ $option }}" />
      {!! Html::input('token') !!}
    </form>

  </div>
</section>
