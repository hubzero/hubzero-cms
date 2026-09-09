{{--
  Member Account — confirm token form.

  Variables from plugin (onMembers):
    $option        — component option
    $id            — member ID
    $notifications — notification messages array

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;

  $__view->css()
      ->css('providers.css', 'com_login')
      ->js()
      ->js('jquery.hoverIntent', 'system');

  $formUrl = Route::url('index.php?option=' . $option . '&id=' . $id . '&active=account&task=confirmtoken');
@endphp

<h3 class="text-lg font-semibold mb-4">{{ Lang::txt('PLG_MEMBERS_ACCOUNT_ENTER_CONFIRMATION_TOKEN') }}</h3>

@if (!empty($notifications))
  @foreach ($notifications as $notification)
    <div class="alert {{ $notification['type'] === 'error' ? 'alert-error' : 'alert-info' }} mb-4" role="alert">
      {{ $notification['message'] }}
    </div>
  @endforeach
@endif

<div class="card bg-base-100 shadow-sm max-w-lg">
  <div class="card-body">
    <form action="{{ $formUrl }}" method="post" class="space-y-4">
      <fieldset>
        <legend class="text-sm font-medium mb-2">{{ Lang::txt('PLG_MEMBERS_ACCOUNT_ENTER_CONFIRMATION_TOKEN') }}</legend>

        <div class="form-control w-full">
          <label class="label" for="token">
            <span class="label-text">{{ Lang::txt('PLG_MEMBERS_ACCOUNT_TOKEN') }}</span>
          </label>
          <input id="token" name="token" type="text" class="input input-bordered w-full" required />
        </div>
      </fieldset>

      <div class="flex gap-2">
        <button type="submit" name="change" class="btn btn-primary">
          {{ Lang::txt('PLG_MEMBERS_ACCOUNT_SUBMIT') }}
        </button>
        <button type="reset" class="btn btn-ghost">
          {{ Lang::txt('PLG_MEMBERS_ACCOUNT_CANCEL') }}
        </button>
      </div>

      {!! Html::input('token') !!}
    </form>
  </div>
</div>
