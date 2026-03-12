{{--
  Member edit — Password tab

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $__view->css('password.css');
@endphp

<div class="max-w-2xl">
  <x-admin-fieldset legend="{{ Lang::txt('COM_MEMBERS_FIELD_PASSWORD') }}">
    @if(is_object($password))
      <div class="admin-field">
        <label class="label">{{ Lang::txt('COM_MEMBERS_PASSWORD_CURRENT') }}</label>
        @if($profile->get('password'))
          <input type="text"
                 class="input input-bordered w-full font-mono text-sm"
                 disabled
                 value="{{ $profile->get('password') }}" />
        @else
          <input type="text"
                 class="input input-bordered w-full"
                 disabled
                 placeholder="{{ Lang::txt('no local password set') }}" />
        @endif
      </div>
    @endif

    <div class="admin-field">
      <label for="newpass" class="label">{{ Lang::txt('COM_MEMBERS_PASSWORD_NEW') }}</label>
      @php
        $checkpassUrl = Route::url(
            'index.php?option=' . $option
            . '&controller=' . $controller
            . '&task=checkpass&no_html=1',
            false, false
        );
        $dataValues = 'user_id=' . $profile->get('id', 0)
            . '&option=' . $option
            . '&controller=' . $controller
            . '&task=checkpass&no_html=1';
      @endphp
      <input type="password"
             name="newpass"
             id="newpass"
             class="input input-bordered w-full"
             value=""
             autocomplete="off"
             data-href="{!! $checkpassUrl !!}"
             data-values="{{ $dataValues }}" />
      <div class="alert alert-warning mt-2">
        <span>{!! Lang::txt('COM_MEMBERS_PASSWORD_NEW_WARNING') !!}</span>
      </div>

      @if(count($password_rules) > 0)
        <div class="mt-3">
          <p class="font-medium text-sm mb-1">{{ Lang::txt('COM_MEMBERS_PASSWORD_RULES') }}:</p>
          <ul id="passrules" class="passrules list-disc list-inside text-sm space-y-0.5">
            @foreach($password_rules as $rule)
              @if(!empty($rule))
                @if($validated && is_array($validated) && in_array($rule, $validated))
                  <li class="pass-error text-error">{{ $rule }}</li>
                @elseif($validated)
                  <li class="pass-passed text-success">{{ $rule }}</li>
                @else
                  <li class="pass-empty text-muted-foreground">{{ $rule }}</li>
                @endif
              @endif
            @endforeach
          </ul>
        </div>
      @endif
    </div>

    @if(is_object($password))
      <div class="divider"></div>

      <div class="admin-field">
        <label class="label">{{ Lang::txt('COM_MEMBERS_PASSWORD_SHADOW_LAST_CHANGE') }}</label>
        @if($password->get('shadowLastChange'))
          @php
            $shadowLastChange = $password->get('shadowLastChange') * 86400;
            $daysAgo = intval((time() / 86400) - ($shadowLastChange / 86400));
          @endphp
          <p class="text-sm">
            {{ date('Y-m-d', $shadowLastChange) }}
            <span class="text-muted-foreground">({{ $password->get('shadowLastChange') }}) &mdash; {{ $daysAgo }} days ago</span>
          </p>
        @else
          <p class="text-sm text-muted-foreground">{{ Lang::txt('COM_MEMBERS_NEVER') }}</p>
        @endif
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="admin-field">
          <label for="field-shadowMax" class="label">{{ Lang::txt('COM_MEMBERS_PASSWORD_SHADOW_MAX') }}</label>
          <input type="text"
                 name="shadowMax"
                 id="field-shadowMax"
                 class="input input-bordered w-full"
                 value="{{ $password->get('shadowMax') }}" />
        </div>

        <div class="admin-field">
          <label for="field-shadowWarning" class="label">{{ Lang::txt('COM_MEMBERS_PASSWORD_SHADOW_WARNING') }}</label>
          <input type="text"
                 name="shadowWarning"
                 id="field-shadowWarning"
                 class="input input-bordered w-full"
                 value="{{ $password->get('shadowWarning') }}" />
        </div>
      </div>

      <div class="admin-field">
        <label for="field-shadowExpire" class="label">{{ Lang::txt('COM_MEMBERS_PASSWORD_SHADOW_EXPIRE') }}</label>
        <input type="text"
               name="shadowExpire"
               id="field-shadowExpire"
               class="input input-bordered w-full"
               value="{{ $password->get('shadowExpire') }}" />
        <span class="label-text-alt text-muted-foreground">{{ Lang::txt('COM_MEMBERS_PASSWORD_SHADOW_EXPIRE_HINT') }}</span>
      </div>

      <div class="admin-field">
        <label class="flex items-center gap-2 cursor-pointer">
          <input type="checkbox"
                 name="resetSecret"
                 id="cbResetSecret"
                 class="checkbox checkbox-sm"
                 value="1" />
          <span>{{ Lang::txt('COM_MEMBERS_PASSWORD_RESET_SECRET') }}</span>
        </label>
        <span class="label-text-alt text-muted-foreground">{{ Lang::txt('COM_MEMBERS_PASSWORD_RESET_SECRET_HINT') }}</span>
      </div>
    @endif
  </x-admin-fieldset>
</div>
