<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Helpers;

$componentPath = Component::path('com_forms');

require_once "$componentPath/helpers/criterion.php";
require_once "$componentPath/helpers/likeCriterion.php";

use Components\Forms\Helpers\Criterion;
use Components\Forms\Helpers\LikeCriterion;
use Hubzero\Utility\Arr;

class CriterionFactory
{

	/**
	 * Instantiates appropriate Criterion type
	 *
	 * @param    array    $args   Criterion type and instantiation state
	 * @return   object
	 */
	public function one($args = [])
	{
		$operator = Arr::getValue($args, 'operator');

		switch($operator)
		{
			case 'like':
				return new LikeCriterion($args);
			default:
				return new Criterion($args);
		}
	}

}
