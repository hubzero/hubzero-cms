<?php

namespace Plugins\Content\Mollom\Service\Mollom\Exception;

/**
 * Mollom network error exception.
 *
 * Thrown in case a HTTP request results in code <= 0, denoting a low-level
 * communication error.
 */
class MollomNetworkException extends MollomException
{
	/**
	 * Overrides MollomException::$severity.
	 *
	 * The client may be able to recover from this error, so use a warning level.
	 */
	protected $severity = 'warning';
}

