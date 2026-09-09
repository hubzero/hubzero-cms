{{--
  API overview documentation section.

  Variables (set via $__view->view('_docs_overview')->set(...)):
    $url   — API base URL (e.g. https://example.com/api)
    $base  — Host subdomain (e.g. "dev" from dev.example.com)

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
$newAppUrl = Route::url('index.php?option=com_developer&controller=applications&task=new');
@endphp

<div class="bg-primary text-primary-content rounded-lg px-6 py-4 mb-6">
  <h2 class="text-2xl font-bold italic" id="overview">Overview</h2>
</div>

<p class="border-l-4 border-primary pl-4 mb-6">Please note that you must register a
  <a class="link link-primary" href="{{ $newAppUrl }}">developer application</a> and
  authenticate with OAuth when making requests. Before doing so, be sure to read our
  <a class="link link-primary" href="/about/terms">Terms &amp; Guidelines</a> to learn
  how the API may be used.</p>

{{-- Schema --}}
<div class="collapse collapse-arrow border border-base-300 mb-2" id="overview-schema">
  <input type="checkbox" />
  <div class="collapse-title text-xl font-semibold">Schema</div>
  <div class="collapse-content">
    <p>All API access is over HTTPS, and accessed from <code class="bg-base-200 px-1 rounded">{{ $url }}</code>.</p>
    <p class="mt-2">All data is sent and received as JSON.</p>
    <p class="mt-2">All timestamps are returned in ISO 8601 format:</p>
    <pre class="bg-base-200 rounded-lg p-3 mt-2"><code>YYYY-MM-DDTHH:MM:SSZ</code></pre>
  </div>
</div>

{{-- Errors --}}
<div class="collapse collapse-arrow border border-base-300 mb-2" id="overview-errormessages">
  <input type="checkbox" />
  <div class="collapse-title text-xl font-semibold">Errors</div>
  <div class="collapse-content">
    <p>Below is an example of a standard error message returned from the API.</p>
    <p class="mt-2">The HTTP response status in this example would be
      <code class="bg-base-200 px-1 rounded">422 Validation Failed</code>.
      It is included in the error response body since some clients have difficulty
      pulling the exact status message.</p>
    <pre class="bg-base-200 rounded-lg p-3 mt-2 text-sm"><code>{
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

{{-- HTTP Verbs --}}
<div class="collapse collapse-arrow border border-base-300 mb-2" id="overview-httpverbs">
  <input type="checkbox" />
  <div class="collapse-title text-xl font-semibold">HTTP Verbs</div>
  <div class="collapse-content">
    <p>Where possible, the API strives to use appropriate HTTP verbs for each action.</p>
    <div class="overflow-x-auto mt-3">
      <table class="table">
        <thead>
          <tr><th>Verb</th><th>Description</th></tr>
        </thead>
        <tbody>
          <tr>
            <td><span class="badge badge-success font-mono">GET</span></td>
            <td>Used for retrieving resources, either a list or single resource</td>
          </tr>
          <tr>
            <td><span class="badge badge-info font-mono">POST</span></td>
            <td>Used for creating resources</td>
          </tr>
          <tr>
            <td><span class="badge badge-warning font-mono">PUT</span></td>
            <td>Used for updating resources, or performing custom actions</td>
          </tr>
          <tr>
            <td><span class="badge badge-error font-mono">DELETE</span></td>
            <td>Used for deleting resources</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

{{-- Versioning --}}
<div class="collapse collapse-arrow border border-base-300 mb-2" id="overview-versioning">
  <input type="checkbox" />
  <div class="collapse-title text-xl font-semibold">Versioning</div>
  <div class="collapse-content">
    <p>All endpoints through the API are versioned. You can supply the version
      in the request three different ways:</p>
    <ol class="list-decimal list-inside mt-3 space-y-3">
      <li>
        <strong>In the URL:</strong>
        <pre class="bg-base-200 rounded-lg p-3 mt-1"><code>/v1.3/groups</code></pre>
      </li>
      <li>
        <strong>Query string parameter:</strong>
        <pre class="bg-base-200 rounded-lg p-3 mt-1"><code>/groups?version=1.3</code></pre>
        <span class="text-sm text-base-content/60">or</span>
        <pre class="bg-base-200 rounded-lg p-3 mt-1"><code>/groups?v=1.3</code></pre>
      </li>
      <li>
        <strong>Custom Accept Header:</strong>
        <pre class="bg-base-200 rounded-lg p-3 mt-1"><code>application/vnd.{{ $base }}.v1.3</code></pre>
      </li>
    </ol>
  </div>
</div>

{{-- Rate Limiting --}}
<div class="collapse collapse-arrow border border-base-300 mb-2" id="overview-ratelimiting">
  <input type="checkbox" />
  <div class="collapse-title text-xl font-semibold">Rate Limiting</div>
  <div class="collapse-content">
    <p>You can make up to <strong>60 requests per minute</strong>, with a hard limit
      of <strong>10,000 per day</strong>. For requests using OAuth, the rate limit is
      for each application and user combination. For unauthenticated requests, the rate
      limit is for the requesting IP address.</p>

    <h4 class="font-semibold mt-4 mb-2">Checking Your Rate Limit Status</h4>
    <p>You can check the returned HTTP headers of any API request to see your current
      per minute rate limit status:</p>
    <pre class="bg-base-200 rounded-lg p-3 mt-2 text-sm"><code>GET /groups/12345</code></pre>
    <pre class="bg-base-200 rounded-lg p-3 mt-2 text-sm"><code>HTTP/1.1 200 OK
Status: 200 OK
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 59
X-RateLimit-Reset: 1392321600</code></pre>

    <p class="mt-3">The headers tell you everything you need to know about your current rate limit status:</p>
    <div class="overflow-x-auto mt-2">
      <table class="table table-sm">
        <thead>
          <tr><th>Header</th><th>Description</th></tr>
        </thead>
        <tbody>
          <tr>
            <td><code class="bg-base-200 px-1 rounded text-sm">X-RateLimit-Limit</code></td>
            <td>The maximum number of requests that the consumer is permitted to make per minute</td>
          </tr>
          <tr>
            <td><code class="bg-base-200 px-1 rounded text-sm">X-RateLimit-Remaining</code></td>
            <td>The number of requests remaining in the current rate limit window</td>
          </tr>
          <tr>
            <td><code class="bg-base-200 px-1 rounded text-sm">X-RateLimit-Reset</code></td>
            <td>The time at which the current rate limit window resets in UTC epoch seconds</td>
          </tr>
        </tbody>
      </table>
    </div>

    <h4 class="font-semibold mt-4 mb-2">Rate Limit Exceeded</h4>
    <p>Once you go over the rate limit you will receive an error response:</p>
    <pre class="bg-base-200 rounded-lg p-3 mt-2 text-sm"><code>HTTP/1.1 429 Too Many Requests
Status: 429 Too Many Requests
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 0
X-RateLimit-Reset: 1392321600</code></pre>
    <pre class="bg-base-200 rounded-lg p-3 mt-2 text-sm"><code>{
    "code"    : 429,
    "message" : "Too Many Requests",
    "errors"  : []
}</code></pre>
    <div class="alert alert-info mt-3">
      <strong>Note:</strong> If you are exceeding your rate limit, you can likely fix
      the issue by caching API responses. If you're caching and still exceeding your
      rate limit, please contact us to request a higher rate limit for your application.
    </div>
  </div>
</div>

{{-- JSON-P --}}
<div class="collapse collapse-arrow border border-base-300 mb-2" id="overview-jsonp">
  <input type="checkbox" />
  <div class="collapse-title text-xl font-semibold">JSON-P</div>
  <div class="collapse-content">
    <p>You can send a <code class="bg-base-200 px-1 rounded">callback</code> parameter
      to any GET call to have the results wrapped in a JSON function. This is typically
      used when browsers want to embed content in web pages by getting around cross
      domain issues. The response includes the same data output as the regular API, plus
      the relevant HTTP Header information.</p>

    <pre class="bg-base-200 rounded-lg p-3 mt-3 text-sm"><code>GET /groups/?callback=FooBar</code></pre>
    <pre class="bg-base-200 rounded-lg p-3 mt-2 text-sm"><code>FooBar([
    {
        "gidNumber":   "1234",
        "cn":          "testgroup",
        "description": "Test Group",
        "created":     "2015-01-29T19:58:07Z",
        "created_by":  "1000"
    },
    ...
]);</code></pre>
    <p class="mt-3">You can write a JavaScript handler to process the callback like this:</p>
    <pre class="bg-base-200 rounded-lg p-3 mt-2 text-sm"><code>function FooBar(groupsData)
{
    console.log(groupsData)
}</code></pre>
  </div>
</div>

{{-- Expanding Objects --}}
<div class="collapse collapse-arrow border border-base-300 mb-2" id="overview-expanding">
  <input type="checkbox" />
  <div class="collapse-title text-xl font-semibold">Expanding Objects</div>
  <div class="collapse-content">
    <p>You can send an <code class="bg-base-200 px-1 rounded">expand</code> parameter
      to any GET call to have results expanded into full objects. This can be extremely
      useful and avoid having to make multiple requests.</p>

    <pre class="bg-base-200 rounded-lg p-3 mt-3 text-sm"><code>GET /groups/12345?expand=created_by</code></pre>
    <pre class="bg-base-200 rounded-lg p-3 mt-2 text-sm"><code>{
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
