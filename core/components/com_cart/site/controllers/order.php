<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Cart\Site\Controllers;

use Components\Cart\Models\Cart;
use Components\Cart\Models\CurrentCart;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;
use Hubzero\Facades\Event;
use Hubzero\Facades\Lang;
use Components\Cart\Lib\Cartmessenger\CartMessenger;
use Components\Cart\Lib\Cartmessenger\LoggingLevel;
use Hubzero\Facades\App;
use Hubzero\Facades\Component;
use Hubzero\Facades\Notify;
use Hubzero\Facades\Pathway;
use Hubzero\Facades\Plugin;
use Hubzero\Facades\User;

/**
 * Cart order controller class
 */
class Order extends ComponentController
{
    /**
     * Execute a task
     *
     * @return  void
     */
    public function execute()
    {
        // Get the task
        $this->task  = Request::getCmd('task', '');

        if (empty($this->task)) {
            $this->task = 'home';
            $this->registerTask('__default', $this->task);
        }

        parent::execute();
    }

    /**
     * Default task
     *
     * @return  void
     */
    public function homeTask()
    {
        App::abort(404);
    }

    /**
     * This is a redirect page where the customer ends up after payment is complete
     *
     * @return  void
     */
    public function completeTask()
    {
        // Get the plugins working
        $provider = Request::getString('provider', '', 'get');
        $pay = Event::trigger('cart.onComplete', [$provider]);

        $verificationVar = false;

        foreach ($pay as $response) {
            if ($response) {
                $verificationVar = $response['verificationVar'];

                break;
            }
        }

        if (empty($verificationVar)) {
            $verificationVar = 'custom';
        }

        // Check the GET values passed
        $customVar = Request::getString($verificationVar, '');

        $tId = false;
        if (strstr($customVar, '-')) {
            $customData = explode('-', $customVar);
            $token = $customData[0];
            $tId = $customData[1];
        } else {
            $token = $customVar;
        }

        // Verify token
        if (!$token || !Cart::verifySecurityToken($token, $tId)) {
            App::abort(500, 'Error processing your order. Failed to verify security token.');
        }

        // Get transaction info
        $tInfo = Cart::getTransactionFacts($tId);

        if (
            empty($tInfo->info->tStatus)
            || ($tInfo->info->tiCustomerStatus ?? '') != 'unconfirmed'
            || $tInfo->info->tStatus != 'completed'
        ) {
            Notify::error('Error processing your order...');

            App::redirect(
                Route::url('index.php?option=' . $this->option),
            );
        }

        // Transaction ok

        if (Pathway::count() <= 0) {
            Pathway::append(
                Lang::txt(strtoupper($this->option)),
                'index.php?option=' . $this->option,
            );
            Pathway::append(
                Lang::txt('Order complete'),
                'index.php?option=' . $this->option,
            );
        }

        // Display message
        $this->view->set('transactionInfo', $tInfo->info);
        $this->view->display();
    }

    /**
     * Place order (for orders with zero balances)
     *
     * @return  void
     */
    public function placeTask()
    {
        // Get the current active transaction
        $cart = new CurrentCart();

        $transaction = $cart->liftTransaction();

        if (!$transaction) {
            App::redirect(
                Route::url('index.php?option=com_cart'),
            );
        }

        // get security token (Parameter 0)
        $token = Request::getString('p0');

        if (!$token || !$cart->verifyToken($token)) {
            App::abort(500, 'Error processing your order. Bad security token.');
        }

        // Check if the order total is 0
        if ($transaction->info->tiTotal != 0) {
            App::abort(500, 'Cannot process transaction. Order total is not zero.');
        }

        // Check if the transaction's status is pending
        if ($transaction->info->tStatus != 'pending') {
            App::abort(500, 'Cannot process transaction. Transaction status is invalid.');
        }

        if ($this->completeOrder($transaction)) {
            // redirect to thank you page
            $redirect_url = Route::url('index.php?option=com_cart')
                . '/order/complete/?custom=' . $token . '-' . $transaction->info->tId;

            App::redirect(
                $redirect_url,
            );
        }
    }

    /**
     * Pay with the offline provider
     *
     * Offline takes payment outside the hub (or not at all, when testing), so
     * no gateway posts back: the member confirms the order here and it
     * completes. Its button used to post to a test controller removed long
     * ago, which left the order waiting for payment that could never arrive.
     *
     * The transaction token is no proof of anything on its own, since it is
     * built from a salt in the source; the order has to be the signed-in
     * member's own, on a form of the hub's, and waiting for payment.
     *
     * @return  void
     */
    public function payTask()
    {
        if (
            Request::getMethod() != 'POST'
            || Request::getWord('provider', '', 'post') != 'offline'
            || !Plugin::isEnabled('cart', 'offline')
        ) {
            App::abort(404, Lang::txt('Page not found'));
        }

        Request::checkToken();

        $custom = explode('-', Request::getString('custom', '', 'post'), 2);
        $token = $custom[0];
        $tId = (int) ($custom[1] ?? 0);

        if (!$tId || !Cart::verifySecurityToken($token, $tId)) {
            App::abort(403, 'Error processing your order. Failed to verify security token.');
        }

        $tInfo = Cart::getTransactionFacts($tId);

        if (
            !$tInfo
            || User::isGuest()
            || Cart::getCartUser($tInfo->info->crtId ?? null) !== (int) User::get('id')
        ) {
            App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
        }

        if (($tInfo->info->tStatus ?? '') != 'awaiting payment') {
            Notify::error('This order is not waiting for payment.');
            App::redirect(Route::url('index.php?option=' . $this->option, false));
        }

        $logger = new CartMessenger('Payment Offline');
        $logger->setMessage('Transaction completed. Transaction ID: ' . $tId);
        $logger->setPostback($_POST);
        $logger->log(LoggingLevel::INFO);

        $this->completeOrder($tInfo, ['Offline', false]);

        App::redirect(
            rtrim(Route::url('index.php?option=' . $this->option, false), '/')
                . '/order/complete?provider=offline&custom=' . urlencode($token . '-' . $tId),
        );
    }

    /**
     * Payment gateway postback: make sure everything checks out and complete transaction
     *
     * @return  void
     */
    public function postbackTask()
    {
        // some payment providers (Dummy, PayPal will do the postback,
        // need to call the plugins and handle appropriately)

        $params = Component::params(Request::getCmd('option'));

        if (empty($_POST)) {
            App::abort(404, Lang::txt('Page not found'));
        }

        // Initialize logger
        $logger = new CartMessenger('Payment Postback');

        // Get the plugins working
        $pay = Event::trigger('cart.onPostback', [$_POST, User::getRoot()]);

        foreach ($pay as $response) {
            if ($response) {
                if ($response['status'] == 'ok') {
                    $logger->setMessage($response['msg']);
                    $logger->setPostback($_POST);
                    $logger->log(LoggingLevel::INFO);

                    if (empty($response['payment'])) {
                        $response['payment'] = false;
                    }

                    $this->completeOrder($response['tInfo'], $response['payment']);
                } else {
                    $logger->setMessage($response['error']);
                    $logger->setPostback($_POST);
                    $logger->log(LoggingLevel::ERROR);
                }

                break;
            }
        }
    }

    /**
     * Complete transaction
     *
     * @param   \stdClass   $tInfo
     * @param   array<int, mixed>|false  $paymentInfo  Provider name and payment details
     * @return  bool
     */
    private function completeOrder($tInfo, $paymentInfo = false)
    {
        // Handle transaction according to items handlers
        Cart::completeTransaction($tInfo, $paymentInfo);

        // Send emails to customer and admin
        $logger = new CartMessenger('Complete order');
        $logger->emailOrderComplete($tInfo);

        return true;
    }
}
