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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   GNU General Public License, version 2 (GPLv2)
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$database = JFactory::getDBO();

if ($this->contributors)
{
	$html = '';
	$names = array();
	$orgs = array();
	$i = 1;
	$k = 0;
	$orgsln = '';
	$names_s = array();
	$orgsln_s = '';

	$types = array(
		'manager'       => 'manager',
		'administrator' => 'administrator',
		'super users'   => 'super administrator',
		'publisher'     => 'publisher',
		'editor'        => 'editor'
	);
	//$types = array(23 => 'manager', 24 => 'administrator', 25 => 'super administrator', 21 => 'publisher', 20 => 'editor');

	foreach ($this->contributors as $contributor)
	{
		if (strtolower($contributor->role) != 'submitter')
		{
			continue;
		}

		// Build the user's name and link to their profile
		if ($contributor->name)
		{
			$name = $contributor->name;
		}
		else if ($contributor->surname || $contributor->givenName)
		{
			$name = stripslashes($contributor->givenName) . ' ';
			if ($contributor->middleName != NULL)
			{
				$name .= stripslashes($contributor->middleName) . ' ';
			}
			$name .= stripslashes($contributor->surname);
		}
		else
		{
			$name = $contributor->xname;
		}
		if (!$contributor->org)
		{
			$contributor->org = $contributor->xorg;
		}

		$name = $this->escape(stripslashes($name));
		if ($contributor->id)
		{
			$link  = '<a href="' . JRoute::_('index.php?option=com_members&id=' . $contributor->id) . '" rel="contributor" title="View the profile of ' . $name . '">' . $name . '</a>';
		}
		else
		{
			$link  = $name;
		}

		//if ($newstyle)
		//{
			if ($this->badges)
			{
				$xuser = JUser::getInstance($contributor->id);
				if (is_object($xuser) && $xuser->get('name'))
				{
					// get users groups
					// in reverse to get the highest levels first
					$groupIds = JAccess::getGroupsByUser($xuser->id, false);
					$database->setQuery("SELECT title FROM `#__usergroups` WHERE `id` IN (" . implode(',', $groupIds) . ") ORDER BY lft ASC");
					$groups = array_reverse($database->loadColumn());

					// use the users first group
					$gid = isset($groups[0]) ? strtolower($groups[0]) : null;

					if (isset($types[$gid]))
					{
						$link .= ' <span class="badge">' . str_replace(' ', '-', $types[$gid]) . '</span>';
					}
				}
			}

			if (trim($contributor->org) != '' && !in_array(trim($contributor->org), $orgs))
			{
				$orgs[$i-1] = trim($contributor->org);
				$orgsln 	.= $i . '. ' . trim($contributor->org) . ' ';
				$orgsln_s 	.= trim($contributor->org).' ';
				$k = $i;
				$i++;
			}
			else
			{
				$k = array_search(trim($contributor->org), $orgs) + 1;
			}
			$link_s = $link;
			if (trim($contributor->org) != '')
			{
				$link .= '<sup>' . $k . '</sup>';
			}
			$names_s[] = $link_s;
		/*}
		else
		{
			$orgs[trim($contributor->org)][] = $link;
		}*/

		$names[] = $link;
	}


		if (count($names) > 0)
		{
			$html  = '<p>';
			$html .= count($orgs) > 1  ? implode(', ', $names) : implode(', ', $names_s);
			$html .= '</p>';
		}
		if ($this->showorgs && count($orgs) > 0)
		{
			$html .= '<p class="orgs">';
			$html .= count($orgs) > 1 ? $orgsln : $orgsln_s;
			$html .= '</p>';
		}

}
else
{
	$html = '';
}
echo $html;