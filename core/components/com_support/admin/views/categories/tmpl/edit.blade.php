{{--
  Support — Category edit/create form

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Toolbar;
  use Hubzero\Facades\User;

  $canDo = \Components\Support\Helpers\Permissions::getActions('category');
  $text  = $row->get('id') ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE');

  Toolbar::title(
      Lang::txt('COM_SUPPORT_TICKETS') . ': '
      . Lang::txt('COM_SUPPORT_CATEGORIES') . ': ' . $text,
      'support'
  );
  if ($canDo->get('core.edit')) {
      Toolbar::apply();
      Toolbar::save();
      Toolbar::spacer();
  }
  Toolbar::cancel();
  Toolbar::spacer();
  Toolbar::help('category');
@endphp

<x-admin-edit
    option="{{ $option }}"
    controller="{{ $controller }}"
>
  {{-- Left column: Details fieldset --}}
  <x-admin-fieldset legend="{{ Lang::txt('JDETAILS') }}">

      <div class="admin-field">
        <label for="field-title" class="label text-base-content">
          {{ Lang::txt('COM_SUPPORT_FIELD_TITLE') }}
          <span class="text-error">*</span>
        </label>
        <input type="text"
               name="fields[title]"
               id="field-title"
               class="input input-bordered w-full"
               required
               value="{{ $row->get('title', '') }}" />
      </div>

      <div class="admin-field">
        <label for="field-alias" class="label text-base-content">
          {{ Lang::txt('COM_SUPPORT_FIELD_ALIAS') }}
        </label>
        <input type="text"
               name="fields[alias]"
               id="field-alias"
               class="input input-bordered w-full"
               value="{{ $row->get('alias', '') }}" />
        <p class="text-xs text-muted-foreground mt-1">
          {{ Lang::txt('COM_SUPPORT_FIELD_ALIAS_HINT') }}
        </p>
      </div>

  </x-admin-fieldset>

  @slot('sidebar')
    <x-admin-fieldset legend="{{ Lang::txt('JDETAILS') }}">
        <table class="admin-meta">
          <tbody>
            <tr>
              <td>{{ Lang::txt('COM_SUPPORT_FIELD_ID') }}</td>
              <td>
                {{ $row->get('id', 0) ?: Lang::txt('JNEW') }}
                <input type="hidden" name="fields[id]"
                       value="{{ $row->get('id', 0) }}" />
              </td>
            </tr>
            @if($row->get('created_by'))
              <tr>
                <td>{{ Lang::txt('COM_SUPPORT_FIELD_CREATED') }}</td>
                <td>
                  @php $createdDate = Date::of($row->get('created'))->toLocal('Y-m-d H:i:s'); @endphp
                  <time datetime="{{ $row->get('created') }}">{{ $createdDate }}</time>
                </td>
              </tr>
              <tr>
                <td>{{ Lang::txt('COM_SUPPORT_FIELD_CREATOR') }}</td>
                <td>
                  @php $creator = User::getInstance($row->get('created_by')); @endphp
                  {{ $creator->get('name', '') }}
                </td>
              </tr>
              @php
                $isModified = $row->get('modified_by')
                    && $row->get('modified_by') != '0000-00-00 00:00:00';
              @endphp
              @if($isModified)
                <tr>
                  <td>{{ Lang::txt('COM_SUPPORT_FIELD_MODIFIED') }}</td>
                  <td>
                    @php $modifiedDate = Date::of($row->get('modified'))->toLocal('Y-m-d H:i:s'); @endphp
                    <time datetime="{{ $row->get('modified') }}">{{ $modifiedDate }}</time>
                  </td>
                </tr>
                <tr>
                  <td>{{ Lang::txt('COM_SUPPORT_FIELD_MODIFIER') }}</td>
                  <td>
                    @php $modifier = User::getInstance($row->get('modified_by')); @endphp
                    {{ $modifier->get('name', '') }}
                  </td>
                </tr>
              @endif
            @endif
          </tbody>
        </table>
    </x-admin-fieldset>
  @endslot

</x-admin-edit>
