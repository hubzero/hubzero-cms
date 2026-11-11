<!--
status: generated
source: Event::trigger('captcha.*') call sites and core/plugins/captcha/
-->

# Captcha events

Events in the `captcha` group. A plugin in `core/plugins/captcha/` receives an event by defining a public method with the event's name; the arguments are those the call site passes, in order.

## `captcha.onCheckAnswer`

Fired from:

- [`core/components/com_members/models/registration.php:645`](../../../core/components/com_members/models/registration.php#L645)
- [`core/components/com_members/site/controllers/register.php:1244`](../../../core/components/com_members/site/controllers/register.php#L1244)
- [`core/components/com_support/site/controllers/tickets.php:895`](../../../core/components/com_support/site/controllers/tickets.php#L895)

Listeners:

- `plg_captcha_image` — [`onCheckAnswer($code = null)`](../../../core/plugins/captcha/image/image.php)
- `plg_captcha_math` — [`onCheckAnswer($code = null)`](../../../core/plugins/captcha/math/math.php)
- `plg_captcha_recaptcha` — [`onCheckAnswer($code = null)`](../../../core/plugins/captcha/recaptcha/recaptcha.php)

## `captcha.onDisplay`

Fired from:

- [`core/components/com_members/site/views/register/tmpl/default.php:625`](../../../core/components/com_members/site/views/register/tmpl/default.php#L625)
- [`core/components/com_members/site/views/register/tmpl/resend_request.php:42`](../../../core/components/com_members/site/views/register/tmpl/resend_request.php#L42)
- [`core/components/com_support/site/controllers/tickets.php:793`](../../../core/components/com_support/site/controllers/tickets.php#L793)

Listeners:

- `plg_captcha_image` — [`onDisplay($name = null, $id = 'image_captcha_1', $class = '')`](../../../core/plugins/captcha/image/image.php)
- `plg_captcha_math` — [`onDisplay($name = null, $id = 'image_captcha_1', $class = '')`](../../../core/plugins/captcha/math/math.php)
- `plg_captcha_recaptcha` — [`onDisplay($name = null, $id = 'dynamic_recaptcha_1', $class = '')`](../../../core/plugins/captcha/recaptcha/recaptcha.php)

## `captcha.onInit`

No call site in the CMS fires this event; the listener may answer an event fired by another package or by an older plugin.

Listeners:

- `plg_captcha_image` — [`onInit($id = 'image_captcha_1')`](../../../core/plugins/captcha/image/image.php)
- `plg_captcha_recaptcha` — [`onInit($id = 'dynamic_recaptcha_1')`](../../../core/plugins/captcha/recaptcha/recaptcha.php)
