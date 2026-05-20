<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Api\RateLimit;

use Hubzero\Base\Middleware;
use Hubzero\Http\Request;
use Hubzero\Utility\Date;

/**
 * Rate limit service for API
 */
class RateLimitService extends Middleware
{
	/**
	 * Load Service
	 *
	 * @return  void
	 */
	public function register()
	{
		// Bind the actual rate limiter
		$this->app['ratelimiter'] = function($app)
		{
			// creat new storage object
			$storage = new Storage\Database($app['db']);

			// Get rate limit config (JSON encode/decode to get as array)
			$config = json_decode(json_encode($app['config']->get('rate_limit')), true);
			$config = (is_array($config)) ? $config : [];

			// Create and return new rate limiter
			return new RateLimiter($storage, $config);
		};
	}

	/**
	 * Handle request in HTTP stack
	 *
	 * @param   object  $request  HTTP Request
	 * @return  mixed
	 */
	public function handle(Request $request)
	{
		// Get response
		$response = $this->next($request);

		// Rate limiting is keyed on the OAuth2 token's (application_id, uidNumber).
		// Session-cookie and unauthenticated API requests carry no token, so the
		// historical code (commit 17f1b3b743, "[PHP7] setup default ratelimit
		// token values") lumped every one of them into a single shared
		// application_id=0/user_id=0 bucket. One busy user could then exhaust the
		// shared short-window limit and return HTTP 429 to everyone else -- a
		// self-inflicted DoS that surfaced as intermittent grading errors after
		// the ~Apr-2025 site upgrade (support ticket #293).
		//
		// So skip tokenless requests entirely: only requests authenticated by a
		// registered API application are rate limited (per-application limiting is
		// unchanged). If session/IP abuse protection is ever needed, apply it at
		// the web-server/proxy layer, not in this per-application middleware.
		$token = $this->app['auth']->token();

		if (empty($token['application_id']))
		{
			return $response;
		}

		// Rate limit by application/user and fetch the current counters
		$rateLimitData = $this->app['ratelimiter']->rateLimit($token['application_id'], $token['uidNumber']);

		// If over either the short or long limit, reject with HTTP 429
		if ($rateLimitData->exceeded_long || $rateLimitData->exceeded_short)
		{
			throw new \Exception('You have exceeded your rate limit allowance. Please see rate limit headers for details.', 429);
		}

		// Otherwise annotate the response with rate limit headers
		$response->headers->set('X-RateLimit-Limit', $rateLimitData->limit_short);
		$response->headers->set('X-RateLimit-Remaining', $rateLimitData->limit_short - $rateLimitData->count_short);
		$response->headers->set('X-RateLimit-Reset', with(new Date($rateLimitData->expires_short))->toUnix());

		// Return response
		return $response;
	}
}
