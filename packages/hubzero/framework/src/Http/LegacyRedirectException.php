<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Framework\Http;

use RuntimeException;

/**
 * Thrown by App::redirect() to break out of legacy component code
 * and return a proper Laravel redirect response through the middleware
 * pipeline (so sessions are saved, cookies are set, etc.).
 */
class LegacyRedirectException extends RuntimeException
{
    public function __construct(
        public readonly string $url,
        public readonly int $statusCode = 302
    ) {
        parent::__construct("Redirect to {$url}");
    }
}
