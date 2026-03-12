{{--
  Wiki Comment — Admin edit

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;

  $canDo = \Components\Wiki\Helpers\Permissions::getActions('comment');
  $text  = $row->get('id') ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE');
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_WIKI') }}: {{ Lang::txt('COM_WIKI_PAGE') }}: {{ Lang::txt('COM_WIKI_COMMENTS') }}: {{ $text }}"
    icon="wiki"
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
        <label class="label cursor-pointer justify-start gap-2">
          <input type="checkbox"
                 name="fields[anonymous]"
                 class="checkbox"
                 value="1"
                 @checked($row->get('anonymous')) />
          <span>{{ Lang::txt('COM_WIKI_FIELD_ANONYMOUS') }}</span>
        </label>
      </div>

      <div class="admin-field">
        <label for="field-ctext" class="label">
          {{ Lang::txt('COM_WIKI_FIELD_CONTENT') }}
          <span class="text-error">*</span>
        </label>
        <textarea name="fields[ctext]"
                  id="field-ctext"
                  class="textarea textarea-bordered w-full"
                  required
                  rows="15">{{ $row->get('ctext', '') }}</textarea>
      </div>

  </x-admin-fieldset>

  @slot('sidebar')
    <x-admin-fieldset legend="{{ Lang::txt('JDETAILS') }}">
        <table class="admin-meta">
          <tbody>
            <tr>
              <td>{{ Lang::txt('COM_WIKI_FIELD_CREATOR') }}</td>
              <td>{{ $row->creator->get('name') }}</td>
            </tr>
            <tr>
              <td>{{ Lang::txt('COM_WIKI_FIELD_CREATED') }}</td>
              <td>{{ $row->get('created') }}</td>
            </tr>
            <tr>
              <td>{{ Lang::txt('COM_WIKI_FIELD_PAGE') }}</td>
              <td>{{ $row->get('page_id') }}</td>
            </tr>
          </tbody>
        </table>
    </x-admin-fieldset>
  @endslot

  <input type="hidden" name="fields[id]" value="{{ $row->get('id') }}" />
  <input type="hidden" name="fields[parent]" value="{{ $row->get('parent') }}" />
  <input type="hidden" name="fields[page_id]" value="{{ $row->get('page_id') }}" />
  <input type="hidden" name="fields[created_by]" value="{{ $row->get('created_by') }}" />
  <input type="hidden" name="fields[created]" value="{{ $row->get('created') }}" />
  <input type="hidden" name="page_id" value="{{ $row->get('page_id') }}" />
  <input type="hidden" name="task" value="save" />
</x-admin-edit>
