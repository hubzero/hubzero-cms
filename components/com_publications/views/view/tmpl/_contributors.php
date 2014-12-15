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
	$html 		= '';
	$names 		= array();
	$orgs 		= array();
	$i 			= 1;
	$k 			= 0;
	$orgsln 	= '';
	$names_s 	= array();
	$orgsln_s 	= '';

	foreach ($this->contributors as $contributor)
	{
		if ($this->incSubmitter == false && $contributor->role == 'submitter')
		{
			continue;
		}

		// Build the user's name and link to their profile
		if ($contributor->name)
		{
			$name = $this->escape(stripslashes($contributor->name));
		}
		else
		{
			$name = $this->escape(stripslashes($contributor->p_name));
		}
		if ($this->format)
		{
			$nameParts    = explode(" ", $name);
			$name = end($nameParts);
			$name.= count($nameParts) > 1 ? ', ' . strtoupper(substr($nameParts[0], 0, 1)) . '.' : '';
			$name.= count($nameParts) > 2 ? ' ' . strtoupper(substr($nameParts[1], 0, 1)) . '.' : '';
		}

		if (!$contributor->organization)
		{
			$contributor->organization = $contributor->p_organization;
		}
		$contributor->organization = $this->escape(stripslashes(trim($contributor->organization)));

		$name = str_replace( '"', '&quot;', $name );
		if ($contributor->user_id && $contributor->open)
		{
			$link  = '<a href="'.JRoute::_('index.php?option=com_members&amp;id=' . $contributor->user_id)
					. '" title="View the profile of ' . $name . '">' . $name . '</a>';
		}
		else
		{
			$link = $name;
		}
		$link .= ($contributor->role) ? ' ('.$contributor->role.')' : '';

		if (trim($contributor->organization) != '' && !in_array(trim($contributor->organization), $orgs))
		{
			$orgs[$i-1] = trim($contributor->organization);
			$orgsln 	.= $i. '. ' .trim($contributor->organization).' ';
			$orgsln_s 	.= trim($contributor->organization).' ';
			$k = $i;
			$i++;
		}
		else if (trim($contributor->organization) != '')
		{
			$k = array_search(trim($contributor->organization), $orgs) + 1;
		}
		else
		{
			$k = 0;
		}

		$link_s = $link;
		if ($this->showorgs && $k)
		{
			$link .= '<sup>'. $k .'</sup>';
		}
		$names_s[] = $link_s;
		$names[] = $link;
	}

	if (count($names) > 0)
	{
		$html = '<p>' . ucfirst(JText::_('By')) . ' ';
		$html .= count($names) > 1 && count($orgs) > 0  ? implode( ', ', $names ) : implode( ', ', $names_s )  ;
		$html .= '</p>';
	}
	if ($this->showorgs && count($orgs) > 0)
	{
		$html .= '<p class="orgs">';
		$html .= count($orgs) > 1 ? $orgsln : $orgsln_s;
		$html .= '</p>';
	}
	if ($this->showaslist)
	{
		$html = count($names) > 1  ? implode( ', ', $names ) : implode( ', ', $names_s ) ;
	}

}
else
{
	$html = '';
}

echo $html;