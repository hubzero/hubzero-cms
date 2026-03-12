{{--
  Developer Applications — Admin edit/create form

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Event;

  $canDo = \Components\Developer\Helpers\Permissions::getActions('application');
  $text  = ($task == 'edit') ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE');
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_DEVELOPER') }}: {{ Lang::txt('COM_DEVELOPER_APPLICATIONS') }}: {{ $text }}"
    icon="developer"
    :canDo="$canDo"
    :edit="true"
/>

<x-admin-edit
    option="{{ $option }}"
    controller="{{ $controller }}"
>
  {{-- Main content column --}}
  <x-admin-fieldset legend="{{ Lang::txt('JDETAILS') }}">

      <div class="admin-field">
        <label for="field-name" class="label">
          {{ Lang::txt('COM_DEVELOPER_FIELD_NAME') }}
          <span class="text-error">*</span>
        </label>
        <input type="text"
               name="fields[name]"
               id="field-name"
               class="input input-bordered w-full"
               maxlength="250"
               required
               value="{{ $row->get('name', '') }}" />
      </div>

      <div class="admin-field">
        <label for="field-description" class="label">
          {{ Lang::txt('COM_DEVELOPER_FIELD_DESCRIPTION') }}
          <span class="text-error">*</span>
        </label>
        <textarea name="fields[description]"
                  id="field-description"
                  class="textarea textarea-bordered w-full"
                  rows="10"
                  required>{{ $row->get('description', '') }}</textarea>
      </div>

      <div class="admin-field">
        <label for="field-redirect_uri" class="label">
          {{ Lang::txt('COM_DEVELOPER_FIELD_REDIRECT_URI') }}
          <span class="text-error">*</span>
        </label>
        @php
          $uris = implode(PHP_EOL, explode(' ', $row->get('redirect_uri', '')));
        @endphp
        <textarea name="fields[redirect_uri]"
                  id="field-redirect_uri"
                  class="textarea textarea-bordered w-full font-mono text-sm"
                  rows="3"
                  required>{{ $uris }}</textarea>
        <p class="text-xs text-muted-foreground mt-1">{{ Lang::txt('COM_DEVELOPER_FIELD_REDIRECT_URI_HINT') }}</p>
      </div>

  </x-admin-fieldset>

  @slot('sidebar')
    {{-- Meta (edit only) --}}
    @if($row->get('id'))
      <x-admin-fieldset legend="{{ Lang::txt('JDETAILS') }}">
          <table class="admin-meta">
            <tbody>
              <tr>
                <td>{{ Lang::txt('COM_DEVELOPER_FIELD_CREATED') }}</td>
                <td>
                  {{ $row->creator->get('name', 'System User') }}
                  <input type="hidden" name="fields[created_by]"
                         value="{{ $row->get('created_by') }}" />
                </td>
              </tr>
              <tr>
                <td>{{ Lang::txt('COM_DEVELOPER_FIELD_CREATED_BY') }}</td>
                <td>
                  {{ Date::of($row->get('created'))->toLocal() }}
                  <input type="hidden" name="fields[created]"
                         value="{{ $row->get('created') }}" />
                </td>
              </tr>
              <tr>
                <td>{{ Lang::txt('COM_DEVELOPER_FIELD_CLIENT_ID') }}</td>
                <td>
                  <code class="text-xs">{{ $row->get('client_id') }}</code>
                  <input type="hidden" name="fields[client_id]"
                         value="{{ $row->get('client_id') }}" />
                </td>
              </tr>
              <tr>
                <td>{{ Lang::txt('COM_DEVELOPER_FIELD_CLIENT_SECRET') }}</td>
                <td>
                  <code class="text-xs">{{ $row->get('client_secret') }}</code>
                  <input type="hidden" name="fields[client_secret]"
                         value="{{ $row->get('client_secret') }}" />
                </td>
              </tr>
            </tbody>
          </table>
      </x-admin-fieldset>

      {{-- Publishing --}}
      <x-admin-fieldset legend="{{ Lang::txt('JGLOBAL_FIELDSET_PUBLISHING') }}">

          <div class="admin-field">
            <label for="field-state" class="label">{{ Lang::txt('COM_DEVELOPER_FIELD_STATE') }}</label>
            <select name="fields[state]" id="field-state"
                    class="select select-bordered w-full">
              <option value="0" @selected($row->get('state') == 0)>{{ Lang::txt('JUNPUBLISHED') }}</option>
              <option value="1" @selected($row->get('state') == 1)>{{ Lang::txt('JPUBLISHED') }}</option>
              <option value="2" @selected($row->get('state') == 2)>{{ Lang::txt('JTRASHED') }}</option>
            </select>
          </div>

      </x-admin-fieldset>

      {{-- Team --}}
      <x-admin-fieldset legend="{{ Lang::txt('COM_DEVELOPER_FIELDSET_TEAM') }}">

          <div class="admin-field">
            <label for="acmembers" class="label">{{ Lang::txt('COM_DEVELOPER_FIELD_ADD_TEAM') }}</label>
            @php
              $currentTeam = [];
              foreach ($row->team()->rows() as $member) {
                  $profile = \Hubzero\User\User::oneOrNew($member->get('uidNumber'));
                  $currentTeam[] = $profile->get('name') . ' (' . $profile->get('id') . ')';
              }
              $teamList = implode(', ', $currentTeam);

              $mc = Event::trigger(
                  'hubzero.onGetMultiEntry',
                  [['members', 'team', 'acmembers', '', $teamList]]
              );
            @endphp
            @if(count($mc) > 0)
              {!! $mc[0] !!}
            @else
              <input type="text" name="team" id="acmembers"
                     class="input input-bordered w-full"
                     value="{{ $teamList }}" />
            @endif
            <p class="text-xs text-muted-foreground mt-1">{{ Lang::txt('COM_DEVELOPER_FIELD_ADD_TEAM_HINT') }}</p>
          </div>

      </x-admin-fieldset>
    @endif
  @endslot

  {{-- Hidden fields --}}
  <input type="hidden" name="fields[id]" value="{{ $row->get('id') }}" />
</x-admin-edit>
