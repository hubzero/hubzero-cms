<!--
status: generated
source: Event::trigger('authentication.*') call sites and core/plugins/authentication/
-->

# Authentication events

Events in the `authentication` group. A plugin in `core/plugins/authentication/` receives an event by defining a public method with the event's name; the arguments are those the call site passes, in order.

## `authentication.onAuthenticate`

No call site in the CMS fires this event; the listener may answer an event fired by another package or by an older plugin.

Listeners:

- `plg_authentication_certificate` — [`onAuthenticate($credentials, $options, &$response)`](../../../core/plugins/authentication/certificate/certificate.php)
- `plg_authentication_emailtoken` — [`onAuthenticate($credentials, $options, &$response)`](../../../core/plugins/authentication/emailtoken/emailtoken.php)
- `plg_authentication_google` — [`onAuthenticate($credentials, $options, &$response)`](../../../core/plugins/authentication/google/google.php)
- `plg_authentication_hubzero` — [`onAuthenticate($credentials, $options, &$response)`](../../../core/plugins/authentication/hubzero/hubzero.php)
- `plg_authentication_linkedin` — [`onAuthenticate($credentials, $options, &$response)`](../../../core/plugins/authentication/linkedin/linkedin.php)
- `plg_authentication_pucas` — [`onAuthenticate($credentials, $options, &$response)`](../../../core/plugins/authentication/pucas/pucas.php)
- `plg_authentication_scistarter` — [`onAuthenticate($credentials, $options, &$response)`](../../../core/plugins/authentication/scistarter/scistarter.php)
- `plg_authentication_shibboleth` — [`onAuthenticate($credentials, $options, &$response)`](../../../core/plugins/authentication/shibboleth/shibboleth.php)
- `plg_authentication_twitter` — [`onAuthenticate($credentials, $options, &$response)`](../../../core/plugins/authentication/twitter/twitter.php)

## `authentication.onUserAuthenticate`

No call site in the CMS fires this event; the listener may answer an event fired by another package or by an older plugin.

Listeners:

- `plg_authentication_certificate` — [`onUserAuthenticate($credentials, $options, &$response)`](../../../core/plugins/authentication/certificate/certificate.php)
- `plg_authentication_cilogon` — [`onUserAuthenticate($credentials, $options, &$response)`](../../../core/plugins/authentication/cilogon/cilogon.php)
- `plg_authentication_emailtoken` — [`onUserAuthenticate($credentials, $options, &$response)`](../../../core/plugins/authentication/emailtoken/emailtoken.php)
- `plg_authentication_facebook` — [`onUserAuthenticate($credentials, $options, &$response)`](../../../core/plugins/authentication/facebook/facebook.php)
- `plg_authentication_globus` — [`onUserAuthenticate($credentials, $options, &$response)`](../../../core/plugins/authentication/globus/globus.php)
- `plg_authentication_google` — [`onUserAuthenticate($credentials, $options, &$response)`](../../../core/plugins/authentication/google/google.php)
- `plg_authentication_hubzero` — [`onUserAuthenticate($credentials, $options, &$response)`](../../../core/plugins/authentication/hubzero/hubzero.php)
- `plg_authentication_linkedin` — [`onUserAuthenticate($credentials, $options, &$response)`](../../../core/plugins/authentication/linkedin/linkedin.php)
- `plg_authentication_orcid` — [`onUserAuthenticate($credentials, $options, &$response)`](../../../core/plugins/authentication/orcid/orcid.php)
- `plg_authentication_pucas` — [`onUserAuthenticate($credentials, $options, &$response)`](../../../core/plugins/authentication/pucas/pucas.php)
- `plg_authentication_scistarter` — [`onUserAuthenticate($credentials, $options, &$response)`](../../../core/plugins/authentication/scistarter/scistarter.php)
- `plg_authentication_shibboleth` — [`onUserAuthenticate($credentials, $options, &$response)`](../../../core/plugins/authentication/shibboleth/shibboleth.php)
- `plg_authentication_twitter` — [`onUserAuthenticate($credentials, $options, &$response)`](../../../core/plugins/authentication/twitter/twitter.php)

## `authentication.onUserLogout`

No call site in the CMS fires this event; the listener may answer an event fired by another package or by an older plugin.

Listeners:

- `plg_authentication_pucas` — [`onUserLogout()`](../../../core/plugins/authentication/pucas/pucas.php)
