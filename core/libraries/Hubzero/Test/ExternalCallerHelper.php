<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Test;

use Hubzero\Database\Driver;

/**
 * External caller helper for testing raw query mode enforcement
 *
 * This class is intentionally outside the Hubzero\Database namespace to
 * simulate an application-level caller (component, module, etc.) calling
 * setQuery() directly. Because it's not in Hubzero\Database\*,
 * the enforceRawQueryMode() check will flag it as external.
 */
class ExternalCallerHelper
{
    public function callSetQuery(
        Driver $driver,
        string $sql,
    ): void {
        $driver->setQuery($sql);
    }
}
