<?php
/**
 * HUBzero CMS
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
 *
 * @package   hubzero-cms
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

ximport('Hubzero_Image_Mozify');

/**
 * Helper class for Converting Image to Table Mosaic
 */
class Hubzero_Image_MozifyHelper
{
	public static function mozifyHtml( $html = '', $mosaicSize = 5 )
	{
		//get all image tags
		preg_match_all('/<img src="([^"]*)"([^>]*)>/', $html, $matches, PREG_SET_ORDER);
		
		//if we have matches mozify the images
		if (count($matches) > 0)
		{
			foreach ($matches as $match)
			{
				$config = array( 'imageUrl' => $match[1], 'mosaicSize' => $mosaicSize );
				$him = new Hubzero_Image_Mozify($config);
				$html = str_replace($match[0], $him->mozify(), $html);
			}
		}
		
		return $html;
	}
}