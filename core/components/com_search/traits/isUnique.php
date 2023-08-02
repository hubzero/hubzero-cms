<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Traits;

trait isUnique
{

	public function isUnique()
	{
		$isUnique = true;

		foreach ($this::uniqueKeys as $fields)
		{
			if ($this->duplicatesExist($fields))
			{
				$isUnique = false;
				break;
			}
		}

		return $isUnique;
	}

	protected function duplicatesExist($fields)
	{
		$duplicates = $this->findDuplicates($fields);

		return $duplicates->count() > 0;
	}

	protected function findDuplicates($fields)
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
