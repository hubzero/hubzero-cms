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

- [`core/libraries/Hubzero/User/Password.php:374`](../../../core/libraries/Hubzero/User/Password.php#L374) with `[$this]`

Listeners:

- `plg_user_ldap` — [`onAfterDeletePassword($user)`](../../../core/plugins/user/ldap/ldap.php)

## `user.onAfterDeleteProfile`

Fired from:

- [`core/libraries/Hubzero/User/Profile.php:1100`](../../../core/libraries/Hubzero/User/Profile.php#L1100) with `[$this]`

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

- [`core/components/com_groups/models/orm/group.php:550`](../../../core/components/com_groups/models/orm/group.php#L550) with `[$this]`
- [`core/components/com_groups/models/orm/group.php:623`](../../../core/components/com_groups/models/orm/group.php#L623) with `[$this]`
- [`core/libraries/Hubzero/User/Group.php:340`](../../../core/libraries/Hubzero/User/Group.php#L340) with `[$this]`
- [`core/libraries/Hubzero/User/Group.php:615`](../../../core/libraries/Hubzero/User/Group.php#L615) with `[$this]`
- [`core/libraries/Hubzero/User/Group.php:668`](../../../core/libraries/Hubzero/User/Group.php#L668) with `[$this]`
- [`core/libraries/Hubzero/User/Group/Membership.php:1083`](../../../core/libraries/Hubzero/User/Group/Membership.php#L1083) with `[$group]`

Listeners:

- `plg_user_ldap` — [`onAfterStoreGroup($group)`](../../../core/plugins/user/ldap/ldap.php)

## `user.onAfterStorePassword`

Fired from:

- [`core/libraries/Hubzero/User/Password.php:332`](../../../core/libraries/Hubzero/User/Password.php#L332) with `[$this]`

Listeners:

- `plg_user_ldap` — [`onAfterStorePassword($user)`](../../../core/plugins/user/ldap/ldap.php)

## `user.onAfterStoreProfile`

Fired from:

- [`core/components/com_members/admin/controllers/hosts.php:65`](../../../core/components/com_members/admin/controllers/hosts.php#L65) with `[$profile]`
- [`core/components/com_members/admin/controllers/hosts.php:111`](../../../core/components/com_members/admin/controllers/hosts.php#L111) with `[$profile]`
- [`core/libraries/Hubzero/User/Profile.php:1049`](../../../core/libraries/Hubzero/User/Profile.php#L1049) with `[$this]`

Listeners:

- `plg_user_constantcontact` — [`onAfterStoreProfile($user)`](../../../core/plugins/user/constantcontact/constantcontact.php)
- `plg_user_ldap` — [`onAfterStoreProfile($user)`](../../../core/plugins/user/ldap/ldap.php)

## `user.onAfterStoreUser`

Fired from:

- [`core/plugins/members/account/account.php:603`](../../../core/plugins/members/account/account.php#L603) with `[$this->user->toArray(), false, null, $this->getError()]`

Listeners:

- `plg_user_ldap` — [`onAfterStoreUser($user, $isnew, $success, $msg)`](../../../core/plugins/user/ldap/ldap.php)
- `plg_user_xusers` — [`onAfterStoreUser($user, $isnew, $success, $msg)`](../../../core/plugins/user/xusers/xusers.php)

## `user.onBeforeStoreUser`

Fired from:

- [`core/plugins/members/account/account.php:550`](../../../core/plugins/members/account/account.php#L550) with `[$this->user->toArray(), false]`

No plugin in the source tree listens for this event.

## `user.onLoginUser`

No call site in the CMS fires this event; the listener may answer an event fired by another package or by an older plugin.

Listeners:

- `plg_user_autoapprove` — [`onLoginUser($user, $options = [])`](../../../core/plugins/user/autoapprove/autoapprove.php)
- `plg_user_d1` — [`onLoginUser($user, $options = [])`](../../../core/plugins/user/d1/d1.php)
- `plg_user_us` — [`onLoginUser($user, $options = [])`](../../../core/plugins/user/us/us.php)
- `plg_user_xusers` — [`onLoginUser($user, $options = [])`](../../../core/plugins/user/xusers/xusers.php)

## `user.onLogoutUser`

No call site in the CMS fires this event; the listener may answer an event fired by another package or by an older plugin.

Listeners:

- `plg_user_xusers` — [`onLogoutUser($user, $options = [])`](../../../core/plugins/user/xusers/xusers.php)

## `user.onUserAfterDelete`

Fired from:

- [`core/components/com_members/admin/controllers/members.php:820`](../../../core/components/com_members/admin/controllers/members.php#L820) with `[$data, true, $this->getError()]`
- [`core/components/com_members/models/member.php:477`](../../../core/components/com_members/models/member.php#L477) with `[$data, true, $this->getError()]`
- [`core/libraries/Hubzero/User/User.php:1159`](../../../core/libraries/Hubzero/User/User.php#L1159) with `[$data, true, $this->getError()]`

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

- [`core/components/com_members/admin/controllers/accessgroups.php:305`](../../../core/components/com_members/admin/controllers/accessgroups.php#L305) with `$afterData`

No plugin in the source tree listens for this event.

## `user.onUserAfterSave`

Fired from:

- [`core/components/com_cart/lib/handlers/type/accessgroupmembershiptypehandler.php:56`](../../../core/components/com_cart/lib/handlers/type/accessgroupmembershiptypehandler.php#L56) with `[$table->toArray(), false, true, null]`
- [`core/libraries/Hubzero/User/User.php:1113`](../../../core/libraries/Hubzero/User/User.php#L1113) with `[$data, $isNew, $result, $this->getError()]`

Listeners:

- `plg_user_autoapprove` — [`onUserAfterSave($user, $isnew, $success, $msg)`](../../../core/plugins/user/autoapprove/autoapprove.php)
- `plg_user_domainrestriction` — [`onUserAfterSave($user, $isnew, $success, $msg)`](../../../core/plugins/user/domainrestriction/domainrestriction.php)
- `plg_user_hubzero` — [`onUserAfterSave($user, $isnew, $success, $msg)`](../../../core/plugins/user/hubzero/hubzero.php)
- `plg_user_ldap` — [`onUserAfterSave($user, $isnew, $success, $msg)`](../../../core/plugins/user/ldap/ldap.php)
- `plg_user_middleware` — [`onUserAfterSave($user, $isnew, $success, $msg)`](../../../core/plugins/user/middleware/middleware.php)
- `plg_user_xusers` — [`onUserAfterSave($user, $isnew, $success, $msg)`](../../../core/plugins/user/xusers/xusers.php)

## `user.onUserAfterSaveProfile`

Fired from:

- [`core/components/com_members/models/member.php:430`](../../../core/components/com_members/models/member.php#L430) with `[$user, $profile, $access]`

No plugin in the source tree listens for this event.

## `user.onUserAuthorisation`

Fired from:

- [`core/libraries/Hubzero/Auth/Guard.php:264`](../../../core/libraries/Hubzero/Auth/Guard.php#L264) with `[$response, $options]`

No plugin in the source tree listens for this event.

## `user.onUserAuthorisationFailure`

Fired from:

- [`core/libraries/Hubzero/Auth/Manager.php:75`](../../../core/libraries/Hubzero/Auth/Manager.php#L75) with `[(array) $authorisation]`

No plugin in the source tree listens for this event.

## `user.onUserBeforeDelete`

Fired from:

- [`core/components/com_members/models/member.php:444`](../../../core/components/com_members/models/member.php#L444) with `[$data]`
- [`core/libraries/Hubzero/User/User.php:1135`](../../../core/libraries/Hubzero/User/User.php#L1135) with `[$data]`

No plugin in the source tree listens for this event.

## `user.onUserBeforeDeleteGroup`

Fired from:

- [`core/components/com_members/admin/controllers/accessgroups.php:295`](../../../core/components/com_members/admin/controllers/accessgroups.php#L295) with `[$data]`

No plugin in the source tree listens for this event.

## `user.onUserBeforeSave`

Fired from:

- [`core/libraries/Hubzero/User/User.php:1077`](../../../core/libraries/Hubzero/User/User.php#L1077) with `[$oldUser->toArray(), $isNew, $data]`

Listeners:

- `plg_user_domainrestriction` — [`onUserBeforeSave($user, $isNew, $new)`](../../../core/plugins/user/domainrestriction/domainrestriction.php)

## `user.onUserBeforeSaveProfile`

Fired from:

- [`core/components/com_members/models/member.php:284`](../../../core/components/com_members/models/member.php#L284) with `[$user, $profile, $access]`

No plugin in the source tree listens for this event.

## `user.onUserDeidentify`

Fired from:

- [`core/components/com_members/admin/controllers/members.php:1163`](../../../core/components/com_members/admin/controllers/members.php#L1163) with `$id`

Listeners:

- `plg_user_hubzero` — [`onUserDeidentify($user_id)`](../../../core/plugins/user/hubzero/hubzero.php)
- `plg_user_ldap` — [`onUserDeidentify($user_id)`](../../../core/plugins/user/ldap/ldap.php)
- `plg_user_middleware` — [`onUserDeidentify($user_id)`](../../../core/plugins/user/middleware/middleware.php)

## `user.onUserLogin`

Fired from:

- [`core/libraries/Hubzero/Auth/Manager.php:98`](../../../core/libraries/Hubzero/Auth/Manager.php#L98) with `[(array) $response, $options]`
- [`core/libraries/Hubzero/User/User.php:649`](../../../core/libraries/Hubzero/User/User.php#L649) with `[$data]`

Listeners:

- `plg_user_autoapprove` — [`onUserLogin($user, $options = [])`](../../../core/plugins/user/autoapprove/autoapprove.php)
- `plg_user_d1` — [`onUserLogin($user, $options = [])`](../../../core/plugins/user/d1/d1.php)
- `plg_user_domainrestriction` — [`onUserLogin($user, $options)`](../../../core/plugins/user/domainrestriction/domainrestriction.php)
- `plg_user_geo` — [`onUserLogin($user, $options = [])`](../../../core/plugins/user/geo/geo.php)
- `plg_user_hubzero` — [`onUserLogin($user, $options = [])`](../../../core/plugins/user/hubzero/hubzero.php)
- `plg_user_us` — [`onUserLogin($user, $options = [])`](../../../core/plugins/user/us/us.php)
- `plg_user_xusers` — [`onUserLogin($user, $options = [])`](../../../core/plugins/user/xusers/xusers.php)

## `user.onUserLoginFailure`

Fired from:

- [`core/libraries/Hubzero/Auth/Manager.php:140`](../../../core/libraries/Hubzero/Auth/Manager.php#L140) with `[(array) $response]`

Listeners:

- `plg_user_xusers` — [`onUserLoginFailure($response)`](../../../core/plugins/user/xusers/xusers.php)

## `user.onUserLogout`

Fired from:

- [`core/components/com_login/site/controllers/auth.php:831`](../../../core/components/com_login/site/controllers/auth.php#L831) with `[$parameters, $options]`
- [`core/components/com_users/site/controllers/auth.php:892`](../../../core/components/com_users/site/controllers/auth.php#L892) with `[$parameters, $options]`
- [`core/libraries/Hubzero/Auth/Manager.php:177`](../../../core/libraries/Hubzero/Auth/Manager.php#L177) with `[$parameters, $options]`

Listeners:

- `plg_user_hubzero` — [`onUserLogout($user, $options = [])`](../../../core/plugins/user/hubzero/hubzero.php)
- `plg_user_xusers` — [`onUserLogout($user, $options = [])`](../../../core/plugins/user/xusers/xusers.php)

## `user.onUserLogoutFailure`

Fired from:

- [`core/components/com_login/site/controllers/auth.php:841`](../../../core/components/com_login/site/controllers/auth.php#L841) with `[$parameters]`
- [`core/components/com_users/site/controllers/auth.php:902`](../../../core/components/com_users/site/controllers/auth.php#L902) with `[$parameters]`
- [`core/libraries/Hubzero/Auth/Manager.php:191`](../../../core/libraries/Hubzero/Auth/Manager.php#L191) with `[$parameters]`

No plugin in the source tree listens for this event.
