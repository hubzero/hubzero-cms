{{--
  com_templates — Style edit/create form

  Variables: $item (Style model), $form (Form object with template params)

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\Toolbar;
  use Hubzero\Facades\User;

  $canDo = \Components\Templates\Helpers\Utilities::getActions();
  $isNew = $item->isNew();

  Toolbar::title(
      $isNew
          ? Lang::txt('COM_TEMPLATES_MANAGER_ADD_STYLE')
          : Lang::txt('COM_TEMPLATES_MANAGER_EDIT_STYLE'),
      'thememanager'
  );

  if ($canDo->get('core.edit')) {
      Toolbar::apply();
      Toolbar::save();
  }
  if (!$isNew && $canDo->get('core.create')) {
      Toolbar::save2copy();
  }
  Toolbar::cancel();
  Toolbar::spacer();
  Toolbar::help('style');

  // templates.js: form validation + menu assignment invert button
  $__view->js('templates');

  $formAction = Route::url(
      'index.php?option=' . $option
      . '&controller=' . $controller
      . '&task=edit&id=' . (int) $item->id, false
  );

  $xml         = $item->parent->xml;
  $description = $xml ? trim($xml->get('description', '')) : null;
@endphp

<form action="{{ $formAction }}"
      method="post"
      name="adminForm"
      id="item-form"
      class="editform"
      data-invalid-msg="{{ Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED') }}">

  <div class="grid grid-cols-1 lg:grid-cols-[1fr_24rem] gap-6">

    {{-- Left column: title, home, menu assignment --}}
    <div class="min-w-0">
      <x-admin-fieldset legend="{{ Lang::txt('JDETAILS') }}">

        <div class="admin-field">
          <label for="field-title" class="label">
            {{ Lang::txt('COM_TEMPLATES_FIELD_TITLE_LABEL') }}
            <span class="text-error">*</span>
          </label>
          <input type="text"
                 name="fields[title]"
                 id="field-title"
                 class="input input-bordered w-full required"
                 maxlength="250"
                 required
                 value="{{ $item->get('title') }}" />
        </div>

        {{-- Home / default: select for site, radios for admin --}}
        @if ($item->client_id == 0)
          <div class="admin-field">
            <label for="field-home" class="label">
              {{ Lang::txt('COM_TEMPLATES_FIELD_HOME_LABEL') }}
            </label>
            <select name="fields[home]" id="field-home" class="select select-bordered w-full">
              <option value="0" {{ $item->home == 0 ? 'selected' : '' }}>
                {{ Lang::txt('JNO') }}
              </option>
              <option value="1" {{ $item->home == 1 ? 'selected' : '' }}>
                {{ Lang::txt('JALL') }}
              </option>
            </select>
            <p class="text-xs text-muted-foreground mt-1">
              {!! Lang::txt('COM_TEMPLATES_FIELD_HOME_SITE_DESC') !!}
            </p>
          </div>
        @else
          <div class="admin-field">
            <label class="label">{{ Lang::txt('COM_TEMPLATES_FIELD_HOME_LABEL') }}</label>
            <div class="flex gap-4">
              <label class="flex items-center gap-2 cursor-pointer">
                <input type="radio"
                       id="fields_home0"
                       name="fields[home]"
                       value="0"
                       class="radio radio-sm"
                       {{ $item->home == 0 ? 'checked' : '' }} />
                <span class="text-sm">{{ Lang::txt('JNO') }}</span>
              </label>
              <label class="flex items-center gap-2 cursor-pointer">
                <input type="radio"
                       id="fields_home1"
                       name="fields[home]"
                       value="1"
                       class="radio radio-sm"
                       {{ $item->home == 1 ? 'checked' : '' }} />
                <span class="text-sm">{{ Lang::txt('JYES') }}</span>
              </label>
            </div>
          </div>
        @endif

      </x-admin-fieldset>

      {{-- Menu assignment (site templates only, if user can edit com_menus) --}}
      @if (User::authorise('core.edit', 'com_menu') && $item->client_id == 0 && $canDo->get('core.edit.state'))
        @include('com_templates::admin.views.styles.tmpl._assignment')
      @endif
    </div>

    {{-- Right column: meta info + template options --}}
    <div>
      <x-admin-fieldset>
        <table class="admin-meta w-full">
          <tbody>
            @if ($item->id)
              <tr>
                <th class="text-xs font-medium text-muted-foreground pr-3 py-1 whitespace-nowrap">
                  {{ Lang::txt('JGLOBAL_FIELD_ID_LABEL') }}
                </th>
                <td class="text-sm py-1">{{ $item->id }}</td>
              </tr>
            @endif
            @if ($xml)
              @if ($description)
                <tr>
                  <th class="text-xs font-medium text-muted-foreground pr-3 py-1 whitespace-nowrap">
                    {{ Lang::txt('COM_TEMPLATES_TEMPLATE_DESCRIPTION') }}
                  </th>
                  <td class="text-sm py-1">{{ Lang::txt($description) }}</td>
                </tr>
              @endif
            @else
              <tr>
                <td colspan="2">
                  <div role="alert" class="alert alert-error text-sm py-2">
                    {{ Lang::txt('COM_TEMPLATES_ERR_XML') }}
                  </div>
                </td>
              </tr>
            @endif
            <tr>
              <th class="text-xs font-medium text-muted-foreground pr-3 py-1 whitespace-nowrap">
                {{ Lang::txt('COM_TEMPLATES_FIELD_TEMPLATE_LABEL') }}
              </th>
              <td class="text-sm py-1">
                {{ $item->template }}
                <input type="hidden" name="fields[template]" value="{{ $item->template }}" />
              </td>
            </tr>
            <tr>
              <th class="text-xs font-medium text-muted-foreground pr-3 py-1 whitespace-nowrap">
                {{ Lang::txt('COM_TEMPLATES_FIELD_CLIENT_LABEL') }}
              </th>
              <td class="text-sm py-1">
                {{ $item->client_id == 0 ? Lang::txt('JSITE') : Lang::txt('JADMINISTRATOR') }}
                <input type="hidden" name="fields[client_id]" value="{{ $item->client_id }}" />
              </td>
            </tr>
          </tbody>
        </table>
      </x-admin-fieldset>

      {{-- Template-specific options from config.xml --}}
      @include('com_templates::admin.views.styles.tmpl._options')
    </div>

  </div>

  <input type="hidden" name="fields[id]" value="{{ $item->id }}" />
  <input type="hidden" name="option"     value="{{ $option }}" />
  <input type="hidden" name="controller" value="{{ $controller }}" />
  <input type="hidden" name="task"       value="" />
  {!! Html::input('token') !!}

</form>
