<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$formattedAddresses = '<div class="grid cf">';

if (count($this->addresses) < 1)
{
	$formattedAddresses .= '<div class="col span4">';
	$formattedAddresses .= Lang::txt('PLG_MEMBERS_PROFILE_ADDRESS_ENTER');
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
			$formattedAddresses .= '<strong>' . $this->escape($address->addressTo) . '</strong><br />';
		}

		//do we have an address line 1
		if (isset($address->address1) && $address->address1 != '')
		{
			$formattedAddresses .= $this->escape($address->address1) . '<br />';
		}

		//do we have an address line 2
		if (isset($address->address2) && $address->address2 != '')
		{
			$formattedAddresses .= $this->escape($address->address2) . '<br />';
		}

		//do we gave a city state and zip
		$formattedAddresses .= $this->escape($address->addressCity) . ' ' . $this->escape($address->addressRegion) . ', ' . $this->escape($address->addressPostal) . '<br />';

		//do we have a country && its not USA
		if (isset($address->addressCountry) && $address->addressCountry != '' && $address->addressCountry != 'US' &&
			$address->addressCountry != 'USA' && $address->addressCountry != 'United States' && $address->addressCountry != 'United States of America')
		{
			$formattedAddresses .= $this->escape($address->addressCountry) . '<br />';
		}

		//do we want to display edit links
		if ($this->displayEditLinks)
		{
			$formattedAddresses .= '<span class="address-links">';
			$formattedAddresses .= '<a class="edit edit-address" href="' . Route::url($this->profile->link() . '&active=profile&action=editaddress&addressid=' . $address->id) . '">' . Lang::txt('JACTION_EDIT') . '</a>';
			$formattedAddresses .= ' | <a class="delete delete-address" href="' . Route::url($this->profile->link() . '&active=profile&action=deleteaddress&addressid=' . $address->id) . '">' . Lang::txt('JACTION_DELETE') . '</a>';
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
