<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Cart\Site\Controllers;

use Exception;

/**
 * Cart AJAX requests
 */
class Request extends ComponentController
{
    /**
     * Add an item to a cart
     *
     * @return  void
     */
    public function addTask()
    {
        $response = new \stdClass();
        $response->status = 'ok';

        $cart = new \Components\Cart\Models\CurrentCart();

        // update cart
        $updateCartRequest = \Hubzero\Facades\Request::getBool('updateCart', false, 'post');
        $pIds = \Hubzero\Facades\Request::getArray('pId', false, 'post');

        // If pIds are posted, convert them to SKUs
        if (!empty($pIds)) {
            $skus = array();

            $warehouse = new \Components\Storefront\Models\Warehouse();

            foreach ($pIds as $pId => $qty) {
                $product_skus = $warehouse->getProductSkus($pId);

                // must be only one sku to work
                if (count($product_skus) != 1) {
                    // each pId must map to one SKU, otherwise ignored
                    continue;
                }

                $skus[$product_skus[0]] = $qty;
            }
        } else {
            $skus = \Hubzero\Facades\Request::getArray('skus', false, 'post');
        }

        // Initialize errors array
        $errors = array();

        if ($updateCartRequest && $skus) {
            // Turn off syncing to prevent redundant session update queries
            $cart->setSync(false);
            foreach ($skus as $sId => $qty) {
                try {
                    $cart->update($sId, $qty);
                } catch (Exception $e) {
                    $errors[] = $e->getMessage();
                }
            }
        }

        // add coupon if needed
        $addCouponRequest = \Hubzero\Facades\Request::getBool('addCouponCode', false, 'post');
        $couponCode = \Hubzero\Facades\Request::getString('couponCode', false, 'post');

        if ($addCouponRequest && $couponCode) {
            // Sync cart before pontial coupons applying
            $cart->getCartInfo(true);

            // Initialize errors array
            $errors = array();

            // Add coupon
            try {
                $cart->addCoupon($couponCode);
            } catch (Exception $e) {
                $errors[] = $e->getMessage();
            }
        }

        if (!empty($errors)) {
            $response->status = 'error';
            $response->errors = $errors;
        }

        echo htmlspecialchars(json_encode($response), ENT_NOQUOTES);
        die();
    }
}
