<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Content\Import;

use Hubzero\Content\Import\Model\Import;

/**
 * Interface for Import Adapters
 */
interface Adapter
{
	/**
	 * Check if a mime type is accepted
	 *
	 * @param  string  $mime
	 */
	public static function accepts($mime);

	/**
	 * Count import records
	 *
	 * @param  object   $import
	 */
	public function count(Import $import);

	/**
	 * Process an import
	 *
	 * @param  object   $import
	 * @param  array    $callbacks
	 * @param  integer  $dryRun
	 */
	public function process(Import $import, array $callbacks, $dryRun);
}
