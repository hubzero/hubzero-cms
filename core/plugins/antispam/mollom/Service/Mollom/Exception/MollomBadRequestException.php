<?php

namespace Plugins\Antispam\Mollom\Service\Mollom\Exception;

include_once __DIR__ . DS . 'MollomException.php';

/**
 * Mollom error due to bad client request exception.
 *
 * Thrown in case the local time diverges too much from UTC.
 *
 * @see Mollom::TIME_OFFSET_MAX
 * @see Mollom::REQUEST_ERROR
 * @see Mollom::handleRequest()
 */
class MollomBadRequestException extends MollomException
{
}

