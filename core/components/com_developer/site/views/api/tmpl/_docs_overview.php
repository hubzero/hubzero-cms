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

<h2 class="doc-section-header" id="overview">Overview</h2>

<p class="doc-section-emphasis">Please note that you must register a <a href="<?php echo Route::url('index.php?option=com_developer&controller=applications&task=new'); ?>">developer application</a> and authenticate with OAuth when making requests. Before doing so, be sure to read our <a href="/about/terms">Terms &amp; Guidelines</a> to learn how the API may be used.</p>

<div class="doc-section" id="overview-schema">
	<h3>Schema</h3>
	<p>All API access is over HTTPS, and accessed from the <?php echo $this->url; ?>.</p>

	<p>All timestamps are returned in ISO 8601 format:</p>
	<code class="block">YYYY-MM-DDTHH:MM:SSZ</code>
</div>

<div class="doc-section" id="overview-errormessages">
	<h3>Errors</h3>
	<p>Below is an example of a standard error message returned from the API.</p>
	<p>The HTTP response status in this example below would be <code>422 Validation Failed</code>. It is included in the error response body since some clients have difficulty pulling the exact status message, vs the generic message.</p>
	<pre><code class="json">{
	"code"    : 422,
	"message" : "Validation Failed",
	"errors"  : [	
		{
			"field"   : "cn",
			"message" : "Group cn cannot be empty."
		},
		{
			"field"   : "cn",
			"message" : "Invalid group ID. You may be using characters that are not allowed."
		}
	]
}</code></pre>
</div>

<div class="doc-section" id="overview-httpverbs">
	<h3>HTTP Verbs</h3>
	<p>Where possible, the API strives to use appropriate HTTP verbs for each action.</p>
	<table>
		<thead>
			<tr>
				<th>Verb</th>
				<th>Description</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>GET</td>
				<td>Used for retrieving resources, either a list or single resource</td>
			</tr>
			<tr>
				<td>POST</td>
				<td>Used for creating resources.</td>
			</tr>
			<tr>
				<td>PUT</td>
				<td>Used for updating resources, or performing custom actions</td>
			</tr>
			<tr>
				<td>DELETE</td>
				<td>Used for deleting resources.</td>
			</tr>
		</tbody>
	</table>
</div>

<div class="doc-section" id="overview-versioning">
	<h3>Versioning</h3>
	<p>All endpoints through the API are versioned. You can supply the version in the request three different ways</p>

	<ol>
		<li>In the URL: <br /><pre><code class="http">/v1.3/groups</code></pre></li>
		<li>Query string parameter: <br /><pre><code class="http">/groups?version=1.3</code></pre> or <pre><code class="http">/groups?v=1.3</code></pre></li>
		<li>Custom Accept Header: <br /><pre><code class="http">application/vnd.<?php echo $this->base; ?>.v1.3</code></pre></li>
	</ol>
</div>

<div class="doc-section" id="overview-ratelimiting">
	<h3>Rate Limiting</h3>
	<p>You can make up to 60 requests per minute, with a hard limit of 10,000 per day. For requests using OAuth, the rate limit is for each application and user combination. For unauthenticated requests, the rate limit is for the requesting IP address.</p>
	<p>You can check the returned HTTP headers of any API request to see your current per minute rate limit status:</p>
	<pre><code class="http">GET /groups/12345</code></pre>
	<br />
	<pre><code class="http">HTTP/1.1 200 OK
Status: 200 OK
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 59
X-RateLimit-Reset: 1392321600</code></pre>

	<p>The headers tell you everything you need to know about your current rate limit status:</p>
	<table>
		<thead>
			<tr>
				<th>Header</th>
				<th>Description</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>X-RateLimit-Limit</td>
				<td>The maximum number of requests that the consumer is permitted to make per minute.</td>
			</tr>
			<tr>
				<td>X-RateLimit-Remaining</td>
				<td>The number of requests remaining in the current rate limit window.</td>
			</tr>
			<tr>
				<td>X-RateLimit-Reset</td>
				<td>The time at which the current rate limit window resets in UTC epoch seconds.</td>
			</tr>
		</tbody>
	</table>

	<p>Once you go over the rate limit you will receive an error response:</p>
	<pre><code class="http">HTTP/1.1 429 Too Many Requests
Status: 429 Too Many Requests
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 0
X-RateLimit-Reset: 1392321600</code></pre>
<br />
	<pre><code class="json">{
	"code"    : 429,
	"message" : "Too Many Requests",
	"errors"  : []
}</code></pre>
	<p>** If you are exceeding your rate limit, you can likely fix the issue by caching API responses. If you’re caching and still exceeding your rate limit, please contact us to request a higher rate limit for your application.</p>
</div>

<div class="doc-section" id="overview-jsonp">
	<h3>JSON-P</h3>
	<p>You can send a <code>callback</code> parameter to any GET call to have the results wrapped in a JSON function. This is typically used when browsers want to embed content in web pages by getting around cross domain issues. The response includes the same data output as the regular API, plus the relevant HTTP Header information.</p>

	<pre><code class="http">GET /groups/?callback=FooBar</code></pre>
	<br />
	<pre><code class="json">FooBar([
	{
		"gidNumber":   "1234",
		"cn":          "testgroup",
		"description": "Test Group",
		"created":     "2015-01-29T19:58:07Z",
		"created_by":  "1000"
	},
	...
]);</code></pre>
<p>You can write a JavaScript handler to process the callback like this:</p>
<pre><code class="javascript">function FooBar(groupsData)
{
	console.log(groupsData)
}</code></pre>
</div>

<div class="doc-section" id="overview-expanding">
	<h3>Expanding Objects</h3>
	<p>You can send an <code>expand</code> parameter to any GET call to have results expanded into full objects. This can be extremely useful and avoid having to make multiple requests.</p>

	<pre><code class="http">GET /groups/12345?expand=created_by</code></pre>
	<br />
	<pre><code class="json">{
	"gidNumber"   : "12345",
	"description" : "Test Group",
	"public_desc" : "Test Group Description",
	"logo"        : "/core/components/com_groups/site/assets/img/group_default_logo.png",
	"created"     : "2015-01-29T19:58:07Z",
	"created_by"  : {
		"uidNumber"    : "1000",
		"name"         : "John Doe",
		"organization" : "Hubzero",
		"url"          : "https://hubzero.org",
		"phone"        : "123-123-1234",
		"bio"          : "Donec ullamcorper nulla non metus auctor fringilla. Donec sed odio dui. Maecenas faucibus mollis interdum."
	}
}</code></pre>
</div>