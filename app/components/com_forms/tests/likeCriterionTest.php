<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Tests;

$componentPath = Component::path('com_forms');

require_once "$componentPath/helpers/likeCriterion.php";

use Hubzero\Test\Basic;
use Components\Forms\Helpers\LikeCriterion;

class LikeCriterionTest extends Basic
{

	public function testGetSqlValueWhenFuzzyEndTrue()
	{
		$criterion = new LikeCriterion([
			'value' => 'foo',
			'fuzzy_end' => true
		]);

		$value = $criterion->getSqlValue();

		$this->assertEquals('foo%', $value);
	}

	public function testToArrayIncludesFuzzyEnd()
	{
		$criterion = new LikeCriterion([
			'fuzzy_end' => true
		]);

		$criterionArray = $criterion->toArray();

		$this->assertEquals($criterionArray['fuzzy_end'], 1);
	}

}
