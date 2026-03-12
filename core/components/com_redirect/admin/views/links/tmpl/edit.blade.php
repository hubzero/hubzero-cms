{{--
  Redirect Link — Admin edit view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;

  $canDo = \Components\Redirect\Helpers\Redirect::getActions();

  $legendTxt = $row->isNew()
      ? Lang::txt('COM_REDIRECT_NEW_LINK')
      : Lang::txt('COM_REDIRECT_EDIT_LINK', $row->id);

  $statusCode = $row->status_code;
  $hasNewUrl  = $row->new_url;
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_REDIRECT_MANAGER_LINK') }}"
    icon="redirect"
    :canDo="$canDo"
    option="{{ $option }}"
    :edit="true"
/>

<x-admin-edit
    option="{{ $option }}"
    controller="{{ $controller }}"
>
  <x-admin-fieldset legend="{{ $legendTxt }}">

      <div class="admin-field">
        <label for="fields-old_url" class="label">
          {{ Lang::txt('COM_REDIRECT_FIELD_OLD_URL_LABEL') }}
          <span class="text-error">*</span>
        </label>
        <input type="text"
               name="fields[old_url]"
               id="fields-old_url"
               class="input input-bordered w-full"
               required
               maxlength="255"
               value="{{ $row->old_url }}" />
        <p class="text-xs text-muted-foreground mt-1">{{ Lang::txt('COM_REDIRECT_FIELD_OLD_URL_DESC') }}</p>
      </div>

      <div class="admin-field">
        <label for="fields-new_url" class="label">
          {{ Lang::txt('COM_REDIRECT_FIELD_NEW_URL_LABEL') }}
        </label>
        <input type="text"
               name="fields[new_url]"
               id="fields-new_url"
               class="input input-bordered w-full"
               maxlength="255"
               value="{{ $row->new_url }}" />
        <p class="text-xs text-muted-foreground mt-1">{{ Lang::txt('COM_REDIRECT_FIELD_NEW_URL_DESC') }}</p>
      </div>

      <div class="admin-field">
        <label for="fields-status_code" class="label">{{ Lang::txt('COM_REDIRECT_STATUS') }}</label>
        <select name="fields[status_code]" id="fields-status_code" class="select select-bordered w-full">
          <option value="404"
                  @selected((!$hasNewUrl && !$statusCode) || $statusCode == 404)>
            {{ Lang::txt('COM_REDIRECT_STATUS_NOTFOUND') }}
          </option>
          <option value="301"
                  @selected($statusCode == 301)>
            {{ Lang::txt('COM_REDIRECT_STATUS_PERMANENT') }}
          </option>
          <option value="302"
                  @selected(($hasNewUrl && !$statusCode) || $statusCode == 302)>
            {{ Lang::txt('COM_REDIRECT_STATUS_FOUND') }}
          </option>
        </select>
      </div>

      <div class="admin-field">
        <label for="fields-comment" class="label">{{ Lang::txt('COM_REDIRECT_FIELD_COMMENT_LABEL') }}</label>
        <input type="text"
               name="fields[comment]"
               id="fields-comment"
               class="input input-bordered w-full"
               value="{{ $row->comment }}" />
        <p class="text-xs text-muted-foreground mt-1">{{ Lang::txt('COM_REDIRECT_FIELD_COMMENT_DESC') }}</p>
      </div>

  </x-admin-fieldset>

  @slot('sidebar')
    {{-- Meta --}}
    <x-admin-fieldset legend="{{ Lang::txt('JDETAILS') }}">
        <table class="admin-meta">
          <tbody>
            <tr>
              <td>{{ Lang::txt('JGLOBAL_FIELD_ID_LABEL') }}</td>
              <td>{{ $row->id ?: Lang::txt('COM_REDIRECT_NEW_LINK') }}</td>
            </tr>
            @if($row->created_date)
              <tr>
                <td>{{ Lang::txt('COM_REDIRECT_FIELD_CREATED_DATE_LABEL') }}</td>
                <td>{{ $row->created_date }}</td>
              </tr>
            @endif
            @if($row->modified_date)
              <tr>
                <td>{{ Lang::txt('COM_REDIRECT_FIELD_UPDATED_DATE_LABEL') }}</td>
                <td>{{ $row->modified_date }}</td>
              </tr>
            @endif
            <tr>
              <td>{{ Lang::txt('JGLOBAL_HITS') }}</td>
              <td>{{ (int) $row->hits }}</td>
            </tr>
          </tbody>
        </table>
    </x-admin-fieldset>

    {{-- Publishing options --}}
    <x-admin-fieldset legend="{{ Lang::txt('COM_REDIRECT_OPTIONS') }}">
        <div class="admin-field">
          <label for="fields-published" class="label">{{ Lang::txt('JSTATUS') }}</label>
          <select name="fields[published]" id="fields-published" class="select select-bordered w-full">
            <option value="1" @selected($row->published == 1)>{{ Lang::txt('JENABLED') }}</option>
            <option value="0" @selected($row->published == 0)>{{ Lang::txt('JDISABLED') }}</option>
            <option value="2" @selected($row->published == 2)>{{ Lang::txt('JARCHIVED') }}</option>
            <option value="-2" @selected($row->published == -2)>{{ Lang::txt('JTRASHED') }}</option>
          </select>
      </x-admin-fieldset>
    </div>
  @endslot

  <input type="hidden" name="fields[id]" value="{{ $row->id }}" />
  <input type="hidden" name="fields[created_date]" value="{{ $row->created_date }}" />
  <input type="hidden" name="fields[modified_date]" value="{{ $row->modified_date }}" />
  <input type="hidden" name="fields[hits]" value="{{ $row->hits }}" />
  <input type="hidden" name="task" value="" />
</x-admin-edit>
