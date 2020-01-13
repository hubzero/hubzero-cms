<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Helpers;

$componentPath = Component::path('com_forms');

require_once "$componentPath/helpers/criterion.php";

use Components\Forms\Helpers\Criterion;
use Hubzero\Utility\Arr;

class LikeCriterion extends Criterion
{

	protected $_fuzzyEnd;

	/**
	 * Constructs LikeCriterion instance
	 *
	 * @param    array   $args   Instantiation state
	 * @return   void
	 */
	public function __construct($args = [])
	{
		parent::__construct($args);

		$this->_operator = 'like';
		$this->_fuzzyEnd = Arr::getValue($args, 'fuzzy_end', 0);
	}

	/**
	 * Returns instances value to use when creating SQL statement
	 *
	 * @return   string
	 */
	public function getSqlValue()
	{
		$value = parent::getSqlValue();

		if ($this->_fuzzyEnd)
		{
			$value .= '%';
		}

		return $value;
	}

	/**
	 * Returns array representation of criterion
	 *
	 * @return   array
	 */
	public function toArray()
	{
		$thisAsArray = parent::toArray();

		$thisAsArray['fuzzy_end'] = $this->_fuzzyEnd;

		return $thisAsArray;
	}


}
