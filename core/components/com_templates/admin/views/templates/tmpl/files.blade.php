{{--
  com_templates — Template file browser

  Variables: $template (Template model), $files (array: main/clo/html)

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\Toolbar;

  $canDo = \Components\Templates\Helpers\Utilities::getActions();

  Toolbar::title(Lang::txt('COM_TEMPLATES_MANAGER_VIEW_TEMPLATE'), 'thememanager');
  Toolbar::cancel('template.cancel', 'JTOOLBAR_CLOSE');
  Toolbar::spacer();
  Toolbar::help('template');

  $editBase  = 'index.php?option=com_templates&controller=source&task=edit&id=';
  $copyUrl   = Route::url(
      'index.php?option=com_templates&controller=templates'
      . '&task=copy&id=' . $template->get('extension_id'), false
  );
@endphp

{{-- Component-specific styles for file list icons --}}
@php $__view->css('templates'); @endphp

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-4">

  {{-- Left column: description + master files + copy form --}}
  <div>
    <x-admin-fieldset legend="{{ Lang::txt('COM_TEMPLATES_TEMPLATE_DESCRIPTION') }}">
      <div class="flex items-start gap-4">
        {!! \Components\Templates\Helpers\Utilities::thumb($template->element, $template->client_id) !!}
        <div>
          <h2 class="text-base font-semibold mb-1">{{ ucfirst($template->element) }}</h2>
          <p class="text-sm text-muted-foreground">
            {{ Lang::txt($template->xml->get('description')) }}
          </p>
        </div>
      </div>
    </x-admin-fieldset>

    <x-admin-fieldset legend="{{ Lang::txt('COM_TEMPLATES_TEMPLATE_MASTER_FILES') }}">
      <ul class="item-list layout">
        @foreach ($files['main'] as $key => $file)
          <li>
            @if ($canDo->get('core.edit'))
              <a href="{{ Route::url($editBase . $file->id, false) }}">
                {{ Lang::txt('Edit %s', $file->get('name')) }}
              </a>
            @else
              {{ Lang::txt('Edit %s', $file->get('name')) }}
            @endif
          </li>
        @endforeach
      </ul>
    </x-admin-fieldset>

    {{-- Copy template form --}}
    <x-admin-fieldset legend="{{ Lang::txt('COM_TEMPLATES_TEMPLATE_COPY') }}">
      <form action="{{ $copyUrl }}" method="post" name="copyForm">
        <div class="admin-field">
          <label for="new_name" class="label">
            {{ Lang::txt('COM_TEMPLATES_TEMPLATE_NEW_NAME_LABEL') }}
          </label>
          <div class="flex gap-2">
            <input type="text"
                   id="new_name"
                   name="new_name"
                   class="input input-bordered flex-1" />
            <button type="submit" class="btn btn-primary btn-sm">
              {{ Lang::txt('COM_TEMPLATES_TEMPLATE_COPY') }}
            </button>
          </div>
          <p class="text-xs text-muted-foreground mt-1">
            {{ Lang::txt('COM_TEMPLATES_TEMPLATE_NEW_NAME_DESC') }}
          </p>
        </div>
        <input type="hidden" name="task" value="" />
        {!! Html::input('token') !!}
      </form>
    </x-admin-fieldset>
  </div>

  {{-- Right column: asset files (CSS, HTML overrides) --}}
  <div>
    <details class="admin-fieldset" open>
      <summary class="admin-fieldset-heading">
        {{ Lang::txt('COM_TEMPLATES_TEMPLATE_ASSETS') }}
      </summary>
      <div class="admin-fieldset-body">
        @if (!empty($files['clo']))
          <ul class="item-list css mb-0">
            @foreach ($files['clo'] as $file)
              <li>
                @if ($canDo->get('core.edit'))
                  <a href="{{ Route::url($editBase . $file->get('id'), false) }}">
                    {{ Lang::txt('COM_TEMPLATES_TEMPLATE_EDIT_CSS', $file->get('name')) }}
                  </a>
                @else
                  {{ Lang::txt('COM_TEMPLATES_TEMPLATE_EDIT_CSS', $file->get('name')) }}
                @endif
              </li>
            @endforeach
          </ul>
        @else
          <p class="text-sm text-muted-foreground">{{ Lang::txt('COM_TEMPLATES_NO_FILES') }}</p>
        @endif
      </div>
    </details>

    <details class="admin-fieldset" open>
      <summary class="admin-fieldset-heading">
        {{ Lang::txt('COM_TEMPLATES_TEMPLATE_HTML') }}
      </summary>
      <div class="admin-fieldset-body">
        @if (!empty($files['html']))
          <ul class="item-list css mb-0">
            @foreach ($files['html'] as $file)
              <li>
                @if ($canDo->get('core.edit'))
                  <a href="{{ Route::url($editBase . $file->get('id'), false) }}">
                    {{ Lang::txt('COM_TEMPLATES_TEMPLATE_EDIT_HTML', $file->get('name')) }}
                  </a>
                @else
                  {{ Lang::txt('COM_TEMPLATES_TEMPLATE_EDIT_HTML', $file->get('name')) }}
                @endif
              </li>
            @endforeach
          </ul>
        @else
          <p class="text-sm text-muted-foreground">{{ Lang::txt('COM_TEMPLATES_NO_FILES') }}</p>
        @endif
      </div>
    </details>
  </div>

</div>
