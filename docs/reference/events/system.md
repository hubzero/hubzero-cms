<!--
status: generated
source: Event::trigger('system.*') call sites and core/plugins/system/
-->

# System events

Events in the `system` group. A plugin in `core/plugins/system/` receives an event by defining a public method with the event's name; the arguments are those the call site passes, in order.

## `system.onAfterDispatch`

Fired from:

- [`core/bootstrap/Administrator/Providers/ComponentServiceProvider.php:54`](../../../core/bootstrap/Administrator/Providers/ComponentServiceProvider.php#L54)
- [`core/bootstrap/Site/Providers/ComponentServiceProvider.php:54`](../../../core/bootstrap/Site/Providers/ComponentServiceProvider.php#L54)
- [`core/libraries/Hubzero/Api/ComponentServiceProvider.php:58`](../../../core/libraries/Hubzero/Api/ComponentServiceProvider.php#L58)

Listeners:

- `plg_system_debug` — [`onAfterDispatch()`](../../../core/plugins/system/debug/debug.php)
- `plg_system_highlight` — [`onAfterDispatch()`](../../../core/plugins/system/highlight/highlight.php)
- `plg_system_languagefilter` — [`onAfterDispatch()`](../../../core/plugins/system/languagefilter/languagefilter.php)
- `plg_system_mobile` — [`onAfterDispatch()`](../../../core/plugins/system/mobile/mobile.php)

## `system.onAfterInitialise`

Fired from:

- [`core/libraries/Hubzero/Base/Application.php:516`](../../../core/libraries/Hubzero/Base/Application.php#L516)

Listeners:

- `plg_system_cache` — [`onAfterInitialise()`](../../../core/plugins/system/cache/cache.php)
- `plg_system_csp` — [`onAfterInitialise()`](../../../core/plugins/system/csp/csp.php)
- `plg_system_hubzero` — [`onAfterInitialise()`](../../../core/plugins/system/hubzero/hubzero.php)
- `plg_system_languagefilter` — [`onAfterInitialise()`](../../../core/plugins/system/languagefilter/languagefilter.php)
- `plg_system_p3p` — [`onAfterInitialise()`](../../../core/plugins/system/p3p/p3p.php)
- `plg_system_referrerpolicy` — [`onAfterInitialise()`](../../../core/plugins/system/referrerpolicy/referrerpolicy.php)
- `plg_system_remember` — [`onAfterInitialise()`](../../../core/plugins/system/remember/remember.php)
- `plg_system_supergroup` — [`onAfterInitialise()`](../../../core/plugins/system/supergroup/supergroup.php)
- `plg_system_xfeed` — [`onAfterInitialise()`](../../../core/plugins/system/xfeed/xfeed.php)

## `system.onAfterRender`

Fired from:

- [`core/bootstrap/Administrator/Providers/DocumentServiceProvider.php:128`](../../../core/bootstrap/Administrator/Providers/DocumentServiceProvider.php#L128)
- [`core/bootstrap/Site/Providers/DocumentServiceProvider.php:152`](../../../core/bootstrap/Site/Providers/DocumentServiceProvider.php#L152)

Listeners:

- `plg_system_cache` — [`onAfterRender()`](../../../core/plugins/system/cache/cache.php)
- `plg_system_languagecode` — [`onAfterRender()`](../../../core/plugins/system/languagecode/languagecode.php)
- `plg_system_sef` — [`onAfterRender()`](../../../core/plugins/system/sef/sef.php)

## `system.onAfterRoute`

Fired from:

- [`core/bootstrap/Administrator/Providers/RouterServiceProvider.php:73`](../../../core/bootstrap/Administrator/Providers/RouterServiceProvider.php#L73)
- [`core/bootstrap/Api/Providers/RouterServiceProvider.php:50`](../../../core/bootstrap/Api/Providers/RouterServiceProvider.php#L50)
- [`core/bootstrap/Site/Providers/RouterServiceProvider.php:73`](../../../core/bootstrap/Site/Providers/RouterServiceProvider.php#L73)

Listeners:

- `plg_system_authfactors` — [`onAfterRoute()`](../../../core/plugins/system/authfactors/authfactors.php)
- `plg_system_certificate` — [`onAfterRoute()`](../../../core/plugins/system/certificate/certificate.php)
- `plg_system_incomplete` — [`onAfterRoute()`](../../../core/plugins/system/incomplete/incomplete.php)
- `plg_system_jquery` — [`onAfterRoute()`](../../../core/plugins/system/jquery/jquery.php)
- `plg_system_memberhome` — [`onAfterRoute()`](../../../core/plugins/system/memberhome/memberhome.php)
- `plg_system_password` — [`onAfterRoute()`](../../../core/plugins/system/password/password.php)
- `plg_system_spamjail` — [`onAfterRoute()`](../../../core/plugins/system/spamjail/spamjail.php)
- `plg_system_unapproved` — [`onAfterRoute()`](../../../core/plugins/system/unapproved/unapproved.php)
- `plg_system_unconfirmed` — [`onAfterRoute()`](../../../core/plugins/system/unconfirmed/unconfirmed.php)
- `plg_system_userconsent` — [`onAfterRoute()`](../../../core/plugins/system/userconsent/userconsent.php)

## `system.onBeforeRender`

Fired from:

- [`core/bootstrap/Administrator/Providers/DocumentServiceProvider.php:124`](../../../core/bootstrap/Administrator/Providers/DocumentServiceProvider.php#L124)
- [`core/bootstrap/Site/Providers/DocumentServiceProvider.php:148`](../../../core/bootstrap/Site/Providers/DocumentServiceProvider.php#L148)

No plugin in the source tree listens for this event.

## `system.onBeforeRenderSuperGroupComponent`

No call site in the CMS fires this event; the listener may answer an event fired by another package or by an older plugin.

Listeners:

- `plg_system_supergroup` — [`onBeforeRenderSuperGroupComponent()`](../../../core/plugins/system/supergroup/supergroup.php)

## `system.onBeforeRoute`

Fired from:

- [`core/bootstrap/Administrator/Providers/RouterServiceProvider.php:66`](../../../core/bootstrap/Administrator/Providers/RouterServiceProvider.php#L66)
- [`core/bootstrap/Api/Providers/RouterServiceProvider.php:43`](../../../core/bootstrap/Api/Providers/RouterServiceProvider.php#L43)
- [`core/bootstrap/Site/Providers/RouterServiceProvider.php:66`](../../../core/bootstrap/Site/Providers/RouterServiceProvider.php#L66)

No plugin in the source tree listens for this event.

## `system.onCleanCache`

Fired from:

- [`core/components/com_templates/admin/controllers/styles.php:492`](../../../core/components/com_templates/admin/controllers/styles.php#L492) with `[$group, $client_id]`

Listeners:

- `plg_system_cache` — [`onCleanCache($group = null, $client_id = 0)`](../../../core/plugins/system/cache/cache.php)

## `system.onContentDestroy`

Fired from:

- [`core/libraries/Hubzero/Database/Relational.php:1631`](../../../core/libraries/Hubzero/Database/Relational.php#L1631) with `[$this->getTableName(), $this]`

Listeners:

- `plg_system_content` — [`onContentDestroy($table, $model)`](../../../core/plugins/system/content/content.php)

## `system.onContentPrepareForm`

No call site in the CMS fires this event; the listener may answer an event fired by another package or by an older plugin.

Listeners:

- `plg_system_languagecode` — [`onContentPrepareForm($form, $data)`](../../../core/plugins/system/languagecode/languagecode.php)

## `system.onContentSave`

Fired from:

- [`core/components/com_groups/models/orm/applicant.php:124`](../../../core/components/com_groups/models/orm/applicant.php#L124) with `[$this->getTableName(), $this]`
- [`core/components/com_groups/models/orm/invitee.php:124`](../../../core/components/com_groups/models/orm/invitee.php#L124) with `[$this->getTableName(), $this]`
- [`core/components/com_groups/models/orm/manager.php:124`](../../../core/components/com_groups/models/orm/manager.php#L124) with `[$this->getTableName(), $this]`
- [`core/components/com_groups/models/orm/member.php:124`](../../../core/components/com_groups/models/orm/member.php#L124) with `[$this->getTableName(), $this]`
- [`core/components/com_modules/models/menu.php:71`](../../../core/components/com_modules/models/menu.php#L71) with `[$this->getTableName(), $this]`
- [`core/libraries/Hubzero/Database/Relational.php:1451`](../../../core/libraries/Hubzero/Database/Relational.php#L1451) with `[$this->getTableName(), $this]`
- [`core/libraries/Hubzero/Database/Table.php:582`](../../../core/libraries/Hubzero/Database/Table.php#L582) with `[$this->getTableName(), $this]`

Listeners:

- `plg_system_content` — [`onContentSave($table, $model)`](../../../core/plugins/system/content/content.php)

## `system.onUserAfterSave`

No call site in the CMS fires this event; the listener may answer an event fired by another package or by an older plugin.

Listeners:

- `plg_system_languagefilter` — [`onUserAfterSave($user, $isnew, $success, $msg)`](../../../core/plugins/system/languagefilter/languagefilter.php)

## `system.onUserBeforeSave`

No call site in the CMS fires this event; the listener may answer an event fired by another package or by an older plugin.

Listeners:

- `plg_system_languagefilter` — [`onUserBeforeSave($user, $isnew, $new)`](../../../core/plugins/system/languagefilter/languagefilter.php)

## `system.onUserLogin`

No call site in the CMS fires this event; the listener may answer an event fired by another package or by an older plugin.

Listeners:

- `plg_system_languagefilter` — [`onUserLogin($user, $options = array()`](../../../core/plugins/system/languagefilter/languagefilter.php)

## `system.onUserLoginFailure`

No call site in the CMS fires this event; the listener may answer an event fired by another package or by an older plugin.

Listeners:

- `plg_system_hubzero` — [`onUserLoginFailure($response)`](../../../core/plugins/system/hubzero/hubzero.php)
- `plg_system_log` — [`onUserLoginFailure($response)`](../../../core/plugins/system/log/log.php)

## `system.onUserLogout`

No call site in the CMS fires this event; the listener may answer an event fired by another package or by an older plugin.

Listeners:

- `plg_system_logout` — [`onUserLogout($user, $options = array()`](../../../core/plugins/system/logout/logout.php)
