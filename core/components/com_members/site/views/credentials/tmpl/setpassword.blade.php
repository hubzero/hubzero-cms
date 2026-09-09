{{--
  Set new password form. Allows users to enter a new password after
  completing the verification step of the password reset flow.

  Variables from controller:
    $password_rules — array of password rule description strings

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}

@php
	$__view->js('setpassword')
	       ->css('setpassword');
@endphp

<x-page-container :title="Lang::txt('COM_MEMBERS_CREDENTIALS_SET_PASSWORD')">
	<div class="alert alert-error hidden" id="error-message"></div>

	<form action="{{ Route::url('index.php?option=com_members&controller=credentials&task=settingpassword') }}" method="post" name="hubForm" id="hubForm">
		<fieldset>
			<legend class="text-lg font-semibold">{{ Lang::txt('COM_MEMBERS_CREDENTIALS_NEW_PASSWORD') }}</legend>

			<p>{{ Lang::txt('COM_MEMBERS_CREDENTIALS_PASSWORD_DESCRIPTION') }}</p>

			<label class="label" for="newpass">
				<span class="label-text">
					{{ Lang::txt('COM_MEMBERS_CREDENTIALS_PASSWORD1_LABEL') }}:
					<span class="text-error text-sm">{{ Lang::txt('COM_MEMBERS_CREDENTIALS_REQUIRED') }}</span>
				</span>
			</label>
			<input type="password" name="password1" id="newpass" tabindex="1" class="input input-bordered w-full" />

			<label class="label" for="password2">
				<span class="label-text">
					{{ Lang::txt('COM_MEMBERS_CREDENTIALS_PASSWORD2_LABEL') }}:
					<span class="text-error text-sm">{{ Lang::txt('COM_MEMBERS_CREDENTIALS_REQUIRED') }}</span>
				</span>
			</label>
			<input type="password" name="password2" id="password2" tabindex="2" class="input input-bordered w-full" />

			@if (count($password_rules) > 0)
				<ul id="passrules">
					@foreach ($password_rules as $rule)
						@if (!empty($rule))
							<li class="empty">{{ $rule }}</li>
						@endif
					@endforeach
				</ul>
			@endif
		</fieldset>

		<div class="clear"></div>

		<input type="hidden" id="pass_no_html" name="no_html" value="0" />

		<p class="submit">
			<button type="submit" id="password-change-save" class="btn btn-primary">{{ Lang::txt('Submit') }}</button>
		</p>

		{!! Html::input('token') !!}
	</form>
</x-page-container>
