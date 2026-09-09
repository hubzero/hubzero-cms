{{--
  OAuth authorization form — asks user to approve/deny app access.

  Variables from controller:
    $application  — Application model requesting authorization

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
$formAction   = Route::url('index.php?option=com_developer');
$appName      = $application->get('name');
$responseType = e(\Hubzero\Facades\Request::getWord('response_type', ''));
$redirectUri  = e(\Hubzero\Facades\Request::getString('redirect_uri', ''));
$state        = e(\Hubzero\Facades\Request::getCmd('state', ''));
@endphp

<x-page-container :title="Lang::txt('COM_DEVELOPER_API_OAUTH_AUTHORIZATION_NEEDED')">

  <div class="card bg-base-100 shadow-sm max-w-lg mx-auto">
    <div class="card-body">
      <p class="mb-4">
        {{ Lang::txt('COM_DEVELOPER_API_OAUTH_AUTHORIZATION_NEEDED_DESC', $appName) }}
      </p>

      <form action="{{ $formAction }}" id="oauth_form" method="post">
        <div class="flex gap-2">
          <button type="submit" name="authorize" value="1" class="btn btn-success">
            {{ Lang::txt('COM_DEVELOPER_API_OAUTH_AUTHORIZE') }}
          </button>
          <button type="submit" name="authorize" value="0" class="btn btn-ghost">
            {{ Lang::txt('COM_DEVELOPER_API_OAUTH_DENY') }}
          </button>
        </div>
        <input type="hidden" name="option" value="com_developer" />
        <input type="hidden" name="controller" value="oauth" />
        <input type="hidden" name="task" value="doauthorize" />
        <input type="hidden" name="client_id"
               value="{{ $application->get('client_id') }}" />
        <input type="hidden" name="response_type" value="{{ $responseType }}" />
        <input type="hidden" name="redirect_uri" value="{{ $redirectUri }}" />
        <input type="hidden" name="state" value="{{ $state }}" />
      </form>
    </div>
  </div>

</x-page-container>
