<!--
status: generated
source: Event::trigger('system.*') call sites and core/plugins/system/
-->

# System events

Events in the `system` group. A plugin in `core/plugins/system/` receives an event by defining a public method with the event's name; the arguments are those the call site passes, in order.

## `system.onAfterDispatch`

Fired from:

- [`core/bootstrap/Administrator/Providers/ComponentServiceProvider.php:52`](../../../core/bootstrap/Administrator/Providers/ComponentServiceProvider.php#L52)
- [`core/bootstrap/Site/Providers/ComponentServiceProvider.php:68`](../../../core/bootstrap/Site/Providers/ComponentServiceProvider.php#L68)
- [`core/libraries/Hubzero/Api/ComponentServiceProvider.php:55`](../../../core/libraries/Hubzero/Api/ComponentServiceProvider.php#L55)

Listeners:

- `plg_system_debug` — [`onAfterDispatch()`](../../../core/plugins/system/debug/debug.php)
- `plg_system_highlight` — [`onAfterDispatch()`](../../../core/plugins/system/highlight/highlight.php)
- `plg_system_languagefilter` — [`onAfterDispatch()`](../../../core/plugins/system/languagefilter/languagefilter.php)
- `plg_system_mobile` — [`onAfterDispatch()`](../../../core/plugins/system/mobile/mobile.php)

## `system.onAfterInitialise`

Fired from:

- [`core/libraries/Hubzero/Base/Application.php:477`](../../../core/libraries/Hubzero/Base/Application.php#L477)

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

- [`core/bootstrap/Administrator/Providers/DocumentServiceProvider.php:124`](../../../core/bootstrap/Administrator/Providers/DocumentServiceProvider.php#L124)
- [`core/bootstrap/Site/Providers/DocumentServiceProvider.php:155`](../../../core/bootstrap/Site/Providers/DocumentServiceProvider.php#L155)

Listeners:

- `plg_system_cache` — [`onAfterRender()`](../../../core/plugins/system/cache/cache.php)
- `plg_system_languagecode` — [`onAfterRender()`](../../../core/plugins/system/languagecode/languagecode.php)
- `plg_system_sef` — [`onAfterRender()`](../../../core/plugins/system/sef/sef.php)

## `system.onAfterRoute`

Fired from:

- [`core/bootstrap/Administrator/Providers/RouterServiceProvider.php:69`](../../../core/bootstrap/Administrator/Providers/RouterServiceProvider.php#L69)
- [`core/bootstrap/Api/Providers/RouterServiceProvider.php:48`](../../../core/bootstrap/Api/Providers/RouterServiceProvider.php#L48)
- [`core/bootstrap/Site/Providers/RouterServiceProvider.php:69`](../../../core/bootstrap/Site/Providers/RouterServiceProvider.php#L69)

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

- [`core/bootstrap/Administrator/Providers/DocumentServiceProvider.php:120`](../../../core/bootstrap/Administrator/Providers/DocumentServiceProvider.php#L120)
- [`core/bootstrap/Site/Providers/DocumentServiceProvider.php:145`](../../../core/bootstrap/Site/Providers/DocumentServiceProvider.php#L145)

No plugin in the source tree listens for this event.

## `system.onBeforeRenderSuperGroupComponent`

No call site in the CMS fires this event; the listener may answer an event fired by another package or by an older plugin.

Listeners:

- `plg_system_supergroup` — [`onBeforeRenderSuperGroupComponent()`](../../../core/plugins/system/supergroup/supergroup.php)

## `system.onBeforeRoute`

Fired from:

- [`core/bootstrap/Administrator/Providers/RouterServiceProvider.php:63`](../../../core/bootstrap/Administrator/Providers/RouterServiceProvider.php#L63)
- [`core/bootstrap/Api/Providers/RouterServiceProvider.php:42`](../../../core/bootstrap/Api/Providers/RouterServiceProvider.php#L42)
- [`core/bootstrap/Site/Providers/RouterServiceProvider.php:63`](../../../core/bootstrap/Site/Providers/RouterServiceProvider.php#L63)

No plugin in the source tree listens for this event.

## `system.onCleanCache`

Fired from:

- [`core/components/com_templates/admin/controllers/styles.php:470`](../../../core/components/com_templates/admin/controllers/styles.php#L470) with `[$group, $client_id]`

Listeners:

- `plg_system_cache` — [`onCleanCache($group = null, $client_id = 0)`](../../../core/plugins/system/cache/cache.php)

## `system.onConfigurationAfterSave`

No call site in the CMS fires this event; the listener may answer an event fired by another package or by an older plugin.

Listeners:

- `plg_system_massmail` — [`onConfigurationAfterSave($context, $table, $isNew)`](../../../core/plugins/system/massmail/massmail.php)

## `system.onContentDestroy`

Fired from:

- [`core/libraries/Hubzero/Database/Relational.php:4932`](../../../core/libraries/Hubzero/Database/Relational.php#L4932) with `[$this->getTableName(), $this]`
- [`core/libraries/Hubzero/Database/Traits/SoftDeletes.php:311`](../../../core/libraries/Hubzero/Database/Traits/SoftDeletes.php#L311) with `[$this->getTableName(), $this]`

Listeners:

- `plg_system_content` — [`onContentDestroy($table, $model)`](../../../core/plugins/system/content/content.php)

## `system.onContentPrepareForm`

No call site in the CMS fires this event; the listener may answer an event fired by another package or by an older plugin.

Listeners:

- `plg_system_languagecode` — [`onContentPrepareForm($form, $data)`](../../../core/plugins/system/languagecode/languagecode.php)

## `system.onContentRestore`

Fired from:

- [`core/libraries/Hubzero/Database/Tests/LifecycleHooksTest.php:566`](../../../core/libraries/Hubzero/Database/Tests/LifecycleHooksTest.php#L566)
- [`core/libraries/Hubzero/Database/Traits/SoftDeletes.php:343`](../../../core/libraries/Hubzero/Database/Traits/SoftDeletes.php#L343) with `[$this->getTableName(), $this]`

No plugin in the source tree listens for this event.

## `system.onContentSave`

Fired from:

- [`core/components/com_groups/models/orm/applicant.php:123`](../../../core/components/com_groups/models/orm/applicant.php#L123) with `[$this->getTableName(), $this]`
- [`core/components/com_groups/models/orm/invitee.php:123`](../../../core/components/com_groups/models/orm/invitee.php#L123) with `[$this->getTableName(), $this]`
- [`core/components/com_groups/models/orm/manager.php:123`](../../../core/components/com_groups/models/orm/manager.php#L123) with `[$this->getTableName(), $this]`
- [`core/components/com_groups/models/orm/member.php:124`](../../../core/components/com_groups/models/orm/member.php#L124) with `[$this->getTableName(), $this]`
- [`core/components/com_modules/models/menu.php:71`](../../../core/components/com_modules/models/menu.php#L71) with `[$this->getTableName(), $this]`
- [`core/libraries/Hubzero/Database/Relational.php:4457`](../../../core/libraries/Hubzero/Database/Relational.php#L4457) with `[$this->getTableName(), $this, $this->changes]`
- [`core/libraries/Hubzero/Database/Table.php:736`](../../../core/libraries/Hubzero/Database/Table.php#L736) with `[$this->getTableName(), $this]`

Listeners:

- `plg_system_content` — [`onContentSave($table, $model)`](../../../core/plugins/system/content/content.php)

## `system.onContentSoftDelete`

Fired from:

- [`core/libraries/Hubzero/Database/Tests/LifecycleHooksTest.php:531`](../../../core/libraries/Hubzero/Database/Tests/LifecycleHooksTest.php#L531)
- [`core/libraries/Hubzero/Database/Traits/SoftDeletes.php:241`](../../../core/libraries/Hubzero/Database/Traits/SoftDeletes.php#L241) with `[$this->getTableName(), $this]`

No plugin in the source tree listens for this event.

## `system.onMassPrune`

Fired from:

- [`core/libraries/Hubzero/Database/Traits/MassPrunable.php:134`](../../../core/libraries/Hubzero/Database/Traits/MassPrunable.php#L134) with `[$instance->getTableName(), $query]`

No plugin in the source tree listens for this event.

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

- `plg_system_languagefilter` — [`onUserLogin($user, $options = [])`](../../../core/plugins/system/languagefilter/languagefilter.php)

## `system.onUserLoginFailure`

No call site in the CMS fires this event; the listener may answer an event fired by another package or by an older plugin.

Listeners:

- `plg_system_hubzero` — [`onUserLoginFailure($response)`](../../../core/plugins/system/hubzero/hubzero.php)
- `plg_system_log` — [`onUserLoginFailure($response)`](../../../core/plugins/system/log/log.php)

## `system.onUserLogout`

No call site in the CMS fires this event; the listener may answer an event fired by another package or by an older plugin.

Listeners:

- `plg_system_logout` — [`onUserLogout($user, $options = [])`](../../../core/plugins/system/logout/logout.php)
