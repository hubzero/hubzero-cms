{{--
  Windows Tools edit — content editing form.

  Variables (from plugin):
    $base     — string: base URL
    $option   — string: component option
    $resource — object: resource model
    $page     — object: page content object
    $name     — string: plugin name (set as 'name' by plugin)

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;

  $__view->css();
@endphp

<div class="pages-wrap">
  <div class="pages-content">

    <form action="{{ Route::url($base) }}"
          method="post"
          id="hubForm"
          class="full"
          enctype="multipart/form-data">
      <fieldset>
        <legend>{{ Lang::txt('PLG_RESOURCES_WINDOWSTOOLS_EDIT_PAGE') }}</legend>

        <label for="field_content">
          {{ Lang::txt('PLG_RESOURCES_WINDOWSTOOLS_FIELD_CONTENT') }}
          <span class="required">{{ Lang::txt('JREQUIRED') }}</span>
          @php
            echo $__view->editor(
                'fields[content]',
                e($page->get('content')),
                35,
                50,
                'field_content'
            );
          @endphp
        </label>

        <p class="submit">
          <input class="btn btn-success" type="submit"
                 value="{{ Lang::txt('PLG_RESOURCES_WINDOWSTOOLS_SAVE') }}" />
          <a class="btn btn-secondary" href="{{ Route::url($base) }}">
            {{ Lang::txt('JCANCEL') }}
          </a>
        </p>
      </fieldset>

      <input type="hidden" name="fields[plugin]" value="{{ $name }}" />
      <input type="hidden" name="fields[title]" value="{{ $page->get('title') }}" />
      <input type="hidden" name="fields[alias]" value="{{ $page->get('alias') }}" />
      <input type="hidden" name="fields[state]" value="{{ $page->get('state') }}" />
      <input type="hidden" name="fields[access]" value="{{ $page->get('access') }}" />
      <input type="hidden" name="fields[id]" value="{{ $page->get('id') }}" />

      {!! Html::input('token') !!}

      <input type="hidden" name="option" value="{{ $option }}" />
      <input type="hidden" name="id" value="{{ $resource->id }}" />
      <input type="hidden" name="active" value="{{ $name }}" />
      <input type="hidden" name="action" value="save" />
    </form>

  </div>
</div>
