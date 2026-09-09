{{--
  Email verification token confirmation form. Allows users to enter the
  verification token they received via email to proceed with password reset.

  Variables from controller:
    (none — this view has no dynamic data beyond Lang strings)

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}

<x-page-container :title="Lang::txt('COM_MEMBERS_CREDENTIALS_VERIFY')">
	<form action="{{ Route::url('index.php?option=com_members&controller=credentials&task=verifying') }}" method="post" name="hubForm" id="hubForm">
		<fieldset>
			<legend class="text-lg font-semibold">{{ Lang::txt('COM_MEMBERS_CREDENTIALS_CONFIRM_VERIFICATION_TOKEN') }}</legend>

			<p>{{ Lang::txt('COM_MEMBERS_CREDENTIALS_VERIFICATION_TOKEN_DESCRIPTION') }}</p>

			<label class="label" for="token">
				<span class="label-text">
					{{ Lang::txt('COM_MEMBERS_CREDENTIALS_VERIFICATION_TOKEN_LABEL') }}:
					<span class="text-error text-sm">{{ Lang::txt('COM_MEMBERS_CREDENTIALS_REQUIRED') }}</span>
				</span>
			</label>
			<input type="text" name="token" id="token" class="input input-bordered w-full" />
		</fieldset>

		<div class="clear"></div>

		<p class="submit">
			<button type="submit" class="btn btn-primary">{{ Lang::txt('Submit') }}</button>
		</p>

		{!! Html::input('token') !!}
	</form>
</x-page-container>
