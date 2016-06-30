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

// No direct access
defined('_HZEXEC_') or die('Restricted access');

/**
 * Short description for 'PaymentGateway'
 *
 * Long description (if any) ...
 */
class PaymentDispatcher
{
	private $buttonVars;
	private $credentials;

	private $paymentHandler;

	/**
	 * Constructor
	 *
	 * @param 	object transaction info
	 * @param	string payment gateway provider
	 * @param 	object payment gateway credentials
	 * @param 	object payment options
	 * @return  void
	 */
	public function __construct($paymentGatewayProivder)
	{
		// Load language file
		//JFactory::getLanguage()->load('com_cart');
		\App::get('language')->load('com_cart');

		switch ($paymentGatewayProivder)
		{
			case "PAYPAL STANDARD":
				$path = __DIR__ . DS . 'paypal_standard';
				set_include_path($path);
			break;
			case "UPAY":
				$path = __DIR__ . DS . 'upay';
				set_include_path($path);
			break;
			case "DUMMY AUTO PAYMENT":
				$path = __DIR__ . DS . 'dummy';
				set_include_path($path);
			break;
			default:
				die('Wrong payment gateway provider.');
		}

		require_once('PaymentProvider.php');
		$this->paymentHandler = new PaymentProvider();
	}

	/**
	 * Get Payment provider instance
	 *
	 */
	public function getPaymentProvider()
	{
		return $this->paymentHandler;
	}

	/* --------------------- Static methods -------------------- */

	/**
	 * Return a transaction ID variable name in the return from payment gatevay site payment URL
	 *
	 */
	public static function getTransactionIdVerificationVarName($paymentGatewayProivder)
	{
		switch ($paymentGatewayProivder)
		{
			case "PAYPAL STANDARD":
				return('cm');
			break;
			case "UPAY":
				return('tId');
			break;
			case "DUMMY AUTO PAYMENT":
			default:
				return('custom');
			break;
				die('Wrong payment gateway provider.');
		}
	}
}
