{{--
  OAuth2 authentication documentation section.

  Variables (set via $__view->view('_docs_oauth')->set(...)):
    $url  — API base URL (e.g. https://example.com/api)

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
$newAppUrl = Route::url('index.php?option=com_developer&controller=applications&task=new');
@endphp

<div class="bg-primary text-primary-content rounded-lg px-6 py-4 mt-8 mb-6">
  <h2 class="text-2xl font-bold italic" id="oauth">Authentication (OAuth2)</h2>
</div>

<div class="mb-6 space-y-3">
  <p>OAuth2 is a protocol that lets external applications request authorization to
    private details in a user's account without getting their password. This is
    preferred over Basic Authentication because tokens can be limited to specific
    types of data, and can be revoked by users at any time.</p>

  <p>All developers need to register a
    <a class="link link-primary" href="{{ $newAppUrl }}">developer application</a>
    before getting started. A registered OAuth application is assigned a unique client
    ID and client secret. The client secret should not be shared.</p>

  <p><strong>Note, there are a number of different grant types you can use to
    authenticate a user via OAuth. Please read each type below to determine which
    type works best for your application.</strong></p>
</div>

{{-- Web Application Flow --}}
<div class="collapse collapse-arrow border border-base-300 mb-2" id="oauth-authorizationcode">
  <input type="checkbox" />
  <div class="collapse-title text-xl font-semibold">Web Application Flow</div>
  <div class="collapse-content space-y-3">
    <p>This grant type is used for a typical web application, usually called
      "3 legged OAuth" The user is on your application and you send them to an
      authorization url (on this site) which asks them authorize the application
      to access the users data on their behalf.</p>

    <h4 class="font-semibold mt-2">1. Redirect to Authorization URL</h4>
    <pre class="bg-base-200 rounded-lg p-3 text-sm"><code>GET /developer/oauth/authorize</code></pre>

    <h4 class="font-semibold">Parameters</h4>
    @include('com_developer::site.views.api.tmpl._param_table', ['params' => [
        ['client_id', 'string', true, 'The client ID you received from your application when you registered your application.'],
        ['redirect_uri', 'string', true, 'The URL in your application where users will be sent after authorization. Must be one of the URLs you entered when registering your application.'],
        ['state', 'string', true, 'An unguessable random string. It is used to protect against cross-site request forgery attacks.'],
        ['response_type', 'string', true, 'At this time the only available option is "code"'],
    ]])

    <h4 class="font-semibold mt-2">2. HUB redirects back to your site</h4>
    <p>If the user accepts your request, the HUB will redirect back to your site
      with a temporary code in a <code class="bg-base-200 px-1 rounded">code</code>
      parameter as well as the state you provided in the previous step in a
      <code class="bg-base-200 px-1 rounded">state</code> parameter. If the states
      don't match, the request has been created by a third party and the process
      should be aborted.</p>

    <p>Exchange this for an access token:</p>
    <pre class="bg-base-200 rounded-lg p-3 text-sm"><code>POST /developer/oauth/token</code></pre>

    <h4 class="font-semibold">Parameters</h4>
    @include('com_developer::site.views.api.tmpl._param_table', ['params' => [
        ['client_id', 'string', true, 'The client ID you received from your application when you registered your application.'],
        ['redirect_uri', 'string', true, 'Must be the URL you gave in Step 1.'],
        ['grant_type', 'string', true, 'Exchanging an authorization code for an access token, you must use the grant type "authorization_code".'],
        ['code', 'string', true, 'The code you received as a response to Step 1.'],
    ]])

    <h4 class="font-semibold mt-2">Response</h4>
    <p>The response will be returned as JSON and takes the following form:</p>
    <pre class="bg-base-200 rounded-lg p-3 text-sm"><code>{
    "access_token": "ac1cb855725c2eb8d5a3b29e70842fc3b5017293",
    "expires_in": 14400,
    "token_type": "Bearer",
    "scope": null,
    "refresh_token": "57c96d8372f7281572cb8063f0c9ad561ba8e903"
}</code></pre>
  </div>
</div>

{{-- User Credentials Flow --}}
<div class="collapse collapse-arrow border border-base-300 mb-2" id="oauth-usercredentials">
  <input type="checkbox" />
  <div class="collapse-title text-xl font-semibold">User Credentials Flow</div>
  <div class="collapse-content space-y-3">
    <p>This grant type is usually only used with trusted clients, just as a desktop
      or mobile application. In this grant type the users must enter their username
      and password which is sent and exchanged for an access token.</p>

    <h4 class="font-semibold">Request an access token</h4>
    <pre class="bg-base-200 rounded-lg p-3 text-sm"><code>POST /developer/oauth/token</code></pre>

    <h4 class="font-semibold">Parameters</h4>
    @include('com_developer::site.views.api.tmpl._param_table', ['params' => [
        ['client_id', 'string', true, 'The client ID you received from your application when you registered your application.'],
        ['client_secret', 'string', true, 'The client Secret you received from your application when you registered your application.'],
        ['grant_type', 'string', true, '"password"'],
        ['username', 'string', true, "The user's username."],
        ['password', 'string', true, "The user's password."],
    ]])

    <h4 class="font-semibold mt-2">Response</h4>
    <p>The response will be returned as JSON and takes the following form:</p>
    <pre class="bg-base-200 rounded-lg p-3 text-sm"><code>{
    "access_token": "ac1cb855725c2eb8d5a3b29e70842fc3b5017293",
    "expires_in": 14400,
    "token_type": "Bearer",
    "scope": null,
    "refresh_token": "57c96d8372f7281572cb8063f0c9ad561ba8e903"
}</code></pre>
  </div>
</div>

{{-- Refresh Token Flow --}}
<div class="collapse collapse-arrow border border-base-300 mb-2" id="oauth-refreshtoken">
  <input type="checkbox" />
  <div class="collapse-title text-xl font-semibold">Refresh Token Flow</div>
  <div class="collapse-content space-y-3">
    <p>Refresh tokens are used to extend the length of an applications granted
      access token. Since each access token has a limited lifetime (couple hours),
      refresh tokens are issued with each access token request to extend their
      lifetime. Using a refresh token, allows you to "refresh" the access token
      after it has expired to get a new access token.</p>

    <p>Although refresh tokens last much longer (couple days, weeks, etc) they do
      expire eventually, so a user who hasn't actively used your application for
      longer than that period will be forced to login anyways.</p>

    <h4 class="font-semibold">Request an access token</h4>
    <pre class="bg-base-200 rounded-lg p-3 text-sm"><code>POST /developer/oauth/token</code></pre>

    <h4 class="font-semibold">Parameters</h4>
    @include('com_developer::site.views.api.tmpl._param_table', ['params' => [
        ['client_id', 'string', true, 'The client ID you received from your application when you registered your application.'],
        ['client_secret', 'string', true, 'The client Secret you received from your application when you registered your application.'],
        ['grant_type', 'string', true, '"refresh_token"'],
        ['refresh_token', 'string', true, 'The refresh token you stored upon getting your original access token.'],
    ]])

    <h4 class="font-semibold mt-2">Response</h4>
    <p>The response will be returned as JSON and takes the following form:</p>
    <pre class="bg-base-200 rounded-lg p-3 text-sm"><code>{
    "access_token": "ac1cb855725c2eb8d5a3b29e70842fc3b5017293",
    "expires_in": 14400,
    "token_type": "Bearer",
    "scope": null,
    "refresh_token": "57c96d8372f7281572cb8063f0c9ad561ba8e903"
}</code></pre>
  </div>
</div>

{{-- Session Token Flow --}}
<div class="collapse collapse-arrow border border-base-300 mb-2" id="oauth-sessiontoken">
  <input type="checkbox" />
  <div class="collapse-title text-xl font-semibold">Session Token Flow</div>
  <div class="collapse-content space-y-3">
    <p>This grant type is used for internal HUB use only. It allows a web developer
      to create a client side application that communicates to the api via AJAX.
      <strong>This grant type will only work for a user with an active session
      (logged in user) from within the HUB in a component, module, plugin or
      template.</strong></p>

    <h4 class="font-semibold">Request an access token</h4>
    <pre class="bg-base-200 rounded-lg p-3 text-sm"><code>POST /developer/oauth/token</code></pre>

    <h4 class="font-semibold">Parameters</h4>
    @include('com_developer::site.views.api.tmpl._param_table', ['params' => [
        ['grant_type', 'string', true, '"session"'],
    ]])

    <h4 class="font-semibold mt-2">Response</h4>
    <p>The response will be returned as JSON and takes the following form:</p>
    <pre class="bg-base-200 rounded-lg p-3 text-sm"><code>{
    "access_token": "ac1cb855725c2eb8d5a3b29e70842fc3b5017293",
    "expires_in": 14400,
    "token_type": "Bearer",
    "scope": null
}</code></pre>
  </div>
</div>

{{-- Tool Session Token Flow --}}
<div class="collapse collapse-arrow border border-base-300 mb-2" id="oauth-toolsessiontoken">
  <input type="checkbox" />
  <div class="collapse-title text-xl font-semibold">Tool Session Token Flow</div>
  <div class="collapse-content space-y-3">
    <p>This grant type is used for internal HUB use only. It allows for a tool
      session container to access the API. <strong>This grant type will only work
      from within an active tool container.</strong></p>

    <h4 class="font-semibold">Request an access token</h4>
    <pre class="bg-base-200 rounded-lg p-3 text-sm"><code>POST /developer/oauth/token</code></pre>

    <h4 class="font-semibold">Parameters</h4>
    @include('com_developer::site.views.api.tmpl._param_table', ['params' => [
        ['grant_type', 'string', true, '"tool"'],
        ['sessionnum', 'string', true, 'The Session ID number. This can typically be found in the resources file in the session data folder. This can be sent as POST or HEADER parameter.'],
        ['sessiontoken', 'string', true, 'The Session Token. This can typically be found in the resources file in the session data folder. This can be sent as POST or HEADER parameter.'],
    ]])

    <h4 class="font-semibold mt-2">Response</h4>
    <p>The response will be returned as JSON and takes the following form:</p>
    <pre class="bg-base-200 rounded-lg p-3 text-sm"><code>{
    "access_token": "ac1cb855725c2eb8d5a3b29e70842fc3b5017293",
    "expires_in": 14400,
    "token_type": "Bearer",
    "scope": null
}</code></pre>
  </div>
</div>

{{-- Authenticating --}}
<div class="collapse collapse-arrow border border-base-300 mb-2" id="oauth-authenticating">
  <input type="checkbox" />
  <div class="collapse-title text-xl font-semibold">Using the Token</div>
  <div class="collapse-content space-y-3">
    <p>The API uses OAuth2 to authenticate incoming requests. After obtaining your
      access token you must supply it with each request in the authorization header:</p>

    <pre class="bg-base-200 rounded-lg p-3 text-sm"><code>"Authorization: Bearer {ACCESS_TOKEN}"</code></pre>
  </div>
</div>
