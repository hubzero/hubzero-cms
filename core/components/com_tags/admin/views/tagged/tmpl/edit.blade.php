{{--
  Tagged item — Admin edit

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;

  $canDo = \Components\Tags\Helpers\Permissions::getActions();
  $text  = $row->get('id') ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE');
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_TAGS') }}: {{ Lang::txt('COM_TAGS_TAGGED') }}: {{ $text }}"
    icon="tags"
    :canDo="$canDo"
    option="{{ $option }}"
    :edit="true"
/>

<x-admin-edit
    option="{{ $option }}"
    controller="{{ $controller }}"
>
  {{-- Details --}}
  <x-admin-fieldset legend="{{ Lang::txt('JDETAILS') }}">

      <div class="admin-field">
        <label for="field-tagid" class="label">
          {{ Lang::txt('COM_TAGS_FIELD_TAGID') }}
          <span class="text-error">*</span>
        </label>
        <input type="text"
               name="fields[tagid]"
               id="field-tagid"
               class="input input-bordered w-full"
               required
               maxlength="11"
               value="{{ $row->get('tagid') }}" />
        <p class="text-xs text-muted-foreground mt-1">
          {{ Lang::txt('COM_TAGS_FIELD_TAGID_HINT') }}
        </p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="admin-field">
          <label for="field-objectid" class="label">
            {{ Lang::txt('COM_TAGS_FIELD_OBJECTID') }}
            <span class="text-error">*</span>
          </label>
          <input type="text"
                 name="fields[objectid]"
                 id="field-objectid"
                 class="input input-bordered w-full"
                 required
                 maxlength="11"
                 value="{{ $row->get('objectid') }}" />
          <p class="text-xs text-muted-foreground mt-1">
            {{ Lang::txt('COM_TAGS_FIELD_OBJECTID_HINT') }}
          </p>
        </div>

        <div class="admin-field">
          <label for="field-tbl" class="label">
            {{ Lang::txt('COM_TAGS_FIELD_TBL') }}
            <span class="text-error">*</span>
          </label>
          <input type="text"
                 name="fields[tbl]"
                 id="field-tbl"
                 class="input input-bordered w-full"
                 required
                 maxlength="250"
                 value="{{ $row->get('tbl') }}" />
          <p class="text-xs text-muted-foreground mt-1">
            {{ Lang::txt('COM_TAGS_FIELD_TBL_HINT') }}
          </p>
        </div>
      </div>

  </x-admin-fieldset>

  @slot('sidebar')
    {{-- Meta --}}
    <x-admin-fieldset legend="{{ Lang::txt('JDETAILS') }}">
        <table class="admin-meta">
          <tbody>
            <tr>
              <td>{{ Lang::txt('COM_TAGS_FIELD_ID') }}</td>
              <td>{{ $row->get('id') ?: Lang::txt('JNEW') }}</td>
            </tr>
            <tr>
              <td>{{ Lang::txt('COM_TAGS_FIELD_CREATOR') }}</td>
              <td>
                @php
                  $creatorName = Lang::txt('COM_TAGS_UNKNOWN');
                  if ($row->creator->get('id')) {
                      $creatorName = $row->creator->get('name');
                  }
                @endphp
                {{ $creatorName }}
              </td>
            </tr>
            <tr>
              <td>{{ Lang::txt('COM_TAGS_FIELD_CREATED') }}</td>
              <td>
                @php $rowCreated = $row->created(); @endphp
                {{ ($rowCreated && $rowCreated != '0000-00-00 00:00:00')
                    ? $rowCreated : Lang::txt('COM_TAGS_UNKNOWN') }}
              </td>
            </tr>
          </tbody>
        </table>
    </x-admin-fieldset>
  @endslot

  <input type="hidden" name="fields[id]" value="{{ $row->get('id') }}" />
  <input type="hidden" name="fields[taggerid]" value="{{ $row->get('taggerid') }}" />
  <input type="hidden" name="fields[taggedon]" value="{{ $row->get('taggedon') }}" />
  <input type="hidden" name="task" value="save" />
</x-admin-edit>
