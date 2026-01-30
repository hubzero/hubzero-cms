<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Content\Migration;

/**
 * Exception thrown when a migration needs to skip execution.
 *
 * This is a clean way for migrations to signal they should be skipped
 * (e.g., required database not available, preconditions not met) without
 * being marked as completed or failed.
 *
 * Usage in a migration:
 *   throw new \Hubzero\Content\Migration\SkipMigrationException('Metrics database not available');
 */
class SkipMigrationException extends \Exception
{
}
