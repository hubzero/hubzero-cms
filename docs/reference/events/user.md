<!--
status: generated
source: Event::trigger('user.*') call sites and core/plugins/user/
-->

# User events

Events in the `user` group. A plugin in `core/plugins/user/` receives an event by defining a public method with the event's name; the arguments are those the call site passes, in order.

## `user.onAfterDeleteGroup`

No call site in the CMS fires this event; the listener may answer an event fired by another package or by an older plugin.

Listeners:

- `plg_user_ldap` — [`onAfterDeleteGroup($group)`](../../../core/plugins/user/ldap/ldap.php)

## `user.onAfterDeletePassword`

Fired from:

- [`core/libraries/Hubzero/User/Password.php:390`](../../../core/libraries/Hubzero/User/Password.php#L390) with `[$this]`

Listeners:

- `plg_user_ldap` — [`onAfterDeletePassword($user)`](../../../core/plugins/user/ldap/ldap.php)

## `user.onAfterDeleteProfile`

Fired from:

- [`core/libraries/Hubzero/User/Profile.php:1060`](../../../core/libraries/Hubzero/User/Profile.php#L1060) with `[$this]`

Listeners:

- `plg_user_constantcontact` — [`onAfterDeleteProfile($user)`](../../../core/plugins/user/constantcontact/constantcontact.php)
- `plg_user_ldap` — [`onAfterDeleteProfile($user)`](../../../core/plugins/user/ldap/ldap.php)

## `user.onAfterDeleteUser`

No call site in the CMS fires this event; the listener may answer an event fired by another package or by an older plugin.

Listeners:

- `plg_user_d1` — [`onAfterDeleteUser($user, $success, $msg)`](../../../core/plugins/user/d1/d1.php)
- `plg_user_ldap` — [`onAfterDeleteUser($user, $success, $msg)`](../../../core/plugins/user/ldap/ldap.php)
- `plg_user_us` — [`onAfterDeleteUser($user, $success, $msg)`](../../../core/plugins/user/us/us.php)
- `plg_user_xusers` — [`onAfterDeleteUser($user, $success, $msg)`](../../../core/plugins/user/xusers/xusers.php)

## `user.onAfterStoreGroup`

Fired from:

- [`core/components/com_groups/models/orm/group.php:585`](../../../core/components/com_groups/models/orm/group.php#L585) with `[$this]`
- [`core/components/com_groups/models/orm/group.php:673`](../../../core/components/com_groups/models/orm/group.php#L673) with `[$this]`
- [`core/libraries/Hubzero/User/Group.php:349`](../../../core/libraries/Hubzero/User/Group.php#L349) with `[$this]`
- [`core/libraries/Hubzero/User/Group.php:665`](../../../core/libraries/Hubzero/User/Group.php#L665) with `[$this]`
- [`core/libraries/Hubzero/User/Group.php:722`](../../../core/libraries/Hubzero/User/Group.php#L722) with `[$this]`
- [`core/libraries/Hubzero/User/Group/Membership.php:1140`](../../../core/libraries/Hubzero/User/Group/Membership.php#L1140) with `[$group]`

Listeners:

- `plg_user_ldap` — [`onAfterStoreGroup($group)`](../../../core/plugins/user/ldap/ldap.php)

## `user.onAfterStorePassword`

Fired from:

- [`core/libraries/Hubzero/User/Password.php:342`](../../../core/libraries/Hubzero/User/Password.php#L342) with `[$this]`

Listeners:

- `plg_user_ldap` — [`onAfterStorePassword($user)`](../../../core/plugins/user/ldap/ldap.php)

## `user.onAfterStoreProfile`

Fired from:

- [`core/components/com_members/admin/controllers/hosts.php:66`](../../../core/components/com_members/admin/controllers/hosts.php#L66) with `[$profile]`
- [`core/components/com_members/admin/controllers/hosts.php:114`](../../../core/components/com_members/admin/controllers/hosts.php#L114) with `[$profile]`
- [`core/libraries/Hubzero/User/Profile.php:999`](../../../core/libraries/Hubzero/User/Profile.php#L999) with `[$this]`

Listeners:

- `plg_user_constantcontact` — [`onAfterStoreProfile($user)`](../../../core/plugins/user/constantcontact/constantcontact.php)
- `plg_user_ldap` — [`onAfterStoreProfile($user)`](../../../core/plugins/user/ldap/ldap.php)

## `user.onAfterStoreUser`

Fired from:

- [`core/plugins/members/account/account.php:597`](../../../core/plugins/members/account/account.php#L597) with `[$this->user->toArray(), false, null, $this->getError()]`

Listeners:

- `plg_user_ldap` — [`onAfterStoreUser($user, $isnew, $success, $msg)`](../../../core/plugins/user/ldap/ldap.php)
- `plg_user_xusers` — [`onAfterStoreUser($user, $isnew, $success, $msg)`](../../../core/plugins/user/xusers/xusers.php)

## `user.onBeforeStoreUser`

Fired from:

- [`core/plugins/members/account/account.php:535`](../../../core/plugins/members/account/account.php#L535) with `[$this->user->toArray(), false]`

No plugin in the source tree listens for this event.

## `user.onLoginUser`

No call site in the CMS fires this event; the listener may answer an event fired by another package or by an older plugin.

Listeners:

- `plg_user_autoapprove` — [`onLoginUser($user, $options = array()`](../../../core/plugins/user/autoapprove/autoapprove.php)
- `plg_user_d1` — [`onLoginUser($user, $options = array()`](../../../core/plugins/user/d1/d1.php)
- `plg_user_us` — [`onLoginUser($user, $options = array()`](../../../core/plugins/user/us/us.php)
- `plg_user_xusers` — [`onLoginUser($user, $options = array()`](../../../core/plugins/user/xusers/xusers.php)

## `user.onLogoutUser`

No call site in the CMS fires this event; the listener may answer an event fired by another package or by an older plugin.

Listeners:

- `plg_user_xusers` — [`onLogoutUser($user, $options = array()`](../../../core/plugins/user/xusers/xusers.php)

## `user.onUserAfterDelete`

Fired from:

- [`core/components/com_members/admin/controllers/members.php:899`](../../../core/components/com_members/admin/controllers/members.php#L899) with `[$data, true, $this->getError()]`
- [`core/components/com_members/models/member.php:536`](../../../core/components/com_members/models/member.php#L536) with `[$data, true, $this->getError()]`
- [`core/libraries/Hubzero/User/User.php:1214`](../../../core/libraries/Hubzero/User/User.php#L1214) with `[$data, true, $this->getError()]`

Listeners:

- `plg_user_d1` — [`onUserAfterDelete($user, $success, $msg)`](../../../core/plugins/user/d1/d1.php)
- `plg_user_geo` — [`onUserAfterDelete($user, $succes, $msg)`](../../../core/plugins/user/geo/geo.php)
- `plg_user_hubzero` — [`onUserAfterDelete($user, $succes, $msg)`](../../../core/plugins/user/hubzero/hubzero.php)
- `plg_user_ldap` — [`onUserAfterDelete($user, $success, $msg)`](../../../core/plugins/user/ldap/ldap.php)
- `plg_user_middleware` — [`onUserAfterDelete($user, $success, $msg)`](../../../core/plugins/user/middleware/middleware.php)
- `plg_user_us` — [`onUserAfterDelete($user, $success, $msg)`](../../../core/plugins/user/us/us.php)
- `plg_user_xusers` — [`onUserAfterDelete($user, $success, $msg)`](../../../core/plugins/user/xusers/xusers.php)

## `user.onUserAfterDeleteGroup`

Fired from:

- [`core/components/com_members/admin/controllers/accessgroups.php:323`](../../../core/components/com_members/admin/controllers/accessgroups.php#L323) with `[$data, true, $this->getError()]`

No plugin in the source tree listens for this event.

## `user.onUserAfterSave`

Fired from:

- [`core/components/com_cart/lib/handlers/type/Access_Group_Membership_Type_Handler.php:61`](../../../core/components/com_cart/lib/handlers/type/Access_Group_Membership_Type_Handler.php#L61) with `[$table->toArray(), false, true, null]`
- [`core/libraries/Hubzero/User/User.php:1161`](../../../core/libraries/Hubzero/User/User.php#L1161) with `[$data, $isNew, $result, $this->getError()]`

Listeners:

- `plg_user_autoapprove` — [`onUserAfterSave($user, $isnew, $success, $msg)`](../../../core/plugins/user/autoapprove/autoapprove.php)
- `plg_user_domainrestriction` — [`onUserAfterSave($user, $isnew, $success, $msg)`](../../../core/plugins/user/domainrestriction/domainrestriction.php)
- `plg_user_hubzero` — [`onUserAfterSave($user, $isnew, $success, $msg)`](../../../core/plugins/user/hubzero/hubzero.php)
- `plg_user_ldap` — [`onUserAfterSave($user, $isnew, $success, $msg)`](../../../core/plugins/user/ldap/ldap.php)
- `plg_user_middleware` — [`onUserAfterSave($user, $isnew, $success, $msg)`](../../../core/plugins/user/middleware/middleware.php)
- `plg_user_xusers` — [`onUserAfterSave($user, $isnew, $success, $msg)`](../../../core/plugins/user/xusers/xusers.php)

## `user.onUserAfterSaveProfile`

Fired from:

- [`core/components/com_members/models/member.php:482`](../../../core/components/com_members/models/member.php#L482) with `[$user, $profile, $access]`

No plugin in the source tree listens for this event.

## `user.onUserAuthorisation`

Fired from:

- [`core/libraries/Hubzero/Auth/Guard.php:282`](../../../core/libraries/Hubzero/Auth/Guard.php#L282) with `[$response, $options]`

No plugin in the source tree listens for this event.

## `user.onUserAuthorisationFailure`

Fired from:

- [`core/libraries/Hubzero/Auth/Manager.php:76`](../../../core/libraries/Hubzero/Auth/Manager.php#L76) with `[(array) $authorisation]`

No plugin in the source tree listens for this event.

## `user.onUserBeforeDelete`

Fired from:

- [`core/components/com_members/models/member.php:496`](../../../core/components/com_members/models/member.php#L496) with `[$data]`
- [`core/libraries/Hubzero/User/User.php:1185`](../../../core/libraries/Hubzero/User/User.php#L1185) with `[$data]`

No plugin in the source tree listens for this event.

## `user.onUserBeforeDeleteGroup`

Fired from:

- [`core/components/com_members/admin/controllers/accessgroups.php:313`](../../../core/components/com_members/admin/controllers/accessgroups.php#L313) with `[$data]`

No plugin in the source tree listens for this event.

## `user.onUserBeforeSave`

Fired from:

- [`core/libraries/Hubzero/User/User.php:1121`](../../../core/libraries/Hubzero/User/User.php#L1121) with `[$oldUser->toArray(), $isNew, $data]`

Listeners:

- `plg_user_domainrestriction` — [`onUserBeforeSave($user, $isNew, $new)`](../../../core/plugins/user/domainrestriction/domainrestriction.php)

## `user.onUserBeforeSaveProfile`

Fired from:

- [`core/components/com_members/models/member.php:304`](../../../core/components/com_members/models/member.php#L304) with `[$user, $profile, $access]`

No plugin in the source tree listens for this event.

## `user.onUserDeidentify`

Fired from:

- [`core/components/com_members/admin/controllers/members.php:1248`](../../../core/components/com_members/admin/controllers/members.php#L1248) with `$id`

Listeners:

- `plg_user_hubzero` — [`onUserDeidentify($user_id)`](../../../core/plugins/user/hubzero/hubzero.php)
- `plg_user_ldap` — [`onUserDeidentify($user_id)`](../../../core/plugins/user/ldap/ldap.php)
- `plg_user_middleware` — [`onUserDeidentify($user_id)`](../../../core/plugins/user/middleware/middleware.php)

## `user.onUserLogin`

Fired from:

- [`core/libraries/Hubzero/Auth/Manager.php:101`](../../../core/libraries/Hubzero/Auth/Manager.php#L101) with `[(array) $response, $options]`
- [`core/libraries/Hubzero/User/User.php:666`](../../../core/libraries/Hubzero/User/User.php#L666) with `[$data]`

Listeners:

- `plg_user_autoapprove` — [`onUserLogin($user, $options = array()`](../../../core/plugins/user/autoapprove/autoapprove.php)
- `plg_user_d1` — [`onUserLogin($user, $options = array()`](../../../core/plugins/user/d1/d1.php)
- `plg_user_domainrestriction` — [`onUserLogin($user, $options)`](../../../core/plugins/user/domainrestriction/domainrestriction.php)
- `plg_user_geo` — [`onUserLogin($user, $options = array()`](../../../core/plugins/user/geo/geo.php)
- `plg_user_hubzero` — [`onUserLogin($user, $options = array()`](../../../core/plugins/user/hubzero/hubzero.php)
- `plg_user_us` — [`onUserLogin($user, $options = array()`](../../../core/plugins/user/us/us.php)
- `plg_user_xusers` — [`onUserLogin($user, $options = array()`](../../../core/plugins/user/xusers/xusers.php)

## `user.onUserLoginFailure`

Fired from:

- [`core/libraries/Hubzero/Auth/Manager.php:137`](../../../core/libraries/Hubzero/Auth/Manager.php#L137) with `[(array) $response]`

Listeners:

- `plg_user_xusers` — [`onUserLoginFailure($response)`](../../../core/plugins/user/xusers/xusers.php)

## `user.onUserLogout`

Fired from:

- [`core/components/com_login/site/controllers/auth.php:921`](../../../core/components/com_login/site/controllers/auth.php#L921) with `[$parameters, $options]`
- [`core/components/com_users/site/controllers/auth.php:992`](../../../core/components/com_users/site/controllers/auth.php#L992) with `[$parameters, $options]`
- [`core/libraries/Hubzero/Auth/Manager.php:177`](../../../core/libraries/Hubzero/Auth/Manager.php#L177) with `[$parameters, $options]`

Listeners:

- `plg_user_hubzero` — [`onUserLogout($user, $options = array()`](../../../core/plugins/user/hubzero/hubzero.php)
- `plg_user_xusers` — [`onUserLogout($user, $options = array()`](../../../core/plugins/user/xusers/xusers.php)

## `user.onUserLogoutFailure`

Fired from:

- [`core/components/com_login/site/controllers/auth.php:934`](../../../core/components/com_login/site/controllers/auth.php#L934) with `[$parameters]`
- [`core/components/com_users/site/controllers/auth.php:1005`](../../../core/components/com_users/site/controllers/auth.php#L1005) with `[$parameters]`
- [`core/libraries/Hubzero/Auth/Manager.php:192`](../../../core/libraries/Hubzero/Auth/Manager.php#L192) with `[$parameters]`

No plugin in the source tree listens for this event.
