{{--
  Username reminder form. Allows users to recover their username(s) by
  providing the email address associated with their account.

  Variables from controller:
    (none — this view has no dynamic data beyond Lang strings)

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}

@php
	$resetUrl = Route::url('index.php?option=com_members&task=reset');
@endphp

<x-page-container :title="Lang::txt('COM_MEMBERS_CREDENTIALS_REMIND')">
	@slot('sidebar')
		<x-sidebar-card>
			<p class="info">
				If you already know your username, and only need your password reset,
				<a href="{{ $resetUrl }}">go here now</a>.
			</p>
		</x-sidebar-card>
	@endslot

	<form action="{{ Route::url('index.php?option=com_members&controller=credentials&task=reminding') }}" method="post" name="hubForm" id="hubForm">
		<fieldset>
			<legend class="text-lg font-semibold">Recover Username(s)</legend>

			<p>{{ Lang::txt('COM_MEMBERS_CREDENTIALS_REMIND_EMAIL_DESCRIPTION') }}</p>

			<label class="label" for="email">
				<span class="label-text">
					{{ Lang::txt('COM_MEMBERS_CREDENTIALS_REMIND_EMAIL_LABEL') }}:
					<span class="text-error text-sm">{{ Lang::txt('COM_MEMBERS_CREDENTIALS_REQUIRED') }}</span>
				</span>
			</label>
			<input type="text" name="email" id="email" class="input input-bordered w-full" />

			<div class="card bg-base-200 mt-4">
				<div class="card-body">
					<h4>What if I have also lost my password?</h4>
					<p>Fill out this form to retrieve your username(s). Then go to the
						<a href="{{ $resetUrl }}">password reset page</a>.
					</p>

					<h4>What if I have multiple accounts?</h4>
					<p>All accounts registered to your email address will be located, and you will be given a list of all of those usernames.</p>

					<h4>What if this cannot find my account?</h4>
					<p>It is possible you registered under a different email address. Please try any other email addresses you have.</p>
				</div>
			</div>
		</fieldset>

		<div class="clear"></div>

		<p class="submit">
			<button type="submit" class="btn btn-primary">{{ Lang::txt('Submit') }}</button>
		</p>

		{!! Html::input('token') !!}
	</form>
</x-page-container>
