<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

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
		Lang::load('com_cart');

		switch ($paymentGatewayProivder)
		{
			case "PAYPAL STANDARD":
				$path = JPATH_COMPONENT . '/lib/payment/paypal_standard';
				set_include_path($path);
			break;
			case "UPAY":
				$path = JPATH_COMPONENT . '/lib/payment/upay';
				set_include_path($path);
			break;
			case "DUMMY AUTO PAYMENT":
				$path = JPATH_COMPONENT . '/lib/payment/dummy';
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