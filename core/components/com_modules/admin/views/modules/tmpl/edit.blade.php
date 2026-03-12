{{--
  com_modules — Module edit/create

  Variables: $item (Module model), $form (Form object)

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Toolbar;
  use Hubzero\Facades\User;

  $isNew      = ($item->id == 0);
  $checkedOut = !($item->checked_out == 0 || $item->checked_out == User::get('id'));
  $canDo      = \Components\Modules\Helpers\Modules::getActions();

  Toolbar::title(
      Lang::txt('COM_MODULES_MANAGER_MODULE', e(Lang::txt($item->module))),
      'module'
  );

  if (!$checkedOut && ($canDo->get('core.edit') || $canDo->get('core.create'))) {
      Toolbar::apply();
      Toolbar::save();
  }
  if (!$checkedOut && $canDo->get('core.create')) {
      Toolbar::save2new();
  }
  if (!$isNew && $canDo->get('core.create')) {
      Toolbar::save2copy();
  }
  Toolbar::cancel();
  Toolbar::help('module');

  $hasContent = empty($item->get('module'))
      || $item->module == 'custom'
      || $item->module == 'mod_custom';

  $isLoginModule = $item->xml && (string) $item->xml->name == 'Login Form';

  $__view->js('edit');
@endphp

<x-admin-edit
    option="{{ $option }}"
    controller="{{ $controller }}"
>
  {{-- ── Main column ─────────────────────────────────────── --}}

  {{-- Details --}}
  <x-admin-fieldset legend="{{ Lang::txt('JDETAILS') }}">

    <div class="admin-field">
      {!! $form->getLabel('title') !!}
      {!! $form->getInput('title') !!}
    </div>

    <div class="admin-field">
      {!! $form->getLabel('showtitle') !!}
      {!! $form->getInput('showtitle') !!}
    </div>

    <div class="admin-field">
      {!! $form->getLabel('position') !!}
      {!! $form->getInput('position') !!}
    </div>

    <div class="admin-field">
      {!! $form->getLabel('ordering') !!}
      {!! $form->getInput('ordering') !!}
    </div>

    @if (!$isLoginModule)
      <div class="grid grid-cols-2 gap-4">
        <div class="admin-field">
          {!! $form->getLabel('published') !!}
          {!! $form->getInput('published') !!}
        </div>
        <div class="admin-field">
          {!! $form->getLabel('access') !!}
          {!! $form->getInput('access') !!}
        </div>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div class="admin-field">
          {!! $form->getLabel('publish_up') !!}
          {!! $form->getInput('publish_up') !!}
        </div>
        <div class="admin-field">
          {!! $form->getLabel('publish_down') !!}
          {!! $form->getInput('publish_down') !!}
        </div>
      </div>
    @else
      <div class="admin-field">
        {!! $form->getLabel('access') !!}
        {!! $form->getInput('access') !!}
      </div>
    @endif

    <div class="admin-field">
      {!! $form->getLabel('language') !!}
      {!! $form->getInput('language') !!}
    </div>

    <div class="admin-field">
      {!! $form->getLabel('note') !!}
      {!! $form->getInput('note') !!}
    </div>

    @if ($item->id)
      <div class="admin-field">
        {!! $form->getLabel('id') !!}
        {!! $form->getInput('id') !!}
      </div>
    @endif

  </x-admin-fieldset>

  {{-- Custom output (for custom modules) --}}
  @if ($hasContent)
    <x-admin-fieldset legend="{{ Lang::txt('COM_MODULES_CUSTOM_OUTPUT') }}">
      <div class="admin-field">
        {!! $form->getLabel('content') !!}
        {!! $form->getInput('content') !!}
      </div>
    </x-admin-fieldset>
  @endif

  {{-- Menu assignment (site modules only) --}}
  @if ($item->client_id == 0)
    @include('com_modules::admin.views.modules.tmpl._assignment')
  @endif

  {{-- ── Sidebar ─────────────────────────────────────────── --}}
  @slot('sidebar')

    {{-- Module info --}}
    <x-admin-fieldset legend="{{ Lang::txt('JDETAILS') }}">
      <table class="w-full">
        <tbody>
          <tr>
            <th class="text-xs font-medium text-muted-foreground pr-3 py-1 whitespace-nowrap align-top">
              {{ Lang::txt('COM_MODULES_HEADING_MODULE') }}
            </th>
            <td class="text-sm py-1">
              {!! $form->getInput('module') !!}
              @if ($item->xml)
                {{ ($text = (string) $item->xml->name) ? Lang::txt($text) : e($item->module) }}
              @else
                <span class="text-error text-xs">{{ Lang::txt('COM_MODULES_ERR_XML') }}</span>
              @endif
            </td>
          </tr>
          <tr>
            <th class="text-xs font-medium text-muted-foreground pr-3 py-1 whitespace-nowrap">
              {{ Lang::txt('Client') }}
            </th>
            <td class="text-sm py-1">
              {!! $form->getInput('client_id') !!}
              {{ $item->client_id == 0 ? Lang::txt('JSITE') : Lang::txt('JADMINISTRATOR') }}
            </td>
          </tr>
          <tr>
            <th class="text-xs font-medium text-muted-foreground pr-3 py-1 whitespace-nowrap align-top">
              {{ Lang::txt('COM_MODULES_MODULE_DESCRIPTION') }}
            </th>
            <td class="text-sm py-1 text-muted-foreground">
              @if ($item->xml)
                @php $desc = trim($item->xml->description); @endphp
                @if ($desc)
                  {{ Lang::txt($desc) }}
                @endif
              @else
                <span class="text-error text-xs">{{ Lang::txt('COM_MODULES_ERR_XML') }}</span>
              @endif
            </td>
          </tr>
        </tbody>
      </table>
    </x-admin-fieldset>

    {{-- Options (params fieldsets) --}}
    @include('com_modules::admin.views.modules.tmpl._options')

  @endslot

</x-admin-edit>
