<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Oaipmh\Models;

/**
 * Interface for OAI-PMH data provider
 */
interface Provider
{
	/**
	 * Return data for sets
	 */
	public function sets();

	/**
	 * Return data for records
	 *
	 * @param  array  $filters
	 */
	public function records($filters = array());

	/**
	 * Return a single record
	 *
	 * @param  integer  $id
	 */
	public function record($id);
}
