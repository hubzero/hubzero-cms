{{--
  Wiki Revision — Admin edit

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Lang;

  $canDo = \Components\Wiki\Helpers\Permissions::getActions('page');
  $text  = $row->get('id') ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE');
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_WIKI') }}: {{ Lang::txt('COM_WIKI_REVISION') }}: {{ $text }}"
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
        <label for="field-summary" class="label">
          {{ Lang::txt('COM_WIKI_FIELD_EDIT_SUMMARY') }}
        </label>
        <input type="text"
               name="revision[summary]"
               id="field-summary"
               class="input input-bordered w-full"
               maxlength="255"
               value="{{ $row->get('summary', '') }}" />
      </div>

      <div class="admin-field">
        <label for="field-pagetext" class="label">
          {{ Lang::txt('COM_WIKI_FIELD_TEXT') }}
        </label>
        <textarea name="revision[pagetext]"
                  id="field-pagetext"
                  class="textarea textarea-bordered w-full font-mono"
                  rows="40">{{ $row->get('pagetext', '') }}</textarea>
      </div>

  </x-admin-fieldset>

  @slot('sidebar')
    {{-- Page meta --}}
    <x-admin-fieldset legend="{{ Lang::txt('COM_WIKI_PAGE') }}">
        <table class="admin-meta">
          <tbody>
            <tr>
              <td>{{ Lang::txt('COM_WIKI_FIELD_TITLE') }}</td>
              <td>{{ $page->get('title') }}</td>
            </tr>
            <tr>
              <td>{{ Lang::txt('COM_WIKI_FIELD_PAGENAME') }}</td>
              <td>{{ $page->get('pagename') }}</td>
            </tr>
            <tr>
              <td>{{ Lang::txt('COM_WIKI_FIELD_SCOPE') }}</td>
              <td>{{ $page->get('scope') . ':' . $page->get('scope_id') }}</td>
            </tr>
            <tr>
              <td>{{ Lang::txt('COM_WIKI_FIELD_ID') }} ({{ Lang::txt('COM_WIKI_PAGE') }})</td>
              <td>{{ $row->get('page_id') }}</td>
            </tr>
            <tr>
              <td>{{ Lang::txt('COM_WIKI_FIELD_ID') }}</td>
              <td>{{ $row->get('id') ?: Lang::txt('JNEW') }}</td>
            </tr>
            <tr>
              <td>{{ Lang::txt('COM_WIKI_FIELD_REVISION') }}</td>
              <td>{{ $row->get('version') }}</td>
            </tr>
            <tr>
              <td>{{ Lang::txt('COM_WIKI_FIELD_CREATOR') }}</td>
              <td>{{ $row->creator->get('name', Lang::txt('COM_WIKI_UNKNOWN')) }}</td>
            </tr>
            <tr>
              <td>{{ Lang::txt('COM_WIKI_FIELD_CREATED') }}</td>
              <td>
                @if ($row->get('created'))
                  <time datetime="{{ $row->get('created') }}">
                    {{ Date::of($row->get('created'))->toLocal() }}
                  </time>
                @else
                  {{ Lang::txt('JNEW') }}
                @endif
              </td>
            </tr>
          </tbody>
        </table>
    </x-admin-fieldset>

    {{-- Parameters --}}
    <x-admin-fieldset legend="{{ Lang::txt('COM_WIKI_FIELDSET_PARAMETERS') }}">

        <div class="admin-field">
          <label class="label cursor-pointer justify-start gap-2">
            <input type="checkbox"
                   name="revision[minor_edit]"
                   class="checkbox"
                   value="1"
                   @checked($row->get('minor_edit')) />
            <span>{{ Lang::txt('COM_WIKI_FIELD_MINOR_EDIT') }}</span>
          </label>
        </div>

        <div class="admin-field">
          <label for="field-approved" class="label">
            {{ Lang::txt('COM_WIKI_FIELD_STATE') }}
          </label>
          <select name="revision[approved]"
                  id="field-approved"
                  class="select select-bordered w-full">
            <option value="0" @selected($row->get('approved') == 0)>
              {{ Lang::txt('COM_WIKI_STATE_NOT_APPROVED') }}
            </option>
            <option value="1" @selected($row->get('approved') == 1)>
              {{ Lang::txt('COM_WIKI_STATE_APPROVED') }}
            </option>
            <option value="2" @selected($row->get('approved') == 2)>
              {{ Lang::txt('COM_WIKI_STATE_TRASHED') }}
            </option>
          </select>
        </div>

    </x-admin-fieldset>
  @endslot

  <input type="hidden" name="revision[id]" value="{{ $row->get('id') }}" />
  <input type="hidden" name="revision[page_id]" value="{{ $row->get('page_id') }}" />
  <input type="hidden" name="revision[version]" value="{{ $row->get('version') }}" />
  <input type="hidden" name="revision[created_by]" value="{{ $row->get('created_by') }}" />
  <input type="hidden" name="revision[created]" value="{{ $row->get('created') }}" />
  <input type="hidden" name="pageid" value="{{ $row->get('page_id') }}" />
  <input type="hidden" name="task" value="save" />
</x-admin-edit>
