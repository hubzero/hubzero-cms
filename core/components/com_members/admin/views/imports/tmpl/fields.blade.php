{{--
  Member Import — Field mapping view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Toolbar;
  use Hubzero\Facades\User;

  $canDo = \Components\Members\Helpers\Admin::getActions('component');

  $title = $import->get('id')
      ? Lang::txt('COM_MEMBERS_IMPORT_TITLE_EDIT')
      : Lang::txt('COM_MEMBERS_IMPORT_TITLE_ADD');

  $__view->css('import')->js('import');
@endphp

@php
  Toolbar::title(Lang::txt('COM_MEMBERS') . ': ' . $title, 'import');
  if ($canDo->get('core.admin')) {
      Toolbar::save();
      Toolbar::spacer();
  }
  Toolbar::cancel();
@endphp

<x-admin-edit
    option="{{ $option }}"
    controller="{{ $controller }}"
>
  <p class="warning">{{ Lang::txt('COM_MEMBERS_IMPORT_EDIT_FIELDSET_MAPPING_REQUIRED') }}</p>

  @include('com_members::admin.views.imports.tmpl._fieldmap', ['import' => $import])

  @slot('sidebar')
    <x-admin-fieldset legend="{{ Lang::txt('JDETAILS') }}">
        <table class="admin-meta">
          <tbody>
            <tr>
              <td>{{ Lang::txt('COM_MEMBERS_IMPORT_EDIT_FIELD_ID') }}</td>
              <td>{{ $import->get('id') }}</td>
            </tr>
            <tr>
              <td>{{ Lang::txt('COM_MEMBERS_IMPORT_EDIT_FIELD_CREATEDBY') }}</td>
              <td>
                @php
                  $createdBy = User::getInstance($import->get('created_by'));
                @endphp
                @if($createdBy)
                  {{ $createdBy->get('name') }}
                @endif
              </td>
            </tr>
            <tr>
              <td>{{ Lang::txt('COM_MEMBERS_IMPORT_EDIT_FIELD_CREATEDON') }}</td>
              <td>
                <time datetime="{{ $import->get('created_at') }}">
                  {{ Date::of($import->get('created_at'))->toLocal('m/d/Y @ g:i a') }}
                </time>
              </td>
            </tr>
          </tbody>
        </table>
    </x-admin-fieldset>
  @endslot

  <input type="hidden" name="import[id]" value="{{ $import->get('id') }}" />
</x-admin-edit>
