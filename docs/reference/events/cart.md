<!--
status: generated
source: Event::trigger('cart.*') call sites and core/plugins/cart/
-->

# Cart events

Events in the `cart` group. A plugin in `core/plugins/cart/` receives an event by defining a public method with the event's name; the arguments are those the call site passes, in order.

## `cart.onComplete`

Fired from:

- [`core/components/com_cart/site/controllers/order.php:69`](../../../core/components/com_cart/site/controllers/order.php#L69) with `[$provider]`

Listeners:

- `plg_cart_offline` — [`onComplete($provider)`](../../../core/plugins/cart/offline/offline.php)
- `plg_cart_upay` — [`onComplete($provider)`](../../../core/plugins/cart/upay/upay.php)

## `cart.onPostback`

Fired from:

- [`core/components/com_cart/site/controllers/order.php:220`](../../../core/components/com_cart/site/controllers/order.php#L220) with `[$_POST, User::getRoot()]`

Listeners:

- `plg_cart_offline` — [`onPostback($postData)`](../../../core/plugins/cart/offline/offline.php)
- `plg_cart_upay` — [`onPostback($postData)`](../../../core/plugins/cart/upay/upay.php)

## `cart.onProcessPayment`

Fired from:

- [`core/components/com_cart/site/controllers/checkout.php:594`](../../../core/components/com_cart/site/controllers/checkout.php#L594) with `[$transaction, User::getInstance()]`

Listeners:

- `plg_cart_paypal` — [`onProcessPayment($transaction, $user)`](../../../core/plugins/cart/paypal/paypal.php)

## `cart.onRenderPaymentOptions`

Fired from:

- [`core/components/com_cart/site/views/checkout/tmpl/payment.php:37`](../../../core/components/com_cart/site/views/checkout/tmpl/payment.php#L37) with `[$this->transaction, User::getRoot()]`

Listeners:

- `plg_cart_offline` — [`onRenderPaymentOptions($transaction, $user)`](../../../core/plugins/cart/offline/offline.php)
- `plg_cart_paypal` — [`onRenderPaymentOptions($cart, $user)`](../../../core/plugins/cart/paypal/paypal.php)
- `plg_cart_upay` — [`onRenderPaymentOptions($cart, $user)`](../../../core/plugins/cart/upay/upay.php)

## `cart.onSelectedPayment`

Fired from:

- [`core/components/com_cart/site/controllers/checkout.php:568`](../../../core/components/com_cart/site/controllers/checkout.php#L568) with `[$transaction, User::getInstance()]`

Listeners:

- `plg_cart_offline` — [`onSelectedPayment($transaction, $user)`](../../../core/plugins/cart/offline/offline.php)
- `plg_cart_upay` — [`onSelectedPayment($transaction, $user)`](../../../core/plugins/cart/upay/upay.php)
