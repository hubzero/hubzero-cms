{{--
  Member edit — Account details tab

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $name       = $profile->get('name', '') ?: '';
  $surname    = $profile->get('surname', '') ?: '';
  $givenName  = $profile->get('givenName', '') ?: '';
  $middleName = $profile->middleName ?: '';

  if (!$surname) {
      $bits = explode(' ', $name);
      $surname = array_pop($bits);
      if (count($bits) >= 1) {
          $givenName = array_shift($bits);
      }
      if (count($bits) >= 1) {
          $middleName = implode(' ', $bits);
      }
  }

  $incomplete = false;
  $authenticator = 'hub';
  if (substr($profile->get('email', ''), -8) == '@invalid') {
      $authenticator = Lang::txt('COM_MEMBERS_UNKNOWN');
      if ($lnk = Hubzero\Auth\Link::find_by_id(abs($profile->get('username')))) {
          $domain = Hubzero\Auth\Domain::find_by_id($lnk->auth_domain_id);
          $authenticator = $domain->authenticator;
      }
      $incomplete = true;
  }
@endphp

<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
  {{-- Left column --}}
  <div class="lg:col-span-7">
    <x-admin-fieldset legend="{{ Lang::txt('Account Details') }}">
      <div class="admin-field">
        <label for="field_username" class="label">
          {{ Lang::txt('COM_MEMBERS_FIELD_USERNAME') }}
          <span class="text-error">*</span>
        </label>
        <input type="text"
               name="fields[username]"
               id="field_username"
               class="input input-bordered w-full"
               required
               value="{{ $profile->get('username') }}"
               @if($profile->get('id')) readonly @endif />
        <span class="label-text-alt text-muted-foreground">{{ Lang::txt('COM_MEMBERS_FIELD_USERNAME_HINT') }}</span>
      </div>

      <div class="admin-field">
        <label for="field_email" class="label">
          {{ Lang::txt('COM_MEMBERS_FIELD_EMAIL') }}
          <span class="text-error">*</span>
        </label>
        <input type="email"
               name="fields[email]"
               id="field_email"
               class="input input-bordered w-full validate-email"
               required
               value="{{ $profile->get('email') }}" />
      </div>

      <div class="divider text-sm">{{ Lang::txt('COM_MEMBERS_FIELD_NAME') }}</div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="admin-field">
          <label for="field-givenName" class="label">{{ Lang::txt('COM_MEMBERS_FIELD_FIRST_NAME') }}</label>
          <input type="text"
                 name="fields[givenName]"
                 id="field-givenName"
                 class="input input-bordered w-full"
                 value="{{ $givenName }}" />
        </div>

        <div class="admin-field">
          <label for="field-middleName" class="label">{{ Lang::txt('COM_MEMBERS_FIELD_MIDDLE_NAME') }}</label>
          <input type="text"
                 name="fields[middleName]"
                 id="field-middleName"
                 class="input input-bordered w-full"
                 value="{{ $middleName }}" />
        </div>

        <div class="admin-field">
          <label for="field-surname" class="label">{{ Lang::txt('COM_MEMBERS_FIELD_LAST_NAME') }}</label>
          <input type="text"
                 name="fields[surname]"
                 id="field-surname"
                 class="input input-bordered w-full"
                 value="{{ $surname }}" />
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="admin-field">
          <label for="field-access" class="label">{{ Lang::txt('COM_MEMBERS_FIELD_ACCESS_LEVEL') }}</label>
          <select name="fields[access]"
                  id="field-access"
                  class="select select-bordered w-full">
            {!! Html::select('options', Html::access('assetgroups'), 'value', 'text', $profile->get('access')) !!}
          </select>
        </div>
        <div class="admin-field">
          <label for="field-sendEmail" class="label">{{ Lang::txt('COM_MEMBERS_FIELD_MAIL_PREFERENCE') }}</label>
          <select name="fields[sendEmail]"
                  id="field-sendEmail"
                  class="select select-bordered w-full">
            <option value="-1" @selected($profile->get('sendEmail') == -1)>{{ Lang::txt('COM_MEMBERS_PROFILE_FORM_SELECT_FROM_LIST') }}</option>
            <option value="1" @selected($profile->get('sendEmail') == 1)>{{ Lang::txt('JYES') }}</option>
            <option value="0" @selected($profile->get('sendEmail') == 0)>{{ Lang::txt('JNO') }}</option>
          </select>
        </div>
      </div>

      <div class="admin-field">
        <label for="field-homeDirectory" class="label">{{ Lang::txt('COM_MEMBERS_FIELD_HOMEDIRECTORY') }}</label>
        <input type="text"
               name="fields[homeDirectory]"
               id="field-homeDirectory"
               class="input input-bordered w-full"
               value="{{ $profile->get('homeDirectory') }}" />
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="admin-field">
          <label for="field-loginShell" class="label">{{ Lang::txt('COM_MEMBERS_FIELD_LOGINSHELL') }}</label>
          <input type="text"
                 name="fields[loginShell]"
                 id="field-loginShell"
                 class="input input-bordered w-full"
                 value="{{ $profile->get('loginShell') }}" />
        </div>
        <div class="admin-field">
          <label for="field-ftpShell" class="label">{{ Lang::txt('COM_MEMBERS_FIELD_FTPSHELL') }}</label>
          <input type="text"
                 name="fields[ftpShell]"
                 id="field-ftpShell"
                 class="input input-bordered w-full"
                 value="{{ $profile->get('ftpShell') }}" />
        </div>
      </div>
    </x-admin-fieldset>

    <x-admin-fieldset legend="{{ Lang::txt('Assigned Access Groups') }}">
      <div id="user-groups">
        @php
          $groups = array();
          foreach ($profile->accessgroups()->rows() as $g) {
              $groups[] = $g->get('group_id');
          }
          $isSuperAdmin = User::authorise('core.admin');
          $grpDb = App::get('db');
          $grpQuery = $grpDb->getQuery()
              ->select('a.*')
              ->select('COUNT(DISTINCT b.id)', 'level')
              ->from('#__usergroups', 'a')
              ->joinRaw('#__usergroups AS b', 'a.lft > b.lft AND a.rgt < b.rgt', 'left')
              ->group('a.id')->group('a.title')->group('a.lft')->group('a.rgt')->group('a.parent_id')
              ->order('a.lft', 'asc');
          $grpDb->setQuery($grpQuery->toString());
          $allGroups = $grpDb->loadObjectList();
        @endphp
        <ul class="list-none p-0 m-0 space-y-0.5">
          @foreach($allGroups as $grp)
            @if($isSuperAdmin || !\Hubzero\Access\Access::checkGroup($grp->id, 'core.admin'))
              <li class="flex items-center gap-2">
                <input type="checkbox"
                       name="fields[accessgroups][]"
                       id="group_{{ $grp->id }}"
                       class="checkbox checkbox-sm"
                       value="{{ $grp->id }}"
                       @checked(in_array($grp->id, $groups)) />
                <label for="group_{{ $grp->id }}"
                       class="flex items-center cursor-pointer text-sm leading-snug">
                  @if($grp->level > 0)
                    <span class="text-faint-foreground text-xs">
                      {!! str_repeat('|&mdash;', $grp->level) !!}
                    </span>
                  @endif
                  {{ $grp->title }}
                </label>
              </li>
            @endif
          @endforeach
        </ul>
      </div>
    </x-admin-fieldset>
  </div>

  {{-- Right column --}}
  <div class="lg:col-span-5">
    <table class="admin-meta">
      <tbody>
        <tr>
          <th>{{ Lang::txt('COM_MEMBERS_FIELD_ID') }}</th>
          <td>
            {{ $profile->get('id') }}
            <input type="hidden" name="fields[id]" value="{{ $profile->get('id') }}" />
          </td>
        </tr>
        <tr>
          <th>{{ Lang::txt('COM_MEMBERS_FIELD_REGISTERIP') }}</th>
          <td>{{ $profile->get('registerIP') }}</td>
        </tr>
        <tr>
          <th>{{ Lang::txt('COM_MEMBERS_FIELD_REGISTERDATE') }}</th>
          <td>{{ $profile->get('registerDate') }}</td>
        </tr>
        <tr>
          <th>{{ Lang::txt('COM_MEMBERS_FIELD_LASTVISITDATE') }}</th>
          <td>
            @php
              $lastVisit = $profile->get('lastvisitDate');
            @endphp
            {{ (!$lastVisit || $lastVisit == '0000-00-00 00:00:00') ? Lang::txt('COM_MEMBERS_NEVER') : $lastVisit }}
          </td>
        </tr>
        <tr>
          <th>{{ Lang::txt('COM_MEMBERS_FIELD_MODIFIED') }}</th>
          <td>
            @php
              $modified = $profile->get('modifiedDate');
            @endphp
            {{ (!$modified || $modified == '0000-00-00 00:00:00') ? Lang::txt('COM_MEMBERS_NEVER') : $modified }}
          </td>
        </tr>
        @if($incomplete)
          <tr>
            <th>{{ Lang::txt('COM_MEMBERS_AUTHENTICATOR') }}</th>
            <td>{{ $authenticator }}</td>
          </tr>
          <tr>
            <th>{{ Lang::txt('COM_MEMBERS_AUTHENTICATOR_STATUS') }}</th>
            <td>{{ Lang::txt('COM_MEMBERS_INCOMPLETE') }}</td>
          </tr>
        @endif
      </tbody>
    </table>

    <x-admin-fieldset legend="{{ Lang::txt('COM_MEMBERS_STATUS') }}">
      <div class="admin-field">
        <label class="label">{{ Lang::txt('COM_MEMBERS_FIELD_USAGE_AGREEMENT') }}</label>
        <div class="flex gap-4">
          <label class="flex items-center gap-2 cursor-pointer">
            <input type="radio"
                   name="fields[usageAgreement]"
                   class="radio radio-sm"
                   value="0"
                   @checked($profile->get('usageAgreement') == 0) />
            <span>{{ Lang::txt('JNo') }}</span>
          </label>
          <label class="flex items-center gap-2 cursor-pointer">
            <input type="radio"
                   name="fields[usageAgreement]"
                   class="radio radio-sm"
                   value="1"
                   @checked($profile->get('usageAgreement') == 1) />
            <span>{{ Lang::txt('JYes') }}</span>
          </label>
        </div>
      </div>

      <div class="admin-field">
        <label class="label">{{ Lang::txt('Block this User') }}</label>
        <div class="flex gap-4">
          <label class="flex items-center gap-2 cursor-pointer">
            <input type="radio"
                   name="fields[block]"
                   class="radio radio-sm"
                   value="0"
                   @checked($profile->get('block') == 0) />
            <span>{{ Lang::txt('JNo') }}</span>
          </label>
          <label class="flex items-center gap-2 cursor-pointer">
            <input type="radio"
                   name="fields[block]"
                   class="radio radio-sm"
                   value="1"
                   @checked($profile->get('block') == 1) />
            <span>{{ Lang::txt('JYes') }}</span>
          </label>
        </div>
      </div>

      <div class="admin-field">
        <label for="field_approved" class="label">{{ Lang::txt('Approved User') }}</label>
        <select id="field_approved"
                name="fields[approved]"
                class="select select-bordered w-full">
          <option value="0" @selected($profile->get('approved') == 0)>{{ Lang::txt('Not approved') }}</option>
          <option value="1" @selected($profile->get('approved') == 1)>{{ Lang::txt('Manually approved') }}</option>
          <option value="2" @selected($profile->get('approved') == 2)>{{ Lang::txt('Automatically approved') }}</option>
        </select>
        <span class="label-text-alt text-muted-foreground">{{ Lang::txt('User approval status. Users not approved are as such because registration requires admin approval.') }}</span>
      </div>

      <div class="admin-field">
        @if($profile->get('email'))
          @if($profile->get('activation') == 1)
            <label class="flex items-center gap-2 cursor-pointer">
              <input type="checkbox"
                     name="activation"
                     id="activation"
                     class="checkbox checkbox-sm"
                     value="1"
                     checked />
              <span>{{ Lang::txt('COM_MEMBERS_FIELD_EMAIL_CONFIRMED') }}</span>
            </label>
          @elseif($profile->get('activation') == 2)
            <div class="badge badge-success">{{ Lang::txt('COM_MEMBERS_FIELD_EMAIL_GRANDFATHERED') }}</div>
            <input type="hidden" name="activation" id="activation" value="2" />
          @elseif($profile->get('activation') == 3)
            <div class="badge badge-info">{{ Lang::txt('COM_MEMBERS_FIELD_EMAIL_DOMAIN_SUPPLIED') }}</div>
            <input type="hidden" name="activation" id="activation" value="3" />
            @php
              $lnks = Hubzero\Auth\Link::find_by_user_id($profile->get('id'));
            @endphp
            @if($lnks)
              <div class="mt-2 space-y-1">
                @foreach($lnks as $lnk)
                  <div class="text-sm">
                    {{ Lang::txt('COM_MEMBERS_AUTHENTICATOR') }}:
                    <span class="badge badge-outline badge-sm">{{ $lnk['auth_domain_name'] }}</span>
                  </div>
                @endforeach
              </div>
            @endif
          @elseif($profile->get('activation') < 0)
            @if($profile->get('email'))
              <div class="alert alert-warning mb-2">
                <span>{{ Lang::txt('COM_MEMBERS_FIELD_EMAIL_AWAITING_CONFIRMATION') }}
                [code: {{ -$profile->get('activation') }}]</span>
              </div>
              <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox"
                       name="activation"
                       id="activation"
                       class="checkbox checkbox-sm"
                       value="1" />
                <span>{{ Lang::txt('COM_MEMBERS_FIELD_EMAIL_CONFIRM') }}</span>
              </label>
              @php
                $return = base64_encode(Route::url(
                    'index.php?option=' . $option
                    . '&controller=' . $controller
                    . '&id=' . $profile->get('id')
                    . '&task=edit&',
                    false,
                    true, false
                ));
                $confirmUrl = Route::url(
                    'index.php?option=' . $option
                    . '&controller=' . $controller
                    . '&id=' . $profile->get('id')
                    . '&task=resendConfirm&'
                    . Session::getFormToken() . '=1'
                    . '&return=' . $return, false
                );
              @endphp
              <a class="btn btn-sm btn-outline mt-2" href="{!! $confirmUrl !!}">
                {{ Lang::txt('COM_MEMBERS_RESEND_CONFIRM') }}
              </a>
            @else
              <span class="text-error">{{ Lang::txt('COM_MEMBERS_FIELD_EMAIL_NONE_ON_FILE') }}</span>
            @endif
          @else
            <span class="text-warning">[{{ Lang::txt('COM_MEMBERS_FIELD_EMAIL_UNKNOWN_STATUS') }}]</span>
            <label class="flex items-center gap-2 cursor-pointer mt-1">
              <input type="checkbox"
                     name="activation"
                     id="activation"
                     class="checkbox checkbox-sm"
                     value="1" />
              <span>{{ Lang::txt('COM_MEMBERS_FIELD_EMAIL_CONFIRM') }}</span>
            </label>
          @endif
        @else
          <span class="text-error">{{ Lang::txt('COM_MEMBERS_FIELD_EMAIL_NONE_ON_FILE') }}</span><br />
          <label class="flex items-center gap-2 cursor-pointer mt-1">
            <input type="checkbox"
                   name="activation"
                   id="activation"
                   class="checkbox checkbox-sm"
                   value="1" />
            <span>{{ Lang::txt('COM_MEMBERS_FIELD_EMAIL_CONFIRM') }}</span>
          </label>
        @endif
      </div>

      @if($profile->get('id') && Plugin::isEnabled('system', 'spamjail'))
        <div class="admin-field">
          <label for="field-reputation" class="label">{{ Lang::txt('COM_MEMBERS_SPAM_COUNT') }}</label>
          <div class="flex gap-2">
            <input type="text"
                   name="spam_count"
                   id="field-reputation"
                   class="input input-bordered flex-1"
                   value="{{ $profile->reputation->get('spam_count', 0) }}" />
            <a class="btn btn-sm btn-outline"
               href="#field-reputation"
               data-action="reset-spam-count">{{ Lang::txt('COM_MEMBERS_RESET') }}</a>
          </div>
          @php
            $spamCount = $profile->reputation->get('spam_count', 0);
            $spamLimit = Plugin::params('system', 'spamjail')->get('user_count', 10);
          @endphp
          @if($spamCount > $spamLimit)
            <p class="text-warning text-sm mt-1">{{ Lang::txt('COM_MEMBERS_SPAM_COUNT_EXCEEDED') }}</p>
          @endif
          <span class="label-text-alt text-muted-foreground">{{ Lang::txt('COM_MEMBERS_SPAM_COUNT_HINT') }}</span>
        </div>
      @endif
    </x-admin-fieldset>

    @php
      $data = new Hubzero\Config\Registry();
      $data->set('params', $profile->params->toArray());
      $form = new Hubzero\Form\Form('fields', array('control' => 'fields'));
      $form->load(Hubzero\Form\Form::getXML(
          Component::path('com_members') . DS . 'models' . DS . 'forms' . DS . 'user.xml',
          true
      ));
      $form->bind($data);
      $fieldsets = $form->getFieldsets();
    @endphp

    @foreach($fieldsets as $fieldset)
      @if($fieldset->name == 'user_details')
        @continue
      @endif
      <details class="collapse collapse-arrow bg-base-100 border border-base-300 mb-2">
        <summary class="collapse-title font-medium">{{ Lang::txt($fieldset->label) }}</summary>
        <div class="collapse-content">
          @foreach($form->getFieldset($fieldset->name) as $field)
            @if($field->hidden)
              {!! $field->input !!}
            @else
              <div class="admin-field">
                {!! $field->label !!}
                {!! $field->input !!}
              </div>
            @endif
          @endforeach
        </div>
      </details>
    @endforeach
  </div>
</div>
