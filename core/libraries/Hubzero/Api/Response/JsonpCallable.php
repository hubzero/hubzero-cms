<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Api\Response;

use Hubzero\Base\Middleware;
use Hubzero\Http\Request;

/**
 * JSON-P Response Modifier
 */
class JsonpCallable extends Middleware
{
	/**
	 * Handle request in HTTP stack
	 *
	 * @param   objct  $request  HTTP Request
	 * @return  mixes
	 */
	public function handle(Request $request)
	{
		// execute response
		$response = $this->next($request);

		// check for presence of callback param
		// if we have one lets replace response content with a function executing the
		// current response content
		if ($callback = $request->getVar('callback', null))
		{
			// Only allow a valid JS identifier (optionally dotted/namespaced) as
			// the callback, so the reflected value cannot inject script.
			if (!is_string($callback) || !preg_match('/^[A-Za-z_$][A-Za-z0-9_$]*(\.[A-Za-z_$][A-Za-z0-9_$]*)*$/', $callback))
			{
				return $response;
			}
			$response->headers->set('content-type', 'application/javascript');
			$response->headers->set('X-Content-Type-Options', 'nosniff');
			$response->setContent(sprintf('/**/%s(%s);', $callback, $response->getContent()));
		}

		// return response
		return $response;
	}
}
