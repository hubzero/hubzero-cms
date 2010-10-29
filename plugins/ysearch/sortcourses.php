<?php

class plgYSearchSortCourses extends YSearchPlugin
{
	public static function onYSearchSort($a, $b)
	{
		if ($a->get_plugin() !== 'resources' || $b->get_plugin() !== 'resources' ||
			$a->get_section() !== 'Courses' || $b->get_section() !== 'Courses')
			return 0;
		
		// Compare the leading parts of the resources to guess whether they
		// refer to the same course
		$title_a = preg_replace('/[^a-z]/', '', strtolower($a->get_title()));
		$title_b = preg_replace('/[^a-z]/', '', strtolower($a->get_title()));
		$match_threshold = 10;
		$match = true;
		for ($idx = 0; $idx < min($match_threshold, min(strlen($title_a), strlen($title_b))); ++$idx)
			if ($title_a[$idx] !== $title_b[$idx])
			{
				$match = false;
				break;
			}
		if (!$match)
			return 0;
		
		return $a->get_date() > $b->get_date() ? 1 : -1;
	}
}
