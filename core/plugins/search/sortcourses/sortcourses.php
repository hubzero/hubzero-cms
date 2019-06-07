<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Short description for 'plgSearchSortCourses'
 *
 * Long description (if any) ...
 */
class plgSearchSortCourses extends \Hubzero\Plugin\Plugin
{
	/**
	 * Short description for 'onYSearchSort'
	 *
	 * Long description (if any) ...
	 *
	 * @param      object $a Parameter description (if any) ...
	 * @param      object $b Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public static function onSearchSort($a, $b)
	{
		if ($a->get_plugin() !== 'resources' || $b->get_plugin() !== 'resources'
		 || $a->get_section() !== 'Courses' || $b->get_section() !== 'Courses')
		{
			return 0;
		}

		// Compare the leading parts of the resources to guess whether they
		// refer to the same course
		$title_a = preg_replace('/[^a-z]/', '', strtolower($a->get_title()));
		$title_b = preg_replace('/[^a-z]/', '', strtolower($a->get_title()));
		$match_threshold = 10;
		$match = true;
		for ($idx = 0; $idx < min($match_threshold, min(strlen($title_a), strlen($title_b))); ++$idx)
		{
			if ($title_a[$idx] !== $title_b[$idx])
			{
				$match = false;
				break;
			}
		}
		if (!$match)
		{
			return 0;
		}

		return $a->get_date() > $b->get_date() ? 1 : -1;
	}
}
