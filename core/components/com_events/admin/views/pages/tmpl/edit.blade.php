{{--
  Event Page — Admin edit/create form

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\Toolbar;

  $text = ($task == 'edit') ? Lang::txt('COM_EVENTS_EDIT') : Lang::txt('COM_EVENTS_NEW');

  Toolbar::title(Lang::txt('COM_EVENTS_PAGE') . ': ' . $text, 'event');
  Toolbar::save();
  Toolbar::cancel();

  $eventEditUrl = Route::url(
      'index.php?option=' . $option . '&task=edit&id=' . $event->id,
      false, false
  );
@endphp

<x-admin-edit
    option="{{ $option }}"
    controller="{{ $controller }}"
>
  {{-- Main content column --}}
  <x-admin-fieldset legend="{{ Lang::txt('COM_EVENTS_PAGE') }}">

      <div class="admin-field">
        <a href="{{ $eventEditUrl }}"
           class="link link-hover text-primary font-medium">
          {{ $event->title }}
        </a>
      </div>

      <div class="admin-field">
        <label for="field-title" class="label">
          {{ Lang::txt('COM_EVENTS_TITLE') }}
          <span class="text-error">*</span>
        </label>
        <input type="text"
               name="fields[title]"
               id="field-title"
               class="input input-bordered w-full required"
               required
               value="{{ $page->title ?? '' }}" />
      </div>

      <div class="admin-field">
        <label for="field-alias" class="label">
          {{ Lang::txt('COM_EVENTS_ALIAS') }}
        </label>
        <input type="text"
               name="fields[alias]"
               id="field-alias"
               class="input input-bordered w-full"
               value="{{ $page->alias ?? '' }}" />
        <p class="text-xs text-muted-foreground mt-1">
          {{ Lang::txt('COM_EVENTS_ALIAS_HINT') }}
        </p>
      </div>

      <div class="admin-field">
        <label for="field-pagetext" class="label">
          {{ Lang::txt('COM_EVENTS_PAGE_TEXT') }}
          <span class="text-error">*</span>
        </label>
        {!! $__view->editor(
            'fields[pagetext]',
            e($page->pagetext ?? ''),
            40, 20,
            'field-pagetext',
            ['class' => 'required']
        ) !!}
      </div>

  </x-admin-fieldset>

  @slot('sidebar')
    <x-admin-fieldset legend="{{ Lang::txt('JDETAILS') }}">
        <table class="admin-meta">
          <tbody>
            <tr>
              <td>{{ Lang::txt('COM_EVENTS_PAGE_ORDERING') }}</td>
              <td>{{ $page->ordering }}</td>
            </tr>
            <tr>
              <td>{{ Lang::txt('COM_EVENTS_PAGE_CREATED') }}</td>
              <td>{{ $page->created ?? '—' }}</td>
            </tr>
            <tr>
              <td>{{ Lang::txt('COM_EVENTS_PAGE_CREATED_BY') }}</td>
              <td>{{ $page->created_by ?? '—' }}</td>
            </tr>
            <tr>
              <td>{{ Lang::txt('COM_EVENTS_PAGE_LAST_MODIFIED') }}</td>
              <td>{{ $page->modified ?? '—' }}</td>
            </tr>
            <tr>
              <td>{{ Lang::txt('COM_EVENTS_PAGE_LAST_MODIFIED_BY') }}</td>
              <td>{{ $page->modified_by ?? '—' }}</td>
            </tr>
          </tbody>
        </table>
    </x-admin-fieldset>
  @endslot

  {{-- Hidden fields --}}
  <input type="hidden" name="event_id" value="{{ $event->id }}" />
  <input type="hidden" name="fields[id]" value="{{ $page->id }}" />
  <input type="hidden" name="id" value="{{ $page->id }}" />
</x-admin-edit>
