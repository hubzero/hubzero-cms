<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Usage\Helpers;

class MonthsHelper {

	protected static $abbreviationMap = [
		'01' => 'Jan',
		'02' => 'Feb',
		'03' => 'Mar',
		'04' => 'Apr',
		'05' => 'May',
		'06' => 'Jun',
		'07' => 'Jul',
		'08' => 'Aug',
		'09' => 'Sep',
		'10' => 'Oct',
		'11' => 'Nov',
		'12' => 'Dec'
	];

	public function getAbbreviationMap()
	{
		return self::$abbreviationMap;
	}

	public function getAbbreviationMapReversed()
	{
		$map = $this->getAbbreviationMap();

		return array_reverse($map, true);
	}

}
