<!--
status: generated
source: Event::trigger('authfactors.*') call sites and core/plugins/authfactors/
-->

# Authfactors events

Events in the `authfactors` group. A plugin in `core/plugins/authfactors/` receives an event by defining a public method with the event's name; the arguments are those the call site passes, in order.

## `authfactors.onRenderChallenge`

Fired from:

- [`core/components/com_login/admin/controllers/login.php:164`](../../../core/components/com_login/admin/controllers/login.php#L164)
- [`core/components/com_login/site/controllers/auth.php:507`](../../../core/components/com_login/site/controllers/auth.php#L507)
- [`core/components/com_users/site/controllers/auth.php:542`](../../../core/components/com_users/site/controllers/auth.php#L542)

Listeners:

- `plg_authfactors_certificate` — [`onRenderChallenge()`](../../../core/plugins/authfactors/certificate/certificate.php)
- `plg_authfactors_google` — [`onRenderChallenge()`](../../../core/plugins/authfactors/google/google.php)
