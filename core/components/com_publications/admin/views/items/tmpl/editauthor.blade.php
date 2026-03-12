{{--
  Publications — Edit/add author form

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Request;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\Toolbar;

  $__view->css();

  $pageTitle = $author->id
      ? Lang::txt('COM_PUBLICATIONS_EDIT_AUTHOR_INFO')
      : Lang::txt('COM_PUBLICATIONS_ADD_AUTHOR');

  $tmpl = Request::getCmd('tmpl', '');

  if ($tmpl != 'component') {
      Toolbar::title(
          Lang::txt('COM_PUBLICATIONS') . ': ' . $pageTitle
          . ' ' . Lang::txt('COM_PUBLICATIONS_FOR_PUB')
          . ' #' . $pub->id . ' (v.' . $row->version_label . ')',
          'publications'
      );
      Toolbar::save('saveauthor');
      Toolbar::cancel();
  }

  // Resolve name parts
  $name      = $author->name ?: null;
  $firstname = null;
  $lastname  = null;

  if (trim((string)$name)) {
      $nameParts = explode(' ', $name);
      $lastname  = end($nameParts);
      $firstname = count($nameParts) > 1 ? $nameParts[0] : '';
  }

  $firstname = $author->firstName ? htmlspecialchars($author->firstName) : $firstname;
  $lastname  = $author->lastName  ? htmlspecialchars($author->lastName)  : $lastname;

  $editUrl   = Route::url(
      'index.php?option=' . $option
      . '&controller=' . $controller
      . '&task=edit&id[]=' . $pub->id, false
  );
  $formUrl   = Route::url('index.php?option=' . $option . '&controller=' . $controller, false);
@endphp

@foreach ($__view->getErrors() as $error)
  <p class="alert alert-error">{{ $error }}</p>
@endforeach

{{-- Breadcrumb (non-component mode) --}}
@if($tmpl != 'component')
  <nav class="text-sm breadcrumbs mb-4">
    <ul>
      <li>{{ Lang::txt('COM_PUBLICATIONS_PUBLICATION_MANAGER') }}</li>
      <li><a href="{!! $editUrl !!}">{{ Lang::txt('COM_PUBLICATIONS_PUBLICATION') }} #{{ $pub->id }}</a></li>
      <li>{{ $pageTitle }}</li>
    </ul>
  </nav>
@endif

<form action="{!! $formUrl !!}"
      method="post"
      name="adminForm"
      id="item-form">

  @if($tmpl == 'component')
    <div class="flex gap-2 mb-4">
      <button type="button" data-submit-task="addusers" class="btn btn-sm btn-primary">
        {{ Lang::txt('JSAVE') }}
      </button>
      <button type="button"
              class="btn btn-sm btn-ghost"
              data-parent-callback="postMessage"
              data-callback-args='["admin-popup-close","*"]'>
        {{ Lang::txt('Cancel') }}
      </button>
    </div>
  @endif

  <x-admin-fieldset legend="{{ $pageTitle }}">

    <input type="hidden" name="author" value="{{ $author->id }}" />
    <input type="hidden" name="option" value="{{ $option }}" />
    <input type="hidden" name="controller" value="{{ $controller }}" />
    <input type="hidden" name="no_html" value="{{ $tmpl == 'component' ? '1' : '0' }}" />
    <input type="hidden" name="task" value="saveauthor" />
    <input type="hidden" name="id" value="{{ $pub->id }}" />
    <input type="hidden" name="version" value="{{ $row->version_number }}" />

    @if(!$author->id)
      <div class="admin-field">
        <label for="field-email" class="label text-base-content">{{ Lang::txt('COM_PUBLICATIONS_FIELD_EMAIL') }}</label>
        <input type="text"
               name="email"
               id="field-email"
               class="input input-bordered w-full"
               value="" />
      </div>
    @endif

    <div class="admin-field">
      <label for="field-uid" class="label text-base-content">{{ Lang::txt('COM_PUBLICATIONS_FIELD_USER_ID') }}</label>
      @if(!$author->id || !$author->user_id)
        <input type="text"
               name="uid"
               id="field-uid"
               class="input input-bordered w-full"
               value="{{ $author->user_id ?? '' }}" />
      @else
        <input type="hidden" name="uid" value="{{ $author->user_id }}" />
        <span class="font-mono text-sm">{{ $author->user_id }}</span>
      @endif
    </div>

    <div class="admin-field">
      <label for="field-firstName" class="label text-base-content">
        {{ Lang::txt('COM_PUBLICATIONS_FIELD_AUTHOR_NAME_FIRST_AND_MIDDLE') }}
        <span class="text-error">*</span>
      </label>
      <input type="text"
             name="firstName"
             id="field-firstName"
             class="input input-bordered w-full"
             required
             value="{{ $firstname }}" />
    </div>

    <div class="admin-field">
      <label for="field-lastName" class="label text-base-content">
        {{ Lang::txt('COM_PUBLICATIONS_FIELD_AUTHOR_NAME_LAST') }}
        <span class="text-error">*</span>
      </label>
      <input type="text"
             name="lastName"
             id="field-lastName"
             class="input input-bordered w-full"
             required
             value="{{ $lastname }}" />
    </div>

    <div class="admin-field">
      <label for="field-organization" class="label text-base-content">
        {{ Lang::txt('COM_PUBLICATIONS_FIELD_AUTHOR_ORGANIZATION') }}
      </label>
      <input type="text"
             name="organization"
             id="field-organization"
             class="input input-bordered w-full"
             value="{{ $author->organization ?? '' }}" />
    </div>

    <div class="admin-field">
      <label for="field-orcid" class="label text-base-content">{{ Lang::txt('COM_PUBLICATIONS_FIELD_AUTHOR_ORCID') }}</label>
      <input type="text"
             name="orcid"
             id="field-orcid"
             class="input input-bordered w-full"
             placeholder="####-####-####-####"
             value="{{ $author->orcid ?? '' }}" />
      <p class="text-xs text-muted-foreground mt-1">
        {{ Lang::txt('COM_PUBLICATIONS_FIELD_AUTHOR_ORCID_ID_DESC') }}
      </p>
    </div>

    <div class="admin-field">
      <label for="field-credit" class="label text-base-content">{{ Lang::txt('COM_PUBLICATIONS_FIELD_AUTHOR_CREDIT') }}</label>
      <input type="text"
             name="credit"
             id="field-credit"
             class="input input-bordered w-full"
             value="{{ $author->credit ?? '' }}" />
    </div>

  </x-admin-fieldset>

  {!! Html::input('token') !!}
</form>
