<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

//var to hold content
$content = '';

//loop through each component and pages group passed in
foreach ($this->pages as $component)
{
	//build content to return
	$content .= '<h2>' . Lang::txt('COM_HELP_COMPONENT_HELP', $component['name']) . '</h2>';

	//make sure we have pages
	if (count($component['pages']) > 0)
	{
		$content .= '<p>' . Lang::txt('COM_HELP_PAGE_INDEX_EXPLANATION', $component['name']) . '</p>';
		$content .= '<ul>';
		foreach ($component['pages'] as $page)
		{
			$name = str_replace('.' . $this->layoutExt, '', $page);

			$content .= '<li><a href="' . Route::url('index.php?option=com_help&component=' . str_replace('com_', '', $component['option']) . '&page=' . $name) . '">' . ucwords(str_replace('_', ' ', $name)) .'</a></li>';
		}
		$content .= '</ul>';
	}
	else
	{
		$content .= '<p>' . Lang::txt('COM_HELP_NO_PAGES_FOUND') . '</p>';
	}
}

echo $content;