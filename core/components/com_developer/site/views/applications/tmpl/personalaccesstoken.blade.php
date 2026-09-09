{{--
  Personal access token display sub-template — shown after creating a new PAT.

  Variables (set by parent view.blade.php):
    $application  — Application model
    $accesstoken  — The new access token string

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}

<div class="card bg-base-100 shadow-sm">
  <div class="card-body">
    <h3 class="card-title text-lg">
      {{ Lang::txt('COM_DEVELOPER_API_APPLICATION_TOKENS_PERSONAL_APPLICATION_TOKEN') }}
    </h3>
    <p>
      {{ Lang::txt('COM_DEVELOPER_API_APPLICATION_TOKENS_NEW_PERSONAL_APPLICATION_TOKEN') }}:
    </p>
    <div class="bg-base-200 rounded-lg p-3 my-2">
      <code class="text-sm font-bold break-all select-all">{{ $accesstoken }}</code>
    </div>
    <div class="alert alert-warning mt-2">
      {{ Lang::txt('COM_DEVELOPER_API_APPLICATION_TOKENS_MAKE_SURE_PERSONAL_APPLICATION_TOKEN') }}
    </div>
  </div>
</div>
