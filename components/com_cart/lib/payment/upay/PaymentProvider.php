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
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * UPay payment provider
 *
 * Long description (if any) ...
 */
class PaymentProvider
{
	private $buttonVars;
	private $credentials;

	/**
	 * Constructor
	 *
	 * @param 	object transaction info
	 * @param	string payment gateway provider
	 * @param 	object payment gateway credentials
	 * @param 	object payment options
	 * @return  void
	 */
	public function __construct($transactionDetails, $paymentGatewayCredentials, $paymentOptions)
	{
		$this->buttonVars[] = 'item_name=' . $paymentOptions->transactionName;
		$this->buttonVars[] = 'business=' . $paymentOptions->businessName;

		//print_r($transactionDetails); die;

		$this->buttonVars[] = 'custom=' . $transactionDetails->info->tId;
		$this->buttonVars[] = 'amount=' . $transactionDetails->info->tTotalAmount;

		$this->credentials = $paymentGatewayCredentials;
	}

	/**
	 * Get HTML code for payment button
	 *
	 */
	public function getPaymentCode()
	{
		$code = "UPay code is coming";
		return $code;
	}
}