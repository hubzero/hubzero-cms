<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
?>

<h2 class="doc-section-header" id="oauth">Authentication (OAuth2)</h2>

<p>OAuth2 is a protocol that lets external applications request authorization to private details in a user’s account without getting their password. This is preferred over Basic Authentication because tokens can be limited to specific types of data, and can be revoked by users at any time.</p>

<p>All developers need to register a <a href="<?php echo Route::url('index.php?option=com_developer&controller=applications&task=new'); ?>">developer application</a> before getting started. A registered OAuth application is assigned a unique client ID and client secret. The client secret should not be shared.</p>

<p><strong>Note, there are a number of different grant types you can use to authenticate a user via OAuth. Please read each type below to determine which type works best for your application.</strong> </p>

<div class="doc-section" id="oauth-authorizationcode">
	<h3>Web Application Flow</h3>
	<p>This grant type is used for a typical web application, usually called "3 legged OAuth" The user is on your application and you send them to an authorization url (on this site) which asks them authorize the application to access the users data on their behalf.</p>

	<h4>1. Redirect to Authorization URL</h4>
	<pre><code class="http">GET /developer/oauth/authorize</code></pre>

	<h4>Parameters</h4>
	<table>
		<thead>
			<tr>
				<th>Name</th>
				<th>Type</th>
				<th>Description</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>client_id</td>
				<td>string</td>
				<td><span class="required">Required.</span> The client ID you received from your application when you registered your application.</td>
			</tr>
			<tr>
				<td>redirect_uri</td>
				<td>string</td>
				<td><span class="required">Required.</span> The URL in your application where users will be sent after authorization. Must be one of the URLs you entered when registering your application.</td>
			</tr>
			<tr>
				<td>state</td>
				<td>string</td>
				<td><span class="required">Required.</span> An unguessable random string. It is used to protect against cross-site request forgery attacks.</td>
			</tr>
			<tr>
				<td>response_type</td>
				<td>string</td>
				<td><span class="required">Required.</span> At this time the only available option is "code"</td>
			</tr>
		</tbody>
	</table>

	<h4>2. HUB redirects back to your site</h4>
	<p>If the user accepts your request, the HUB will redirect back to your site with a temporary code in a <code>code</code> parameter as well as the state you provided in the previous step in a <code>state</code> parameter. If the states don’t match, the request has been created by a third party and the process should be aborted.</p>

	<p>Exchange this for an access token:</p>
	<pre><code class="http">POST /developer/oauth/token</code></pre>

	<h4>Parameters</h4>
	<table>
		<thead>
			<tr>
				<th>Name</th>
				<th>Type</th>
				<th>Description</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>client_id</td>
				<td>string</td>
				<td><span class="required">Required.</span> The client ID you received from your application when you registered your application.</td>
			</tr>
			<tr>
				<td>redirect_uri</td>
				<td>string</td>
				<td><span class="required">Required.</span> Must be the URL you gave in Step 1.</td>
			</tr>
			<tr>
				<td>grant_type</td>
				<td>string</td>
				<td><span class="required">Required.</span> Exchanging an authorization code for an access token, you must use the grant type "authorization_code".</td>
			</tr>
			<tr>
				<td>code</td>
				<td>string</td>
				<td><span class="required">Required.</span> The code you received as a response to Step 1.</td>
			</tr>
		</tbody>
	</table>

	<h4>Response</h4>
	<p>The response will be returned as JSON and takes the following form:</p>
	<pre><code class="json">{
	"access_token": "ac1cb855725c2eb8d5a3b29e70842fc3b5017293",
	"expires_in": 14400,
	"token_type": "Bearer",
	"scope": null,
	"refresh_token": "57c96d8372f7281572cb8063f0c9ad561ba8e903"
}</code></pre>
</div>

<div class="doc-section" id="oauth-usercredentials">
	<h3>User Credentials Flow</h3>
	<p>This grant type is usually only used with trusted clients, just as a desktop or mobile application. In this grant type the users must enter their username and password which is sent and exchanged for an access token.</p>

	<h4>Request an access token</h4>
	<pre><code class="http">POST /developer/oauth/token</code></pre>

	<h4>Parameters</h4>
	<table>
		<thead>
			<tr>
				<th>Name</th>
				<th>Type</th>
				<th>Description</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>client_id</td>
				<td>string</td>
				<td><span class="required">Required.</span> The client ID you received from your application when you registered your application.</td>
			</tr>
			<tr>
				<td>client_secret</td>
				<td>string</td>
				<td><span class="required">Required.</span> The client Secret you received from your application when you registered your application.</td>
			</tr>
			<tr>
				<td>grant_type</td>
				<td>string</td>
				<td><span class="required">Required.</span> "password"</td>
			</tr>
			<tr>
				<td>username</td>
				<td>string</td>
				<td><span class="required">Required.</span> The user's username.</td>
			</tr>
			<tr>
				<td>password</td>
				<td>string</td>
				<td><span class="required">Required.</span> The user's password.</td>
			</tr>
		</tbody>
	</table>

	<h4>Response</h4>
	<p>The response will be returned as JSON and takes the following form:</p>
	<pre><code class="json">{
	"access_token": "ac1cb855725c2eb8d5a3b29e70842fc3b5017293",
	"expires_in": 14400,
	"token_type": "Bearer",
	"scope": null,
	"refresh_token": "57c96d8372f7281572cb8063f0c9ad561ba8e903"
}</code></pre>
</div>

<div class="doc-section" id="oauth-refreshtoken">
	<h3>Refresh Token Flow</h3>
	<p>Refresh tokens are used to extend the length of an applications granted access token. Since each access token has a limited lifetime (couple hours), refresh tokens are issued with each access token request to extend their lifetime. Using a refresh token, allows you to "refresh" the access token after it has expired to get a new access token.</p>

	<p>Although refresh tokens last much long (couple days, weeks, etc) they do expire eventually, so a user who hasnt actively used your application for longer then that period will be forced to login anyways.</p>

	<h4>Request an access token</h4>
	<pre><code class="http">POST /developer/oauth/token</code></pre>

	<h4>Parameters</h4>
	<table>
		<thead>
			<tr>
				<th>Name</th>
				<th>Type</th>
				<th>Description</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>client_id</td>
				<td>string</td>
				<td><span class="required">Required.</span> The client ID you received from your application when you registered your application.</td>
			</tr>
			<tr>
				<td>client_secret</td>
				<td>string</td>
				<td><span class="required">Required.</span> The client Secret you received from your application when you registered your application.</td>
			</tr>
			<tr>
				<td>grant_type</td>
				<td>string</td>
				<td><span class="required">Required.</span> "refresh_token"</td>
			</tr>
			<tr>
				<td>refresh_token</td>
				<td>string</td>
				<td><span class="required">Required.</span> The refresh token you stored upon getting your original access token.</td>
			</tr>
		</tbody>
	</table>
	<h4>Response</h4>
	<p>The response will be returned as JSON and takes the following form:</p>
	<pre><code class="json">{
	"access_token": "ac1cb855725c2eb8d5a3b29e70842fc3b5017293",
	"expires_in": 14400,
	"token_type": "Bearer",
	"scope": null,
	"refresh_token": "57c96d8372f7281572cb8063f0c9ad561ba8e903"
}</code></pre>
</div>

<div class="doc-section" id="oauth-sessiontoken">
	<h3>Session Token Flow</h3>
	<p>This grant type is used for internal HUB use only. It allows a web developer to create a client side application that communicates to the api via AJAX. <strong>This grant type will only work for a user with an active session (logged in user) from within the HUB in a component, module, plugin or template.</strong></p>

	<h4>Request an access token</h4>
	<pre><code class="http">POST /developer/oauth/token</code></pre>

	<h4>Parameters</h4>
	<table>
		<thead>
			<tr>
				<th>Name</th>
				<th>Type</th>
				<th>Description</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>grant_type</td>
				<td>string</td>
				<td><span class="required">Required.</span> "session"</td>
			</tr>
		</tbody>
	</table>
	<h4>Response</h4>
	<p>The response will be returned as JSON and takes the following form:</p>
	<pre><code class="json">{
	"access_token": "ac1cb855725c2eb8d5a3b29e70842fc3b5017293",
	"expires_in": 14400,
	"token_type": "Bearer",
	"scope": null
}</code></pre>
</div>

<div class="doc-section" id="oauth-toolsessiontoken">
	<h3>Tool Session Token Flow</h3>
	<p>This grant type is used for internal HUB use only. It allows for a tool session container to access the API. <strong>This grant type will only work from within an active tool container.</strong></p>

	<h4>Request an access token</h4>
	<pre><code class="http">POST /developer/oauth/token</code></pre>

	<h4>Parameters</h4>
	<table>
		<thead>
			<tr>
				<th>Name</th>
				<th>Type</th>
				<th>Description</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>grant_type</td>
				<td>string</td>
				<td><span class="required">Required.</span> "tool"</td>
			</tr>
			<tr>
				<td>sessionnum</td>
				<td>string</td>
				<td><span class="required">Required.</span> The Session ID number. This can typically be found in the resources file in the session data folder. This can be send as POST or HEADER parameter.</td>
			</tr>
			<tr>
				<td>sessiontoken</td>
				<td>string</td>
				<td><span class="required">Required.</span> The Session Token. This can typically be found in the resources file in the session data folder. This can be send as POST or HEADER parameter.</td>
			</tr>
		</tbody>
	</table>
	<h4>Response</h4>
	<p>The response will be returned as JSON and takes the following form:</p>
	<pre><code class="json">{
	"access_token": "ac1cb855725c2eb8d5a3b29e70842fc3b5017293",
	"expires_in": 14400,
	"token_type": "Bearer",
	"scope": null
}</code></pre>
</div>

<div class="doc-section" id="oauth-authenticating">
	<h3>Authenticating</h3>
	<p>The API uses OAuth2 to authenticate incoming requests. After obtaining your access token you must supply it with each request in the authorization header:</p>

	<pre><code class="nohighlight">"Authorization: Bearer {ACCESS_TOKEN}"</code></pre>
</div>