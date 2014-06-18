<?php

namespace Plugins\Content\Mollom\Service\Mollom\Exception;

use Exception;
use Plugins\Content\Mollom\Service\Mollom;

/**
 * A catchable Mollom exception.
 *
 * The Mollom class internally uses exceptions to handle HTTP request errors
 * within the Mollom::handleRequest() method. All exceptions thrown in the
 * Mollom class and derived classes should be instances of the MollomException
 * class if they pertain to errors that can be catched/handled within the class.
 * Other errors should not use the MollomException class and handled
 * differently.
 *
 * No MollomException is supposed to pile up as a user-facing fatal error. All
 * functions that invoke Mollom::handleRequest() have to catch Mollom
 * exceptions.
 *
 * @see Mollom::query()
 * @see Mollom::handleRequest()
 *
 * @param $message
 *   The Exception message to throw.
 * @param $code
 *   The Exception code.
 * @param $previous
 *   (optional) The previous Exception, if any.
 * @param $instance
 *   The Mollom class instance the Exception is thrown in.
 * @param $arguments
 *   (optional) A associative array containing information about a performed
 *   HTTP request that failed:
 *   - request: (string) The HTTP method and URI of the performed request; e.g.,
 *     "GET http://server.mollom.com/v1/foo/bar". In case of GET requests, do
 *     not add query parameters to the URI; pass them in 'data' instead.
 *   - data: (array) An associative array containing HTTP GET/POST/PUT request
 *     query parameters that were sent to the server.
 *   - response: (mixed) The server response, either as string, or the already
 *     parsed response; i.e., an array.
 */
class MollomException extends Exception
{
	/**
	 * @var Mollom
	 */
	protected $mollom;

	/**
	 * The severity of this exception.
	 *
	 * By default, all exceptions should be logged and appear as errors (unless
	 * overridden by a later log entry).
	 *
	 * @var string
	 */
	protected $severity = 'error';

	/**
	 * Overrides Exception::__construct().
	 */
	function __construct($message = '', $code = 0, Exception $previous = NULL, Mollom $mollom, array $request_info = array())
	{
		// Fatal error on PHP <5.3 when passing more arguments to Exception.
		if (version_compare(phpversion(), '5.3') >= 0)
		{
			parent::__construct($message, $code, $previous);
		}
		else
		{
			parent::__construct($message, $code);
		}
		$this->mollom = $mollom;

		// Set the error code on the Mollom class.
		$mollom->lastResponseCode = $code;

		// Log the exception.
		// To aid Mollom technical support, include the IP address of the server we
		// tried to reach in case a request fails.
		// PHP's native gethostbyname() is available on all platforms, but its DNS
		// lookup and caching behavior is undocumented and unclear. User comments on
		// php.net mention that it does not have an own cache and also does not use
		// the OS/platform's native DNS name resolver. Due to that, we only use it
		// under error conditions.
		$message = array(
			'severity' => $this->severity,
			'message' => 'Error @code: %message (@server-ip)',
			'arguments' => array(
				'@code' => $code,
				'%message' => $message,
				'@server-ip' => gethostbyname($mollom->server),
			),
		);
		// Add HTTP request information, if available.
		if (!empty($request_info))
		{
			$message += $request_info;
		}
		$mollom->log[] = $message;
	}
}
