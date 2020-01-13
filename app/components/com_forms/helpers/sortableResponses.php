<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Helpers;

class SortableResponses
{

	/**
	 * Constructs SortableResponses instance
	 *
	 * @param    array   $args   Instantiation state
	 * @return   void
	 */
	public function __construct($args)
	{
		$this->_responses = $args['responses'];
		$this->_rows = null;

		$this->pagination = $this->_responses->pagination;
	}

	/**
	 * Orders responses based on given field and direction
	 *
	 * @param    string   $sortField       Field to sort by
	 * @param    string   $sortDirection   Sorting direction
	 * @return   void
	 */
	public function order($sortField, $sortDirection)
	{
		switch ($sortField)
		{
			case 'completion_percentage':
				$this->_orderByCompletionPercentage($sortDirection);
				break;
			default:
				$this->_responses->order($sortField, $sortDirection);
		}
	}

	/**
	 * Orders responses based on calculated completion percentage
	 *
	 * @param    string   $sortDirection   Sorting direction
	 * @return   void
	 */
	protected function _orderByCompletionPercentage($sortDirection)
	{
		$responses = clone $this->_responses;
		$responseRecords = $responses->rows()->raw();

		$directionModifier = ($sortDirection == 'asc') ? 1 : -1;

		usort($responseRecords, function($before, $after) use ($directionModifier) {
			$delta = $before->requiredCompletionPercentage() - $after->requiredCompletionPercentage();
			return $delta * $directionModifier;
		});

		$this->_rows = $responseRecords;
	}

	/**
	 * Returns iterator containing response records
	 *
	 * @return   iterable
	 */
	public function rows()
	{
		$rows = $this->_rows;

		if (!$rows)
		{
			$rows = $this->_responses->rows();
		}

		return $rows;
	}

	/**
	 * Forwards function calls to responses
	 *
	 * @param    string   $name   Function name
	 * @param    array    $args   Function arguments
	 * @return   mixed
	 */
	public function __call($name, $args)
	{
		return $this->_responses->$name(...$args);
	}

}
