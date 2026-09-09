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

<x-confirm-dialog
    :title="Lang::txt('COM_BLOG_DELETE_TITLE', $entryTitle)"
    :description="Lang::txt('COM_BLOG_DELETE_DESCRIPTION')"
    :action="$deleteUrl"
    :confirmLabel="Lang::txt('COM_BLOG_DELETE_HEADER')"
    :cancelUrl="$cancelUrl"
    :error="$__view->getError()">

    <h3>{{ Lang::txt('COM_BLOG_DELETE_IMPACT_HEADING') }}</h3>
    <ul>
      <li>{{ Lang::txt('COM_BLOG_DELETE_IMPACT_ENTRY', $entryTitle) }}</li>
      <li>{{ Lang::txt('COM_BLOG_DELETE_IMPACT_COMMENTS') }}</li>
      <li>{{ Lang::txt('COM_BLOG_DELETE_IMPACT_ATTACHMENTS') }}</li>
    </ul>

    <x-slot name="hiddenFields">
      <input type="hidden" name="id" value="{{ $entry->get('id') }}" />
      <input type="hidden" name="task" value="delete" />
      <input type="hidden" name="process" value="1" />
      <input type="hidden" name="confirmdel" value="1" />
      <input type="hidden" name="option" value="{{ $option }}" />
      {!! Html::input('token') !!}
    </x-slot>
</x-confirm-dialog>
