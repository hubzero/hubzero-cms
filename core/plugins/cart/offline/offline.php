<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Cart\Offline;

use Hubzero\Plugin\Plugin;
use Components\Cart\Models\Cart;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;

/**
 * Cart plugin for Payment: Offline
 */
class Offline extends Plugin
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
     * @param   object  $transaction
     * @param   object  $user
     * @return  array<string, mixed>
     */
    public function onRenderPaymentOptions($transaction, $user)
    {
        $view = $this
            ->view('default', 'payment')
            ->set('url', Route::url('index.php?option=com_cart&controller=checkout/confirm'));

        $payment = [];
        $payment['options'] = $view->loadTemplate();
        $payment['title'] = $this->params->get('title', 'Offline');

        return $payment;
    }

    /**
     * On selected payment
     *
     * @param   \stdClass  $transaction
     * @param   \Hubzero\User\User  $user
     * @return  array<mixed>|false
     */
    public function onSelectedPayment($transaction, $user)
    {
        $provider = Request::getWord('paymentProvider', false, 'post');

        if ($provider != 'offline') {
            return false;
        }

        // Payment selected mark transaction as awaiting payment
        Cart::updateTransactionStatus('awaiting payment', $transaction->info->tId);

        $view = $this
            ->view('code', 'payment')
            ->set('user', $user)
            ->set('transaction', $transaction);

        $payment = [];
        $payment['status'] = 'ok';
        $payment['paymentInfo'] = 'Offline testing payment provider';
        $payment['response'] = $view->loadTemplate();
        $payment['title'] = $this->params->get('title', 'Offline');

        return $payment;
    }

    /**
     * Refuse a payment postback for the offline provider, which has no gateway
     *
     * @param   mixed  $postData  The postback, unused
     * @return  array<mixed>|false  False for other providers
     */
    public function onPostback($postData)
    {
        $provider = Request::getWord('provider', false, 'post');
        if ($provider != 'offline') {
            return false;
        }

        // Offline has no gateway to post back: the member confirms the order on
        // the hub (cart/order/pay), which checks it is theirs. Accepting a
        // postback here completed any transaction for whoever posted its ID,
        // with a token anyone can compute.
        return [
            'status' => 'error',
            'error'  => 'The offline payment provider takes no postback; order not completed.',
        ];
    }

    /**
     * On complete
     *
     * @param   string  $provider
     * @return  mixed
     */
    public function onComplete($provider)
    {
        if ($provider != 'offline') {
            return false;
        }

        $response = [];
        $response['verificationVar'] = 'custom';
        return $response;
    }
}
