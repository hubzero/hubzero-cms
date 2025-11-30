<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
?>

<h2 class="doc-section-header" id="overview">Overview</h2>

<p class="doc-section-emphasis">Please note that you must register a <a
        href="<?php echo Route::url('index.php?option=com_developer&controller=applications&task=new'); ?>">developer
        application</a> and authenticate with OAuth when making requests. Before doing so, be sure to read our <a
        href="/about/terms">Terms &amp; Guidelines</a> to learn how the API may be used.</p>

<div class="doc-section collapsed" id="overview-schema">
    <h3 class="endpoint-header collapsed">Schema</h3>
    <div class="endpoint-content collapsed">
        <p>All API access is over HTTPS, and accessed from <code><?php echo $this->url; ?></code>.</p>

        <p>All data is sent and received as JSON.</p>

        <p>All timestamps are returned in ISO 8601 format:</p>
        <pre><code>YYYY-MM-DDTHH:MM:SSZ</code></pre>
    </div>
</div>

<div class="doc-section collapsed" id="overview-errormessages">
    <h3 class="endpoint-header collapsed">Errors</h3>
    <div class="endpoint-content collapsed">
        <p>Below is an example of a standard error message returned from the API.</p>
        <p>The HTTP response status in this example would be <code>422 Validation Failed</code>. It is included in the
            error response body since some clients have difficulty pulling the exact status message.</p>
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
</div>

<div class="doc-section collapsed" id="overview-httpverbs">
    <h3 class="endpoint-header collapsed">HTTP Verbs</h3>
    <div class="endpoint-content collapsed">
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
                    <td><span class="http-method get">GET</span></td>
                    <td>Used for retrieving resources, either a list or single resource</td>
                </tr>
                <tr>
                    <td><span class="http-method post">POST</span></td>
                    <td>Used for creating resources</td>
                </tr>
                <tr>
                    <td><span class="http-method put">PUT</span></td>
                    <td>Used for updating resources, or performing custom actions</td>
                </tr>
                <tr>
                    <td><span class="http-method delete">DELETE</span></td>
                    <td>Used for deleting resources</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="doc-section collapsed" id="overview-versioning">
    <h3 class="endpoint-header collapsed">Versioning</h3>
    <div class="endpoint-content collapsed">
        <p>All endpoints through the API are versioned. You can supply the version in the request three different ways:
        </p>

        <ol>
            <li>
                <strong>In the URL:</strong>
                <pre><code class="http">/v1.3/groups</code></pre>
            </li>
            <li>
                <strong>Query string parameter:</strong>
                <pre><code class="http">/groups?version=1.3</code></pre>
                or
                <pre><code class="http">/groups?v=1.3</code></pre>
            </li>
            <li>
                <strong>Custom Accept Header:</strong>
                <pre><code class="http">application/vnd.<?php echo $this->base; ?>.v1.3</code></pre>
            </li>
        </ol>
    </div>
</div>

<div class="doc-section collapsed" id="overview-ratelimiting">
    <h3 class="endpoint-header collapsed">Rate Limiting</h3>
    <div class="endpoint-content collapsed">
        <p>You can make up to <strong>60 requests per minute</strong>, with a hard limit of <strong>10,000 per
                day</strong>. For requests using OAuth, the rate limit is for each application and user combination. For
            unauthenticated requests, the rate limit is for the requesting IP address.</p>

        <h4>Checking Your Rate Limit Status</h4>
        <p>You can check the returned HTTP headers of any API request to see your current per minute rate limit status:
        </p>
        <pre><code class="http">GET /groups/12345</code></pre>
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
                    <td><code>X-RateLimit-Limit</code></td>
                    <td>The maximum number of requests that the consumer is permitted to make per minute</td>
                </tr>
                <tr>
                    <td><code>X-RateLimit-Remaining</code></td>
                    <td>The number of requests remaining in the current rate limit window</td>
                </tr>
                <tr>
                    <td><code>X-RateLimit-Reset</code></td>
                    <td>The time at which the current rate limit window resets in UTC epoch seconds</td>
                </tr>
            </tbody>
        </table>

        <h4>Rate Limit Exceeded</h4>
        <p>Once you go over the rate limit you will receive an error response:</p>
        <pre><code class="http">HTTP/1.1 429 Too Many Requests
Status: 429 Too Many Requests
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 0
X-RateLimit-Reset: 1392321600</code></pre>
        <pre><code class="json">{
	"code"    : 429,
	"message" : "Too Many Requests",
	"errors"  : []
}</code></pre>
        <p class="info"><strong>Note:</strong> If you are exceeding your rate limit, you can likely fix the issue by
            caching API responses. If you're caching and still exceeding your rate limit, please contact us to request a
            higher rate limit for your application.</p>
    </div>
</div>

<div class="doc-section collapsed" id="overview-jsonp">
    <h3 class="endpoint-header collapsed">JSON-P</h3>
    <div class="endpoint-content collapsed">
        <p>You can send a <code>callback</code> parameter to any GET call to have the results wrapped in a JSON
            function. This is typically used when browsers want to embed content in web pages by getting around cross
            domain issues. The response includes the same data output as the regular API, plus the relevant HTTP Header
            information.</p>

        <pre><code class="http">GET /groups/?callback=FooBar</code></pre>
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
</div>

<div class="doc-section collapsed" id="overview-expanding">
    <h3 class="endpoint-header collapsed">Expanding Objects</h3>
    <div class="endpoint-content collapsed">
        <p>You can send an <code>expand</code> parameter to any GET call to have results expanded into full objects.
            This can be extremely useful and avoid having to make multiple requests.</p>

        <pre><code class="http">GET /groups/12345?expand=created_by</code></pre>
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
		"bio"          : "Donec ullamcorper nulla non metus auctor fringilla."
	}
}</code></pre>
    </div>
</div>