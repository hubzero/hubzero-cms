<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Tests;

$componentPath = Component::path('com_forms');

require_once "$componentPath/helpers/criterionFactory.php";

use Hubzero\Test\Basic;
use Components\Forms\Helpers\CriterionFactory;

class CriterionFactoryTest extends Basic
{

	public function testOneReturnsLikeCriterionWhenLikeOperator()
	{
		$factory = new CriterionFactory();

		$criterion = $factory->one(['operator' => 'like']);
		$criterionClass = get_class($criterion);

		$this->assertEquals('Components\Forms\Helpers\LikeCriterion', $criterionClass);
	}

	public function testOneReturnsCriterionByDefault()
	{
		$factory = new CriterionFactory();

		$criterion = $factory->one(['operator' => 'unkown']);
		$criterionClass = get_class($criterion);

		$this->assertEquals('Components\Forms\Helpers\Criterion', $criterionClass);
	}

}
