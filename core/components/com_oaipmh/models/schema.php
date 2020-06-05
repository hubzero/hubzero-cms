<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Oaipmh\Models;

/**
 * Interface for OAI-PMH Schemas
 */
interface Schema
{
	/**
	 * Check if a schema format is handled
	 *
	 * @param  string  $type
	 */
	public static function handles($type);

	/**
	 * Output set data
	 *
	 * @param  array  $set
	 */
	public function set($set);

	/**
	 * Output a lsit of records
	 *
	 * @param  array    $iterator
	 * @param  boolean  $metadata
	 */
	public function records($iterator, $metadata=true);

	/**
	 * Output a record
	 *
	 * @param  object   $result
	 * @param  boolean  $metadata
	 */
	public function record($result, $metadata=true);
}
