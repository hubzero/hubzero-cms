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
			$response->headers->set('content-type', 'application/javascript');
			$response->setContent(sprintf('%s(%s);', $callback, $response->getContent()));
		}

		// return response
		return $response;
	}
}
