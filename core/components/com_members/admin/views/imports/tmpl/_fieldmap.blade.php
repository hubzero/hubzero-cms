{{--
  Member Import — Field mapping partial

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@if($import->get('id'))
  <x-admin-fieldset legend="{{ Lang::txt('COM_MEMBERS_IMPORT_EDIT_FIELDSET_MAPPING') }}">
      <div class="bg-base-100 rounded-box border border-base-300 overflow-x-auto">
        <table class="admin-table">
          <thead>
            <tr>
              <th>{{ Lang::txt('COM_MEMBERS_IMPORT_EDIT_COL_FIELD_COLUMN') }}</th>
              <th>{{ Lang::txt('COM_MEMBERS_IMPORT_EDIT_COL_FIELD_MEMBER') }}</th>
            </tr>
          </thead>
          <tbody>
            @foreach($import->fields() as $mapping)
              <tr @unless($mapping['field']) class="field-unknown" @endunless>
                <td>
                  <label for="mapping-{{ $mapping['name'] }}">
                    {{ $mapping['label'] }}
                  </label>
                </td>
                <td>
                  <input type="hidden"
                         name="mapping[{{ $mapping['name'] }}][name]"
                         value="{{ $mapping['name'] }}" />
                  <input type="hidden"
                         name="mapping[{{ $mapping['name'] }}][label]"
                         value="{{ $mapping['label'] }}" />
                  <select name="mapping[{{ $mapping['name'] }}][field]"
                          id="mapping-{{ $mapping['name'] }}"
                          class="select select-bordered w-full">
                    <option value="">{{ Lang::txt('COM_MEMBERS_UNKNOWN') }}</option>
                    <optgroup label="{{ Lang::txt('COM_MEMBERS_IMPORT_FIELDS_ACCOUNT') }}">
                      <option value="id" @selected($mapping['field'] == 'id')>id</option>
                      <option value="username" @selected($mapping['field'] == 'username')>username</option>
                      <option value="password" @selected($mapping['field'] == 'password')>password</option>
                      <option value="email" @selected($mapping['field'] == 'email')>email</option>
                      <option value="activation" @selected($mapping['field'] == 'activation')>activation</option>
                      <option value="sendEmail" @selected($mapping['field'] == 'sendEmail')>sendEmail</option>
                      <option value="usageAgreement" @selected($mapping['field'] == 'usageAgreement')>usageAgreement</option>
                      <option value="note" @selected($mapping['field'] == 'note')>note</option>
                      <option value="homeDirectory" @selected($mapping['field'] == 'homeDirectory')>homeDirectory</option>
                      <option value="modifiedDate" @selected($mapping['field'] == 'modifiedDate')>modifiedDate</option>
                      <option value="block" @selected($mapping['field'] == 'block')>block</option>
                      <option value="approved" @selected($mapping['field'] == 'approved')>approved</option>
                      <option value="loginShell" @selected($mapping['field'] == 'loginShell')>loginShell</option>
                      <option value="ftpShell" @selected($mapping['field'] == 'ftpShell')>ftpShell</option>
                      <option value="groups" @selected($mapping['field'] == 'groups')>groups</option>
                      <option value="projects" @selected($mapping['field'] == 'projects')>projects</option>
                      <option value="access" @selected($mapping['field'] == 'access')>access</option>
                    </optgroup>
                    <optgroup label="{{ Lang::txt('COM_MEMBERS_IMPORT_FIELDS_REGISTER') }}">
                      <option value="registerIP" @selected($mapping['field'] == 'registerIP')>registerIP</option>
                      <option value="registerHost" @selected($mapping['field'] == 'registerHost')>registerHost</option>
                      <option value="registerDate" @selected($mapping['field'] == 'registerDate')>registerDate</option>
                    </optgroup>
                    <optgroup label="{{ Lang::txt('COM_MEMBERS_IMPORT_FIELDS_NAME') }}">
                      <option value="name" @selected($mapping['field'] == 'name')>name</option>
                      <option value="givenName" @selected($mapping['field'] == 'givenName')>givenName</option>
                      <option value="middleName" @selected($mapping['field'] == 'middleName')>middleName</option>
                      <option value="surname" @selected($mapping['field'] == 'surname')>surname</option>
                    </optgroup>
                    <optgroup label="{{ Lang::txt('COM_MEMBERS_IMPORT_FIELDS_PROFILE') }}">
                      @php
                        $profileFields = \Components\Members\Models\Profile\Field::all()
                            ->ordered()
                            ->rows();
                      @endphp
                      @foreach($profileFields as $field)
                        @php $fieldName = $field->get('name'); @endphp
                        <option value="{{ $fieldName }}" @selected($mapping['field'] == $fieldName)>{{ $fieldName }}</option>
                      @endforeach
                    </optgroup>
                  </select>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
  </x-admin-fieldset>
@endif
