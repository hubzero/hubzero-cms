<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Traits;

trait isUnique
{

	public function isUnique()
	{
		$isUnique = true;

		foreach (self::$uniqueKeys as $fields)
		{
			if ($this->_duplicatesExist($fields))
			{
				$isUnique = false;
				break;
			}
		}

		return $isUnique;
	}

	protected function _duplicatesExist($fields)
	{
		$duplicates = $this->_findDuplicates($fields);

		return $duplicates->count() > 0;
	}

	protected function _findDuplicates($fields)
	{
		$savedRecords = self::all();

		foreach ($fields as $field)
		{
			$savedRecords
				->whereEquals($field, $this->get($field));
		}

		return $savedRecords;
	}

}
