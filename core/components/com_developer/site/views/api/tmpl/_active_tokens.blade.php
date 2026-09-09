{{--
  Active session tokens section for the API docs page.

  Variables (set via $__view->view('_active_tokens')->set(...)):
    $tokens  — Collection of user's active access tokens

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@if (empty($tokens) || count($tokens) === 0)
  @php return; @endphp
@endif

<div class="collapse collapse-arrow border border-success bg-success/5 mb-6" id="active-session-tokens">
  <input type="checkbox" checked="checked" />
  <div class="collapse-title text-lg font-semibold">Active Session Tokens</div>
  <div class="collapse-content">
    <div class="alert alert-success mb-3">
      <strong>You are currently authenticated!</strong>
      Below are your active session tokens that you can use to make API requests.
    </div>

    <div class="space-y-2">
      @foreach ($tokens as $token)
        <div class="flex items-center justify-between bg-base-200 rounded-lg px-3 py-2">
          <code class="text-sm break-all">{{ e($token->access_token) }}</code>
          <span class="text-sm text-base-content/60 whitespace-nowrap ml-3">
            expires {{ \Hubzero\Utility\Date::of($token->expires)->toLocal('M d, Y g:i a') }}
          </span>
        </div>
      @endforeach
    </div>

    <h4 class="font-semibold mt-4 mb-2">Using Your Token</h4>
    <p>Include this token in the Authorization header of your API requests:</p>
    <pre class="bg-base-200 rounded-lg p-3 mt-2 text-sm"><code>Authorization: Bearer {{ e($tokens->first()->access_token) }}</code></pre>
  </div>
</div>
