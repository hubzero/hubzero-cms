<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Short description for 'plgSearchWeightContributor'
 *
 * Long description (if any) ...
 */
class plgSearchWeightContributor extends \Hubzero\Plugin\Plugin
{
	/**
	 * Short description for 'onSearchWeightAll'
	 *
	 * Long description (if any) ...
	 *
	 * @param      object $terms Parameter description (if any) ...
	 * @param      object $res Parameter description (if any) ...
	 * @return     float Return description (if any) ...
	 */
	public static function onSearchWeightAll($terms, $res)
	{
		$pos_terms = $terms->get_positive_chunks();

		foreach (array_map('strtolower', $res->get_contributors()) as $contributor)
		{
			foreach ($pos_terms as $term)
			{
				if (strpos($contributor, $term) !== false)
				{
					return 1.0;
				}
			}
		}
		return 0.5;
	}
}
