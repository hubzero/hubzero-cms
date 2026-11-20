<?php

namespace Plugins\Cart\Paypal;

use Hubzero\Plugin\Plugin;

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/**
 * Cart plugin for Payment: Paypal
 */
class Paypal extends Plugin
{
    /**
     * Affects constructor behavior. If true, language files will be loaded automatically.
     *
     * @var  boolean
     */
    protected $autoloadLanguage = true;

    /**
     * Render payment options
     *
     * @param   object  $cart
     * @param   object  $user
     * @return  array<string, mixed>
     */
    public function onRenderPaymentOptions($cart, $user)
    {
        $view = $this
            ->view('default', 'payment')
            ->set('user', $user)
            ->set('cart', $cart);

        $payment = [];
        $payment['options'] = $view->loadTemplate();
        $payment['title'] = $this->params->get('title', 'PayPal');
        $payment['description'] = $this->params->get('description', 'Checkout with PayPal');

        return $payment;
    }

    /**
     * Return a list of filters that can be applied
     *
     * @param   mixed  $transaction
     * @param   mixed  $user
     * @return  array<mixed>|bool
     */
    public function onProcessPayment($transaction, $user)
    {
        return true;
    }
}
