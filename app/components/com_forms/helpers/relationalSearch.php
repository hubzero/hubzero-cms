<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Helpers;

class RelationalSearch
{

	protected $_class;

	/**
	 * Constructs RelationalSearch instance
	 *
	 * @param    array   $args   Instantiation state
	 * @return   void
	 */
	public function __construct($args = [])
	{
		$this->_class = $args['class'];
	}

	/**
	 * Returns records matching given criteria
	 *
	 * @param    array    $criteria   Criteria to match records to
	 * @return   object
	 */
	public function findBy($criteria)
	{
		$records = $this->_class->all();

		$this->_filterBy($records, $criteria);

		return $records;
	}

	/**
	 * Filters records based on given criteria
	 *
	 * @param    object   $records    Type records
	 * @param    array    $criteria   Criteria to match records to
	 * @return   void
	 */
	protected function _filterBy($records, $criteria)
	{
		foreach ($criteria as $criterion)
		{
			$this->_addFilter($records, $criterion);
		}
	}

	protected function _addFilter($records, $criterion)
	{
		if ($criterion->isValid())
		{
			$records->where(
				$criterion->getName(),
				$criterion->getOperator(),
				$criterion->getSqlValue()
			);
		}
	}

}
