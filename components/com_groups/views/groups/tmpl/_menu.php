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
?>

<ul <?php echo $this->classOrId; ?>>
	<?php foreach ($this->sections as $k => $section) : ?>
		<?php
			//do we want to display item in menu?
			if (!$section['display_menu_tab'])
			{
				continue;
			}

			//set some vars
			$access  = $this->pluginAccess[$section['name']];
			$class   = strtolower($section['name']);
			$title   = $section['title'];
			$link    = JRoute::_('index.php?option=com_groups&cn='.$this->group->get('cn').'&active=' . $section['name']);
			$liClass = ($this->tab == $section['name']) ? 'active' : '';

			if (!isset($section['icon']))
			{
				$section['icon'] = 'f009';
			}

			//if we are on the overview tab and we have group pages
			if ($section['name'] == 'overview' && count($this->pages) > 0)
			{
				$trueTab = strtolower(JRequest::getVar('active', 'overview'));
				$liClass = ($trueTab != $this->tab) ? '' : $liClass;

				if (($access == 'registered' && $this->juser->get('guest')) || ($access == 'members' && !in_array($this->juser->get("id"), $this->group->get('members'))))
				{
					$item  = "<li class=\"protected group-overview-tab\"><span data-icon=\"&#x{$section['icon']};\" class=\"overview\">Overview</span>";
				}
				else
				{
					$item  = "<li class=\"{$liClass} group-overview-tab\">";
					$item .= "<a class=\"overview\" data-icon=\"&#x{$section['icon']};\" title=\"{$this->group->get('description')}'s Overview Page\" href=\"{$link}\">Overview</a>";
				}

				$item .= "<ul class=\"\">";

				foreach ($this->pages as $page)
				{
					// dont show home page in menu
					// dont show page links if there isnt an approved version
					if ($page->get('home') == 1 || $page->approvedVersion() === null)
					{
						continue;
					}

					// page access settings
					$pageAccess = ($page->get('privacy') == 'default') ? $access : $page->get('privacy');

					// page vars
					$title = $page->get('title');
					$cls = ($trueTab == $page->get('alias')) ? 'active' : '';
					$link = JRoute::_('index.php?option=com_groups&cn='.$this->group->get("cn").'&active='.$page->get('alias'));

					//page menu item
					if (($pageAccess == 'registered' && $this->juser->get('guest')) ||
					  ($pageAccess == 'members' && !in_array($this->juser->get("id"), $this->group->get('members'))))
					{
						$item .= "<li class=\"protected\"><span class=\"page\">{$title}</span></li>";
					}
					else
					{
						$item .= "<li class=\"{$cls}\">";
						$item .= "<a href=\"{$link}\" class=\"page\" title=\"{$this->group->get('description')}'s {$title} Page\">{$title}</a>";
						$item .= "</li>";
					}
				}

				$item .= "</ul>";
				$item .= "</li>";
			}
			else
			{
				if ($access == 'nobody')
				{
					$item = '';
				}
				elseif ($access == 'members' && !in_array($this->juser->get("id"), $this->group->get('members')))
				{
					$item  = "<li class=\"protected members-only group-{$class}-tab\" title=\"This page is restricted to group members only!\">";
					$item .= "<span data-icon=\"&#x{$section['icon']};\" class=\"{$class}\">{$title}</span>";
					$item .= "</li>";
				}
				elseif ($access == 'registered' && $this->juser->get('guest'))
				{
					$item  = "<li class=\"protected registered-only group-{$class}-tab\" title=\"This page is restricted to registered hub users only!\">";
					$item .= "<span data-icon=\"&#x{$section['icon']};\" class=\"{$class}\">{$title}</span>";
					$item .= "</li>";
				}
				else
				{
					//menu item meta data vars
					$metadata = (isset($this->sectionsContent[$k]['metadata'])) ? $this->sectionsContent[$k]['metadata'] : array();
					$meta_count = (isset($metadata['count']) && $metadata['count'] != '') ? $metadata['count'] : '';
					$meta_alert = (isset($metadata['alert']) && $metadata['alert'] != '') ? $metadata['alert'] : '';

					$cls  = ($meta_count) ? 'hasmeta' : '';
					$cls .= ($meta_alert) ? ' hasalert' : '';

					//create menu item
					$item  = "<li class=\"{$liClass} group-{$class}-tab {$cls}\">";
					$item .= "<a class=\"{$class}\"  data-icon=\"&#x{$section['icon']};\" title=\"{$this->group->get('description')}'s {$title} Page\" href=\"{$link}\">{$title}</a>";
					$item .= "<span class=\"meta\">";
					if ($meta_count)
					{
						$item .= "<span class=\"count\">" . $meta_count . "</span>";
					}
					$item .= "</span>";
					$item .= $meta_alert;
					$item .= "</li>";
				}
			}
			echo $item;
		?>
	<?php endforeach; ?>
</ul>