<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Members\Models\Import\Handler;

use Hubzero\Base\Obj;
use User;

include_once dirname(dirname(__DIR__)) . '/tags.php';

/**
 * Member Tags import handler
 */
class Tags extends Obj
{
	/**
	 * Return a sample for import header and content
	 *
	 * @return  array
	 */
	public function sample()
	{
		return array();
	}

	/**
	 * Bind all raw data
	 *
	 * @param   object  $raw
	 * @param   object  $record
	 * @param   string  $mode
	 * @return  object
	 */
	public function bind($raw, $record, $mode = 'UPDATE')
	{
		if (isset($raw->interests))
		{
			$record->tags = $this->_multiValueField($raw->interests);
		}

		return $record;
	}

	/**
	 * Check Data integrity
	 *
	 * @param   object  $raw
	 * @param   object  $record
	 * @param   string  $mode
	 * @return  object
	 */
	public function check($raw, $record, $mode = 'UPDATE')
	{
		return $record;
	}

	/**
	 * Store data
	 *
	 * @param   object  $raw
	 * @param   object  $record
	 * @param   string  $mode
	 * @return  object
	 */
	public function store($raw, $record, $mode = 'UPDATE')
	{
		if (!isset($record->tags))
		{
			return $record;
		}

		if ($mode == 'PATCH' && !$record->tags)
		{
			return $record;
		}

		// save tags
		$tags = new \Components\Members\Models\Tags($record->entry->get('id'));
		$tags->setTags($record->tags, User::get('id'));

		return $record;
	}

	/**
	 * Split a string into multiple values based on delimiter(s)
	 *
	 * @param   mixed   $data   String or array of field values
	 * @param   string  $delim  List of delimiters, separated by a pipe "|"
	 * @return  array
	 */
	private function _multiValueField($data, $delim=',|;')
	{
		if (is_string($data))
		{
			$data = array_map('trim', preg_split("/($delim)/", $data));
			$data = array_values(array_filter($data));
		}

		return $data;
	}
}
