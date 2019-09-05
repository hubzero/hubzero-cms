<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Usage\Tests;

$componentPath = Component::path('com_usage');

require_once "$componentPath/helpers/monthsHelper.php";

use Components\Usage\Helpers\MonthsHelper as Helper;
use Hubzero\Test\Basic;

class MonthsHelperTest extends Basic
{

	public function testGetAbbreviationMap()
	{
		$expectedKeys = [
			'01', '02', '03', '04', '05', '06',
			'07', '08', '09', '10', '11', '12'
		];
		$expectedValues = [
			'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
			'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
		];
		$helper = new Helper();

		$map = $helper->getAbbreviationMap();
		$mapKeys = array_keys($map);
		$mapValues = array_values($map);

		$this->assertEquals($expectedKeys, $mapKeys);
		$this->assertEquals($expectedValues, $mapValues);
	}

	public function testGetAbbreviationMapReversed()
	{
		$expectedKeys = [
			'12', '11', '10', '09', '08', '07',
			'06', '05', '04', '03', '02', '01'
		];
		$expectedValues = [
			'Dec', 'Nov', 'Oct', 'Sep', 'Aug', 'Jul',
			'Jun', 'May', 'Apr', 'Mar', 'Feb', 'Jan'
		];
		$helper = new Helper();

		$map = $helper->getAbbreviationMapReversed();
		$mapKeys = array_keys($map);
		$mapValues = array_values($map);

		$this->assertEquals($expectedKeys, $mapKeys);
		$this->assertEquals($expectedValues, $mapValues);
	}

}
