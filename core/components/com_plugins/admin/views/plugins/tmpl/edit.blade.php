{{--
  com_plugins — Plugin edit form

  Variables: $item (Plugin model), $form (Form object)

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Request;
  use Hubzero\Facades\Toolbar;
  use Hubzero\Facades\User;

  $canDo = \Components\Plugins\Helpers\Plugins::getActions();

  Toolbar::title(
      Lang::txt('COM_PLUGINS_MANAGER_PLUGIN', Lang::txt($item->name)),
      'plugin'
  );
  if ($canDo->get('core.edit')) {
      Toolbar::apply('apply');
      Toolbar::save('save');
  }
  Toolbar::cancel('cancel', 'JTOOLBAR_CLOSE');
  Toolbar::spacer();
  Toolbar::help('plugin');

  $__view->js();

  $extId       = $item->extension_id;
  $hasModified = $item->modified && $item->modified !== '0000-00-00 00:00:00';
@endphp

<x-admin-toolbar
    title=""
    icon="plugin"
    :canDo="$canDo"
    option="{{ $option }}"
    :help="false"
/>

<x-admin-edit
    option="{{ $option }}"
    controller="{{ $controller }}"
>
  {{-- Details fieldset --}}
  <x-admin-fieldset legend="{{ Lang::txt('JDETAILS') }}">

    <div class="grid grid-cols-2 gap-4">
      <div class="admin-field">
        {!! $form->getLabel('enabled') !!}
        {!! $form->getInput('enabled') !!}
      </div>
      <div class="admin-field">
        {!! $form->getLabel('access') !!}
        {!! $form->getInput('access') !!}
      </div>
    </div>

    <div class="admin-field">
      {!! $form->getLabel('ordering') !!}
      {!! $form->getInput('ordering') !!}
    </div>

  </x-admin-fieldset>

  @slot('sidebar')
    {{-- Meta --}}
    <x-admin-fieldset legend="{{ Lang::txt('JDETAILS') }}">
      <table class="admin-meta w-full">
        <tbody>
          <tr>
            <th class="text-xs font-medium text-muted-foreground pr-3 py-1 whitespace-nowrap">
              {{ Lang::txt('COM_PLUGINS_FIELD_NAME_LABEL') }}
            </th>
            <td class="text-sm py-1">
              {{ Lang::txt($item->name) }}
              {!! $form->getInput('name') !!}
            </td>
          </tr>
          @if ($extId)
            <tr>
              <th class="text-xs font-medium text-muted-foreground pr-3 py-1 whitespace-nowrap">
                {{ Lang::txt('JGLOBAL_FIELD_ID_LABEL') }}
              </th>
              <td class="text-sm py-1">
                {{ (int) $extId }}
                <input type="hidden" name="fields[extension_id]" value="{{ (int) $extId }}" />
              </td>
            </tr>
          @endif
          <tr>
            <th class="text-xs font-medium text-muted-foreground pr-3 py-1 whitespace-nowrap">
              {{ Lang::txt('COM_PLUGINS_FIELD_FOLDER_LABEL') }}
            </th>
            <td class="text-sm py-1">
              {{ $item->folder }}
              <input type="hidden" name="fields[folder]" value="{{ $item->folder }}" />
            </td>
          </tr>
          <tr>
            <th class="text-xs font-medium text-muted-foreground pr-3 py-1 whitespace-nowrap">
              {{ Lang::txt('COM_PLUGINS_FIELD_ELEMENT_LABEL') }}
            </th>
            <td class="text-sm py-1">
              {{ $item->element }}
              <input type="hidden" name="fields[element]" value="{{ $item->element }}" />
            </td>
          </tr>
          <tr>
            <th class="text-xs font-medium text-muted-foreground pr-3 py-1 whitespace-nowrap">
              {{ Lang::txt('JGLOBAL_DESCRIPTION') }}
            </th>
            <td class="text-sm py-1">
              @if ($item->xml)
                @php $desc = trim($item->xml->description ?? ''); @endphp
                @if ($desc)
                  {{ Lang::txt($desc) }}
                @else
                  <span class="text-faint-foreground">—</span>
                @endif
              @else
                <span class="text-error text-xs">{{ Lang::txt('COM_PLUGINS_XML_ERR') }}</span>
              @endif
            </td>
          </tr>
          @if ($hasModified)
            <tr>
              <th class="text-xs font-medium text-muted-foreground pr-3 py-1 whitespace-nowrap">
                {{ Lang::txt('JGLOBAL_FIELD_MODIFIED_LABEL') }}
              </th>
              <td class="text-sm py-1">
                <time datetime="{{ $item->modified }}">
                  {{ Date::of($item->modified)->toLocal() }}
                </time>
              </td>
            </tr>
          @endif
          @if ($item->modified_by)
            <tr>
              <th class="text-xs font-medium text-muted-foreground pr-3 py-1 whitespace-nowrap">
                {{ Lang::txt('JGLOBAL_FIELD_MODIFIED_BY_LABEL') }}
              </th>
              <td class="text-sm py-1">
                @php
                  $modifier = User::getInstance($item->modified_by);
                  $modName  = $modifier->get('name', Lang::txt('COM_PLUGINS_UNKNOWN'));
                @endphp
                {{ $modName }}
              </td>
            </tr>
          @endif
        </tbody>
      </table>
    </x-admin-fieldset>

    {{-- Plugin-specific options --}}
    @include('com_plugins::admin.views.plugins.tmpl._options')
  @endslot

  <input type="hidden" name="id" value="{{ (int) $extId }}" />
  <input type="hidden" name="component" value="{{ Request::getCmd('component', '') }}" />
</x-admin-edit>
