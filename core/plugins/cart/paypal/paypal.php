<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/**
 * Cart plugin for Payment: Paypal
 */
class plgCartPaypal extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Render payment options
	 *
	 * @param   object  $cart
	 * @param   object  $user
	 * @return  array
	 */
	public function onRenderPaymentOptions($cart, $user)
	{
		$view = $this->view('default', 'payment')
			->set('user', $user)
			->set('cart', $cart);

		$payment = array();
		$payment['options'] = $view->loadTemplate();
		$payment['title'] = $this->params->get('title', 'PayPal');
		$payment['description'] = $this->params->get('description', 'Checkout with PayPal');

		return $payment;
	}

	/**
	 * Return a list of filters that can be applied
	 *
	 * @return  array
	 */
	public function onProcessPayment($transaction, $user)
	{
		return true;
	}
}
