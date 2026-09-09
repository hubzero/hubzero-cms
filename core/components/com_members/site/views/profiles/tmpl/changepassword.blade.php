{{--
  Change password form. Allows users to update their account password
  by providing their current password and choosing a new one.

  Variables from controller:
    $title          — string   Page title
    $option         — string   Component option (com_members)
    $profile        — object   User profile object
    $change         — bool     Whether a change was submitted
    $oldpass        — string   Submitted current password
    $newpass        — string   Submitted new password
    $newpass2       — string   Submitted new password confirmation
    $password_rules — array    Password requirement descriptions
    $validated      — array|string  Validation error messages

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    use Hubzero\Facades\Lang;
    use Hubzero\Facades\Route;

    $__view->css()
           ->js('changepassword.js');

    $accountUrl = Route::url('index.php?option=' . $option . '&id=' . $profile->get('id'));
    $formAction = Route::url($profile->link() . '&task=changepassword', true);

    $passMatch = ($change && $oldpass
        && !\Hubzero\User\Password::passwordMatches($profile->get('id'), $oldpass, true));

    $newpassError = $change && (!$newpass || $newpass != $newpass2);
    $newpass2Error = $change && (!$newpass2 || $newpass != $newpass2);
@endphp

<x-page-container :title="$title">
    @slot('actions')
        <a class="btn btn-ghost btn-sm" href="{{ $accountUrl }}">{{ Lang::txt('COM_MEMBERS_MYACCOUNT') }}</a>
    @endslot

    @if ($__view->getError())
        <div class="alert alert-error" id="errors">{{ $__view->getError() }}</div>
    @endif

    <form action="{{ $formAction }}" method="post" id="hubForm">
        <fieldset>
            <legend>{{ Lang::txt('COM_MEMBERS_CHANGEPASSWORD_CHOOSE') }}</legend>

            <label for="oldpass">
                {{ Lang::txt('COM_MEMBERS_FIELD_CURRENT_PASS') }}
            </label>
            <input
                name="oldpass"
                id="oldpass"
                type="password"
                value=""
                class="input input-bordered w-full{{ $passMatch ? ' input-error' : '' }}" />

            @if ($change && !$oldpass)
                <div class="alert alert-error">{{ Lang::txt('COM_MEMBERS_PASS_BLANK') }}</div>
            @endif
            @if ($passMatch)
                <div class="alert alert-error">{{ Lang::txt('COM_MEMBERS_PASS_INCORRECT') }}</div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="newpass">
                        {{ Lang::txt('COM_MEMBERS_FIELD_NEW_PASS') }}
                    </label>
                    <input
                        name="newpass"
                        id="newpass"
                        type="password"
                        value=""
                        class="input input-bordered w-full{{ $newpassError ? ' input-error' : '' }}" />

                    @if ($change && !$newpass)
                        <span class="text-error text-sm">{{ Lang::txt('COM_MEMBERS_PASS_BLANK') }}</span>
                    @endif
                </div>
                <div>
                    <label for="newpass2">
                        {{ Lang::txt('COM_MEMBERS_FIELD_PASS_CONFIRM') }}
                    </label>
                    <input
                        name="newpass2"
                        id="newpass2"
                        type="password"
                        value=""
                        class="input input-bordered w-full{{ $newpass2Error ? ' input-error' : '' }}" />

                    @if ($change && !$newpass2)
                        <span class="text-error text-sm">{{ Lang::txt('COM_MEMBERS_PASS_MUST_CONFIRM') }}</span>
                    @endif
                    @if ($change && $newpass && $newpass2 && ($newpass != $newpass2))
                        <span class="text-error text-sm">{{ Lang::txt('COM_MEMBERS_PASS_NEW_CONFIRMATION_MISMATCH') }}</span>
                    @endif
                </div>
            </div>

            @if (count($password_rules) > 0)
                <ul id="passrules">
                    @foreach ($password_rules as $rule)
                        @if (!empty($rule))
                            @php
                                $err = is_array($validated) ? in_array($rule, $validated) : false;
                                $ruleClass = $err ? 'error' : 'empty';
                            @endphp
                            <li class="{{ $ruleClass }}">{{ $rule }}</li>
                        @endif
                    @endforeach
                    @if (is_array($validated))
                        @foreach ($validated as $msg)
                            @if (!in_array($msg, $password_rules))
                                <li class="error">{{ $msg }}</li>
                            @endif
                        @endforeach
                    @endif
                </ul>
            @endif
        </fieldset>

        <p class="submit">
            {!! Html::input('token') !!}
            <input type="hidden" id="pass_no_html" name="no_html" value="0" />
            <input type="hidden" name="change" value="1" />
            <button
                type="submit"
                name="submit"
                id="password-change-save"
                class="btn btn-success">{{ Lang::txt('COM_MEMBERS_CHANGEPASSWORD') }}</button>
        </p>
    </form>
</x-page-container>
