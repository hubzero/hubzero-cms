<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Steve Snyder <snyder13@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
	 * @param      mixed $terms Parameter description (if any) ...
	 * @param      object $res Parameter description (if any) ...
	 * @return     number Return description (if any) ...
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
		foreach ($terms->get_positive_chunks() as $idx=>$chunk)
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
	 * Short description for 'stem_list'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $str Parameter description (if any) ...
	 * @return     array Return description (if any) ...
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

