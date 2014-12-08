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

	foreach ($this->contributors as $contributor)
	{
		if (strtolower($contributor->role) == 'submitter')
		{
			continue;
		}

		// Build the user's name and link to their profile
		if ($contributor->name)
		{
			$name = $this->escape(stripslashes($contributor->name));
		}
		else if ($contributor->surname || $contributor->givenName)
		{
			$name = $this->escape(stripslashes($contributor->givenName)) . ' ';
			if ($contributor->middleName != NULL)
			{
				$name .= $this->escape(stripslashes($contributor->middleName)) . ' ';
			}
			$name .= $this->escape(stripslashes($contributor->surname));
		}
		else
		{
			$name = $this->escape(stripslashes($contributor->xname));
		}

		if (!$contributor->org)
		{
			$contributor->org = $contributor->xorg;
		}
		$contributor->org = $this->escape(stripslashes(trim($contributor->org)));

		$link = $name;
		if ($contributor->id)
		{
			$profile = \Hubzero\User\Profile::getInstance($contributor->id);
			if ($profile && $profile->get('public'))
			{
				$link = '<a href="' . JRoute::_($profile->getLink()) . '" rel="contributor" title="' . JText::sprintf('View the profile of %s', $name) . '">' . $name . '</a>';
			}
		}

		$link .= ($contributor->role) ? ' (' . $contributor->role . ')' : '';

		if (trim($contributor->org) != '' && !in_array(trim($contributor->org), $orgs))
		{
			$orgs[$i-1] = trim($contributor->org);
			$orgsln    .= $i . '. ' . trim($contributor->org) . ' ';
			$orgsln_s  .= trim($contributor->org).' ';
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
		$names[] = $link;
	}

	if (count($names) > 0)
	{
		$html = '<p>'.ucfirst(JText::_('By')).' ';
		//$html .= count($orgs) > 1  ? implode(', ', $names) : implode(', ', $names_s);
		$html .= count($this->contributors) > 1 ? implode(', ', $names) : implode(', ', $names_s);
		$html .= '</p>';
	}

	if (count($orgs) > 0)
	{
		$html .= '<p class="orgs">';
		//$html .= count($orgs) > 1 ? $orgsln : $orgsln_s;
		$html .= count($this->contributors) > 1 ? $orgsln : $orgsln_s;
		$html .= '</p>';
	}
}
else
{
	$html = '';
}

echo $html;