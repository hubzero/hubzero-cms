{{--
  API documentation sidebar navigation menu.

  Variables (set via $__view->view('_menu')->set(...)):
    $documentation  — Array from API Doc Generator
    $active         — Currently active endpoint section (or '' for docs)
    $version        — Active API version string

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
$base = 'index.php?option=' . $option . '&controller=' . $controller;

$docsUrl     = Route::url($base . '&task=docs');
$schemaUrl   = Route::url($base . '&task=docs#overview-schema');
$errorUrl    = Route::url($base . '&task=docs#overview-errormessages');
$httpUrl     = Route::url($base . '&task=docs#overview-httpverbs');
$versionUrl  = Route::url($base . '&task=docs#overview-versioning');
$rateUrl     = Route::url($base . '&task=docs#overview-ratelimiting');
$jsonpUrl    = Route::url($base . '&task=docs#overview-jsonp');
$expandUrl   = Route::url($base . '&task=docs#overview-expanding');

$oauthUrl    = Route::url($base . '&task=docs#oauth');
$authCodeUrl = Route::url($base . '&task=docs#oauth-authorizationcode');
$userCredUrl = Route::url($base . '&task=docs#oauth-usercredentials');
$refreshUrl  = Route::url($base . '&task=docs#oauth-refreshtoken');
$sessionUrl  = Route::url($base . '&task=docs#oauth-sessiontoken');
$toolUrl     = Route::url($base . '&task=docs#oauth-toolsessiontoken');
$authUrl     = Route::url($base . '&task=docs#oauth-authenticating');

$done = [];

// Which accordion section is open?
// If viewing an endpoint page, open "endpoints"; otherwise open "using"
$openSection = $active ? 'endpoints' : 'using';
@endphp

<nav class="api-toc">
  {{-- Using the API --}}
  <details name="api-nav" @if($openSection === 'using') open @endif>
    <summary class="api-toc-header">{{ Lang::txt('Using the API') }}</summary>
    <ul class="api-toc-list">
      <li class="api-toc-group active">
        <a class="api-toc-group-link" href="{{ $docsUrl }}">{{ Lang::txt('Overview') }}</a>
        <ul class="api-toc-sublist">
          <li><a href="{{ $schemaUrl }}">{{ Lang::txt('Schema') }}</a></li>
          <li><a href="{{ $errorUrl }}">{{ Lang::txt('Error Messages') }}</a></li>
          <li><a href="{{ $httpUrl }}">{{ Lang::txt('HTTP Verbs') }}</a></li>
          <li><a href="{{ $versionUrl }}">{{ Lang::txt('Versioning') }}</a></li>
          <li><a href="{{ $rateUrl }}">{{ Lang::txt('Rate Limiting') }}</a></li>
          <li><a href="{{ $jsonpUrl }}">{{ Lang::txt('JSON-P') }}</a></li>
          <li><a href="{{ $expandUrl }}">{{ Lang::txt('Expanding Objects') }}</a></li>
        </ul>
      </li>
      <li class="api-toc-group">
        <a class="api-toc-group-link" href="{{ $oauthUrl }}">{{ Lang::txt('Authentication (OAuth2)') }}</a>
        <ul class="api-toc-sublist">
          <li><a href="{{ $authCodeUrl }}">{{ Lang::txt('Web Application Flow') }}</a></li>
          <li><a href="{{ $userCredUrl }}">{{ Lang::txt('User Credentials Flow') }}</a></li>
          <li><a href="{{ $refreshUrl }}">{{ Lang::txt('Refresh Token Flow') }}</a></li>
          <li><a href="{{ $sessionUrl }}">{{ Lang::txt('Session Token Flow') }}</a></li>
          <li><a href="{{ $toolUrl }}">{{ Lang::txt('Tool Session Token Flow') }}</a></li>
          <li><a href="{{ $authUrl }}">{{ Lang::txt('Using the Token') }}</a></li>
        </ul>
      </li>
    </ul>
  </details>

  {{-- API Endpoints --}}
  <details name="api-nav" @if($openSection === 'endpoints') open @endif>
    <summary class="api-toc-header">{{ Lang::txt('API Endpoints') }}</summary>
    <ul class="api-toc-list">
      @foreach ($documentation['sections'] as $component => $endpoints)
        @php
        $compUrl = Route::url($base . '&task=endpoint&active=' . $component);
        $isActive = ($component == $active);
        @endphp
        <li @class(['api-toc-group', 'active' => $isActive])>
          <a class="api-toc-group-link" href="{{ $compUrl }}">{{ ucfirst($component) }}</a>
          @if ($isActive && count($endpoints))
            <ul class="api-toc-sublist">
              @foreach ($endpoints as $endpoint)
                @php
                $key = $endpoint['_metadata']['component']
                    . '-' . $endpoint['_metadata']['method'];
                if (in_array($key, $done)) {
                    continue;
                }
                $done[] = $key;
                $epUrl = Route::url(
                    $base . '&task=endpoint&active='
                    . $component . '#' . $key
                );
                @endphp
                <li><a href="{{ $epUrl }}">{{ $endpoint['name'] }}</a></li>
              @endforeach
            </ul>
          @endif
        </li>
      @endforeach
    </ul>
  </details>
</nav>

<script>
// Exclusive accordion fallback for browsers without <details name=""> support
document.querySelectorAll('.api-toc details').forEach(function(detail) {
  detail.addEventListener('toggle', function() {
    if (detail.open) {
      document.querySelectorAll('.api-toc details').forEach(function(other) {
        if (other !== detail) other.open = false;
      });
    }
  });
});
</script>
