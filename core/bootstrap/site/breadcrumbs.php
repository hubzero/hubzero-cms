<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

$menu = \App::get('menu');

if ($item = $menu->getActive())
{
	$menus = $menu->getMenu();
	$home  = $menu->getDefault();

	if (is_object($home) && ($item->id != $home->id))
	{
		foreach ($item->tree as $menupath)
		{
			$url = '';
			$link = $menu->getItem($menupath);

			switch ($link->type)
			{
				case 'separator':
					$url = null;
					break;

				case 'url':
					if ((strpos($link->link, 'index.php?') === 0) && (strpos($link->link, 'Itemid=') === false))
					{
						// If this is an internal Joomla link, ensure the Itemid is set.
						$url = $link->link . '&Itemid=' . $link->id;
					}
					else
					{
						$url = $link->link;
					}
					break;

				case 'alias':
					// If this is an alias use the item id stored in the parameters to make the link.
					$url = 'index.php?Itemid=' . $link->params->get('aliasoptions');
					break;

				default:
					/*$router = \JSite::getRouter();
					if ($router->getMode() == JROUTER_MODE_SEF)
					{*/
						$url = 'index.php?Itemid=' . $link->id;
					/*}
					else {
						$url .= $link->link . '&Itemid=' . $link->id;
					}*/
					break;
			}

			$trail->append($menus[$menupath]->title, $url);
		}
	}
}
