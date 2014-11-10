<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
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
defined('_JEXEC') or die( 'Restricted access');

$formattedAddresses = '<div class="grid cf">';

if (count($this->addresses) < 1)
{
	$formattedAddresses .= '<div class="col span4">';
	$formattedAddresses .= JText::_('PLG_MEMBERS_PROFILE_ADDRESS_ENTER');
	$formattedAddresses .= '</div>';
}
else
{
	foreach ($this->addresses as $k => $address)
	{
		//start
		if (($k+1) % 3 == 0)
		{
			$formattedAddresses .= '<div class="col span4 omega">';
		}
		else
		{
			$formattedAddresses .= '<div class="col span4">';
		}

		//do we have a to field
		if (isset($address->addressTo) && $address->addressTo != '')
		{
			$formattedAddresses .= '<strong>' . $address->addressTo . '</strong><br />';
		}

		//do we have an address line 1
		if (isset($address->address1) && $address->address1 != '')
		{
			$formattedAddresses .= $address->address1 . '<br />';
		}

		//do we have an address line 2
		if (isset($address->address2) && $address->address2 != '')
		{
			$formattedAddresses .= $address->address2 . '<br />';
		}

		//do we gave a city state and zip
		$formattedAddresses .= $address->addressCity . ' ' . $address->addressRegion . ', ' . $address->addressPostal . '<br />';

		//do we have a country && its not USA
		if (isset($address->addressCountry) && $address->addressCountry != '' && $address->addressCountry != 'US' &&
			$address->addressCountry != 'USA' && $address->addressCountry != 'United States' && $address->addressCountry != 'United States of America')
		{
			$formattedAddresses .= $address->addressCountry . '<br />';
		}

		//do we want to display edit links
		if ($this->displayEditLinks)
		{
			$formattedAddresses .= '<span class="address-links">';
			$formattedAddresses .= '<a class="edit edit-address" href="' . JRoute::_($this->profile->getLink() . '&active=profile&action=editaddress&addressid=' . $address->id) . '">' . JText::_('JACTION_EDIT') . '</a>';
			$formattedAddresses .= ' | <a class="delete delete-address" href="' . JRoute::_($this->profile->getLink() . '&active=profile&action=deleteaddress&addressid=' . $address->id) . '">' . JText::_('JACTION_DELETE') . '</a>';
			$formattedAddresses .= '</span>';
		}

		//end column
		$formattedAddresses .= '</div><!-- /#end address col -->';
		if ((($k+1) % 3 == 0) && count($addresses) > 3)
		{
			$formattedAddresses .= '</div>';
			$formattedAddresses .= '<div class="grid cf">';
		}
	}
}

//end grid
$formattedAddresses .= '</div><!-- /#end address grid -->';

echo $formattedAddresses;