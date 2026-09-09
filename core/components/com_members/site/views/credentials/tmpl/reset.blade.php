{{--
  Password reset request form. Allows users to initiate a password reset
  by providing their username to receive a verification token via email.

  Variables from controller:
    (none — this view has no dynamic data beyond Lang strings)

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}

<x-page-container :title="Lang::txt('COM_MEMBERS_CREDENTIALS_RESET')">
	@slot('sidebar')
		<x-sidebar-card>
			<p class="info">
				{!! Lang::txt(
					'Forgot your username? Go <a href="%s">here</a> to recover it.',
					Route::url('index.php?option=com_members&task=remind')
				) !!}
			</p>
		</x-sidebar-card>
	@endslot

	<form action="{{ Route::url('index.php?option=com_members&controller=credentials&task=resetting') }}" method="post" name="hubForm" id="hubForm">
		<fieldset>
			<legend class="text-lg font-semibold">{{ Lang::txt('COM_MEMBERS_CREDENTIALS_EMAIL_VERIFICATION_TOKEN') }}</legend>

			<p>{{ Lang::txt('COM_MEMBERS_CREDENTIALS_RESET_PASSWORD_DESCRIPTION') }}</p>

			<label class="label" for="username">
				<span class="label-text">
					{{ Lang::txt('COM_MEMBERS_CREDENTIALS_RESET_PASSWORD_LABEL') }}:
					<span class="text-error text-sm">{{ Lang::txt('COM_MEMBERS_CREDENTIALS_REQUIRED') }}</span>
				</span>
			</label>
			<input type="text" name="username" id="username" class="input input-bordered w-full" />
		</fieldset>

		<div class="clear"></div>

		<p class="submit">
			<button type="submit" class="btn btn-primary">{{ Lang::txt('Submit') }}</button>
		</p>

		{!! Html::input('token') !!}
	</form>
</x-page-container>
