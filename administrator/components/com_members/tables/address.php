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

/**
 * Table class for member/object association
 */
class MembersAddress extends JTable
{
	var $id               = null;
	var $uidNumber        = null;
	var $addressTo        = null;
	var $address1         = null;
	var $address2         = null;
	var $addressCity      = null;
	var $addressRegion    = null;
	var $addressPostal    = null;
	var $addressCountry   = null;
	var $addressLatitude  = null;
	var $addressLongitude = null;


	public function __construct( $db )
	{
		parent::__construct('#__xprofiles_address', 'id', $db);
	}


	/**
	 * Check method for saving addresses
	 *
	 * @return     void
	 */
	public function check()
	{
		if (!isset($this->uidNumber) || $this->uidNumber == '')
		{
			$this->setError( JText::_('You must supply a user id.') );
			return false;
		}

		return true;
	}


	/**
	 * Method to verify we can delete address
	 *
	 * @return     void
	 */
	public function canDelete($pk = NULL, $joins = NULL)
	{
		return true;
	}


	/**
	 * Method to get addressed for member
	 *
	 * @param      $uidNumber    Member User Id
	 * @return     void
	 */
	public function getAddressesForMember( $uidNumber )
	{
		//make sure we have a user id
		if (!isset($uidNumber))
		{
			$this->setError( JText::_('You must supply a user id.') );
			return false;
		}

		//query database for addresses for user id
		$sql = "SELECT * FROM {$this->_tbl} WHERE uidNumber=" . $this->_db->quote( $uidNumber );
		$this->_db->setQuery( $sql );

		return $this->_db->loadObjectList();
	}


	/**
	 * Method to format addresses for display on profile tab
	 *
	 * @param      $addresses           Array of Member Addresses
	 * @param      $displayEditLinks    Display Edit/Delete links with addresses
	 * @return     void
	 */
	public function formatAddressesForProfile( $addresses = array(), $displayEditLinks = false )
	{
		$formattedAddresses = '<div class="grid cf">';

		if (count($addresses) < 1)
		{
			$formattedAddresses .= '<div class="col span4">';
			$formattedAddresses .= JText::_('Enter an Address');
			$formattedAddresses .= '</div>';
		}
		else
		{
			foreach ($addresses as $k => $address)
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
				if ($displayEditLinks)
				{
					$formattedAddresses .= '<span class="address-links">';
					$formattedAddresses .= '<a class="edit edit-address" href="'.JRoute::_('index.php?option=com_members&id='.JFactory::getUser()->get('id').'&active=profile&action=editaddress&addressid='.$address->id).'">Edit</a>';
					$formattedAddresses .= ' | <a class="delete delete-address" href="'.JRoute::_('index.php?option=com_members&id='.JFactory::getUser()->get('id').'&active=profile&action=deleteaddress&addressid='.$address->id).'">Delete</a>';
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

		return $formattedAddresses;
	}
}
