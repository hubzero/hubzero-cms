{{--
  Publications — Edit content (attachment titles/bundle name)

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

  $tmpl = Request::getCmd('tmpl', '');

  if ($tmpl != 'component') {
      Toolbar::title(
          Lang::txt('COM_PUBLICATIONS') . ': '
          . Lang::txt('COM_PUBLICATIONS_EDIT_CONTENT_FOR_PUB')
          . ' #' . $pub->get('id')
          . ' (v.' . $pub->get('version_label') . ')',
          'publications'
      );
      Toolbar::save('savecontent');
      Toolbar::cancel();
  }

  $mgrUrl  = Route::url('index.php?option=' . $option . '&controller=' . $controller, false);
  $editUrl = Route::url(
      'index.php?option=' . $option
      . '&controller=' . $controller
      . '&task=edit&id[]=' . $pub->get('id')
      . '&version=' . $pub->get('version_number'), false
  );
  $formUrl = Route::url('index.php?option=' . $option . '&controller=' . $controller, false);
@endphp

@foreach ($__view->getErrors() as $error)
  <p class="alert alert-error">{{ $error }}</p>
@endforeach

{{-- Breadcrumb --}}
@if($tmpl != 'component')
  <nav class="text-sm breadcrumbs mb-4">
    <ul>
      <li><a href="{!! $mgrUrl !!}">{{ Lang::txt('COM_PUBLICATIONS_PUBLICATION_MANAGER') }}</a></li>
      <li><a href="{!! $editUrl !!}">{{ Lang::txt('COM_PUBLICATIONS_PUBLICATION') }} #{{ $pub->get('id') }}</a></li>
      <li>{{ Lang::txt('COM_PUBLICATIONS_EDIT_CONTENT_INFO') }}</li>
    </ul>
  </nav>
@endif

<form action="{!! $formUrl !!}"
      method="post"
      name="adminForm"
      id="item-form">

  @if($tmpl == 'component')
    <div class="flex gap-2 mb-4">
      @if(!$__view->getErrors())
        <button type="button" data-submit-task="savecontent" class="btn btn-sm btn-primary">
          {{ Lang::txt('JSAVE') }}
        </button>
      @endif
      <button type="button"
              class="btn btn-sm btn-ghost"
              data-parent-callback="postMessage"
              data-callback-args='["admin-popup-close","*"]'>
        {{ Lang::txt('Cancel') }}
      </button>
    </div>
  @endif

  <div class="grid grid-cols-1 lg:grid-cols-[1fr_20rem] gap-6">
    <div>
      <x-admin-fieldset legend="{{ Lang::txt('COM_PUBLICATIONS_EDIT_CONTENT_INFO') }}">

        <input type="hidden" name="option" value="{{ $option }}" />
        <input type="hidden" name="controller" value="{{ $controller }}" />
        <input type="hidden" name="no_html" value="{{ $tmpl == 'component' ? '1' : '0' }}" />
        <input type="hidden" name="id" value="{{ $pub->get('id') }}" />
        <input type="hidden" name="task" value="savecontent" />
        <input type="hidden" name="version" value="{{ $pub->get('version_number') }}" />

        @if(!$__view->getErrors())
          @php
            $manifest   = $element;
            $element    = $manifest->element;
            $elName     = 'element' . $elementId;
            $defaultTitle = $element->params->title
                ? str_replace('{pubtitle}', $pub->title,
                    str_replace('{pubversion}', $pub->version_label, $element->params->title))
                : null;
            $attachments = $pub->_attachments;
            $attachments = isset($attachments['elements'][$elementId])
                ? $attachments['elements'][$elementId] : null;
            $bundleName  = $pub->params->get($elName . 'bundlename', $defaultTitle);
            $multiZip    = !(isset($element->params->typeParams->multiZip)
                && $element->params->typeParams->multiZip == 0);
          @endphp

          <input type="hidden" name="el" value="{{ $elementId }}" />

          @if($attachments && count($attachments) > 1 && $multiZip)
            <div class="admin-field">
              <label for="field-bundlename" class="label text-base-content">{{ Lang::txt('COM_PUBLICATIONS_FIELD_BUNDLE_NAME') }}</label>
              <input type="text"
                     name="params[{{ $elName }}bundlename]"
                     id="field-bundlename"
                     class="input input-bordered w-full"
                     maxlength="250"
                     value="{{ $bundleName ?? '' }}" />
            </div>
          @endif

          @if($attachments)
            @foreach($attachments as $attach)
              <div class="admin-field border-t border-base-300 pt-3 mt-3">
                <p class="text-sm text-muted-foreground mb-1">
                  <span class="badge badge-ghost badge-sm">{{ $attach->type }}</span>
                  {{ $attach->path }}
                </p>
                <label for="field-attach-{{ $attach->id }}" class="label text-base-content">{{ Lang::txt('COM_PUBLICATIONS_FIELD_ATTACHMENT_TITLE') }}</label>
                <input type="text"
                       name="attachments[{{ $attach->id }}][title]"
                       id="field-attach-{{ $attach->id }}"
                       class="input input-bordered w-full"
                       maxlength="250"
                       value="{{ $attach->title ?? '' }}" />
              </div>
            @endforeach
          @else
            <p class="text-muted-foreground text-sm">{{ Lang::txt('COM_PUBLICATIONS_NO_CONTENT') }}</p>
          @endif
        @endif

      </x-admin-fieldset>
    </div>

    <div>
      @if(isset($elementId) && isset($element))
      <table class="admin-meta">
        <tbody>
          <tr>
            <td>{{ Lang::txt('COM_PUBLICATIONS_ELEMENT_ID') }}</td>
            <td>{{ $elementId }}</td>
          </tr>
          <tr>
            <td>{{ Lang::txt('COM_PUBLICATIONS_ELEMENT_TYPE') }}</td>
            <td>{{ $element->params->type ?? '' }}</td>
          </tr>
          <tr>
            <td>{{ Lang::txt('COM_PUBLICATIONS_ELEMENT_ROLE') }}</td>
            <td>
              {{ ($element->params->role ?? 0) == 1
                  ? Lang::txt('COM_PUBLICATIONS_ELEMENT_ROLE_PRIMARY')
                  : Lang::txt('COM_PUBLICATIONS_ELEMENT_ROLE_SECOND') }}
            </td>
          </tr>
        </tbody>
      </table>
      @endif
    </div>
  </div>

  {!! Html::input('token') !!}
</form>
