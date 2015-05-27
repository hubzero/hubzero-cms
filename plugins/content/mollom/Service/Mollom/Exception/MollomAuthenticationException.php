<?php

namespace Plugins\Content\Mollom\Service\Mollom\Exception;

include_once __DIR__ . DS . 'MollomException.php';

/**
 * Mollom authentication error exception.
 *
 * Thrown in case API keys or other authentication parameters are invalid.
 */
class MollomAuthenticationException extends MollomException
{
}
