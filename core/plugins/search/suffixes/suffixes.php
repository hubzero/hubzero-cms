<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Short description for 'plgSearchSuffixes'
 *
 * Long description (if any) ...
 */
class plgSearchSuffixes extends \Hubzero\Plugin\Plugin
{
	/**
	 * Short description for 'onSearchExpandTerms'
	 *
	 * Long description (if any) ...
	 *
	 * @param      array &$terms Parameter description (if any) ...
	 * @return     void
	 */
	public static function onSearchExpandTerms(&$terms)
	{
		$add = array();
		foreach ($terms as $term)
		{
			// eg electric <-> electronic
			if (preg_match('/^(.*?)(on)?ic$/', $term, $match))
			{
				$add[] = count($match) == 3 ? $match[1] . 'ic' : $match[1] . 'onic';
			}

			// the fulltxt indexer mangles course names, but it helps if we add a space between the letters and numbers
			if (preg_match('/^([a-zA-Z]+)(\d+)/', $term, $course_name))
			{
				$add[] = $course_name[1] . ' ' . $course_name[2];
			}
		}
		$terms = array_merge($terms, $add);
		foreach ($terms as $term)
		{
			// try plural
			$add[] = substr($term, 0, -1) == 's' ? $term . 'es' : $term . 's';
			if (substr($term, 0, -1) == 'y')
			{
				$add[] = substr($term, 0, strlen($term) -1) . 'ies';
			}
		}
		$terms = array_merge($terms, $add);
	}
}
