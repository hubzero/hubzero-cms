<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Short description for 'plgSearchWeightTitle'
 *
 * Long description (if any) ...
 */
class plgSearchWeightTitle extends \Hubzero\Plugin\Plugin
{
	/**
	 * Short description for 'onSearchWeightAll'
	 *
	 * Long description (if any) ...
	 *
	 * @param   mixed   $terms  Parameter description (if any) ...
	 * @param   object  $res    Parameter description (if any) ...
	 * @return  number  Return description (if any) ...
	 */
	public static function onSearchWeightAll($terms, $res)
	{
		$title = strtolower($res->get_title());
		$strterms = strtolower($terms->get_raw_without_section());
		if (preg_replace('/[^a-z]/', '', $title) == preg_replace('/[^a-z]/', '', $terms))
		{
			return 10;
		}

		$title_stems = self::stem_list($title);
		if (!($title_len = count($title_stems)))
		{
			return 0.5;
		}

		$term_stems = array();
		$quoted_weight = 0;
		foreach ($terms->get_positive_chunks() as $idx => $chunk)
		{
			if ($terms->is_quoted($idx))
			{
				if (strpos($title, $chunk) !== false)
				{
					$quoted_weight += count(explode(' ', $chunk));
				}
			}
			else
			{
				foreach (self::stem_list($chunk) as $stem)
				{
					$term_stems[] = $stem;
				}
			}
		}
		$term_stems = array_unique($term_stems);
		if (!($term_len = count($term_stems)))
		{
			return $quoted_weight ? $quoted_weight : 0.5;
		}

		$intersecting_stems = count(array_intersect($title_stems, $term_stems));
		return $quoted_weight + (1 - (($term_len - $intersecting_stems) * (0.8/$term_len)));
	}

	/**
	 * Summary for 'stem_list'
	 *
	 * Description (if any) ...
	 *
	 * @param   unknown  $str Parameter description (if any) ...
	 * @return  array    Return description (if any) ...
	 */
	private static function stem_list($str)
	{
		$stems = array();
		foreach (array_unique(preg_split('/\s+/', trim($str))) as $word)
		{
			if (!\Components\Search\Models\Basic\DocumentMetadata::is_stop_word($word))
			{
				$stems[] = stem(preg_replace('/[^[:alnum:]]/', '', $word));
			}
		}
		return $stems;
	}
}
