<!--
status: generated
source: core/plugins/cart/*/*.xml
-->

# Cart plugins

Parameters of every plugin in the `cart` group, from each plugin's manifest. Set them under **Extensions > Plugins** in the administrator interface.

## Cart - Payment: Offline (`plg_cart_offline`)

Offline payment processor for the cart.

This plugin has no parameters.

## Cart - Payment: PayPal (`plg_cart_paypal`)

PayPal payment processor for the cart.

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `title` | Tab title | text | `PayPal` | Tab title |
| `description` | Description text | textarea | `Click on the button to pay with PayPal` | Description text |
| `env` | PLG_AUTHENTICATION_FACEBOOK_PARAM_SITELOGIN_LABEL | radio | `1` | PLG_AUTHENTICATION_FACEBOOK_PARAM_SITELOGIN_DESC. Options: `live` Live, `sandbox` Sandbox. |
| `receiver_email` | Paypal Email | text | — | Paypal Email Desc |
| `currency` | Paypal Currency | text | `USD` | Paypal Currency Desc |
| `secure_post` | Paypal Secure Post | radio | `0 (No)` | Paypal Secure Post Desc. Options: `0` No, `1` Yes. |
| `sandbox_receiver_email` | Paypal Sandbox Email | text | — | Paypal Sandbox Email Desc |

## Cart - Payment: UPay (`plg_cart_upay`)

UPay payment processor for the cart.

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `title` | Tab title | text | `PayPal` | Tab title |
| `paymentSiteId` | UPay site ID | text | — | UPay site ID |
| `paymentValidationKey` | Payment validation key | text | — | Payment validation key |
| `env` | Environment | radio | `1` | Environment. Options: `live` Live, `sandbox` Sandbox. |
