<?php
/**
 * @package     hubzero-cms
 * @author      Steve Snyder <snyder13@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );


class plgYSearchWeightTitle
{
	public static function onYSearchWeightAll($terms, $res)
	{
		$title = strtolower($res->get_title());
		$strterms = strtolower($terms->get_raw_without_section());
		if (preg_replace('/[^a-z]/', '', $title) == preg_replace('/[^a-z]/', '', $terms))
			return 10;

		$title_stems = self::stem_list($title);
		if (!($title_len = count($title_stems))) return 0.5;

		$term_stems = array();
		$quoted_weight = 0;
		foreach ($terms->get_positive_chunks() as $idx=>$chunk)
			if ($terms->is_quoted($idx))
			{
				if (strpos($title, $chunk) !== false)
				{
					echo "$chunk in $title: ".count(explode(' ', $chunk));
					$quoted_weight += count(explode(' ', $chunk));
				}
			}
			else
				foreach (self::stem_list($chunk) as $stem)
					$term_stems[] = $stem;
		$term_stems = array_unique($term_stems);
		if (!($term_len = count($term_stems))) return $quoted_weight ? $quoted_weight : 0.5;

		$intersecting_stems = count(array_intersect($title_stems, $term_stems));
		return $quoted_weight + (1 - (($term_len - $intersecting_stems) * (0.8/$term_len)));
	}

	private static function stem_list($str)
	{
		$stems = array();
		foreach (array_unique(preg_split('/\s+/', trim($str))) as $word)
			if (!DocumentMetadata::is_stop_word($word))
				$stems[] = stem(preg_replace('/[^[:alnum:]]/', '', $word));
		return $stems;
	}
}

