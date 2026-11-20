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

- [`core/libraries/Hubzero/User/Password.php:360`](../../../core/libraries/Hubzero/User/Password.php#L360) with `[$this]`

Listeners:

- `plg_user_ldap` — [`onAfterDeletePassword($user)`](../../../core/plugins/user/ldap/ldap.php)

## `user.onAfterDeleteProfile`

Fired from:

- [`core/libraries/Hubzero/User/Profile.php:1071`](../../../core/libraries/Hubzero/User/Profile.php#L1071) with `[$this]`

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

- [`core/components/com_groups/models/orm/group.php:551`](../../../core/components/com_groups/models/orm/group.php#L551) with `[$this]`
- [`core/components/com_groups/models/orm/group.php:624`](../../../core/components/com_groups/models/orm/group.php#L624) with `[$this]`
- [`core/libraries/Hubzero/User/Group.php:344`](../../../core/libraries/Hubzero/User/Group.php#L344) with `[$this]`
- [`core/libraries/Hubzero/User/Group.php:604`](../../../core/libraries/Hubzero/User/Group.php#L604) with `[$this]`
- [`core/libraries/Hubzero/User/Group.php:649`](../../../core/libraries/Hubzero/User/Group.php#L649) with `[$this]`
- [`core/libraries/Hubzero/User/Group/Membership.php:1083`](../../../core/libraries/Hubzero/User/Group/Membership.php#L1083) with `[$group]`

Listeners:

- `plg_user_ldap` — [`onAfterStoreGroup($group)`](../../../core/plugins/user/ldap/ldap.php)

## `user.onAfterStorePassword`

Fired from:

- [`core/libraries/Hubzero/User/Password.php:323`](../../../core/libraries/Hubzero/User/Password.php#L323) with `[$this]`

Listeners:

- `plg_user_ldap` — [`onAfterStorePassword($user)`](../../../core/plugins/user/ldap/ldap.php)

## `user.onAfterStoreProfile`

Fired from:

- [`core/components/com_members/admin/controllers/hosts.php:71`](../../../core/components/com_members/admin/controllers/hosts.php#L71) with `[$profile]`
- [`core/components/com_members/admin/controllers/hosts.php:123`](../../../core/components/com_members/admin/controllers/hosts.php#L123) with `[$profile]`
- [`core/libraries/Hubzero/User/Profile.php:1026`](../../../core/libraries/Hubzero/User/Profile.php#L1026) with `[$this]`

Listeners:

- `plg_user_constantcontact` — [`onAfterStoreProfile($user)`](../../../core/plugins/user/constantcontact/constantcontact.php)
- `plg_user_ldap` — [`onAfterStoreProfile($user)`](../../../core/plugins/user/ldap/ldap.php)

## `user.onAfterStoreUser`

Fired from:

- [`core/plugins/members/account/account.php:609`](../../../core/plugins/members/account/account.php#L609) with `[$this->user->toArray(), false, null, $this->getError()]`

Listeners:

- `plg_user_ldap` — [`onAfterStoreUser($user, $isnew, $success, $msg)`](../../../core/plugins/user/ldap/ldap.php)
- `plg_user_xusers` — [`onAfterStoreUser($user, $isnew, $success, $msg)`](../../../core/plugins/user/xusers/xusers.php)

## `user.onBeforeStoreUser`

Fired from:

- [`core/plugins/members/account/account.php:556`](../../../core/plugins/members/account/account.php#L556) with `[$this->user->toArray(), false]`

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

- [`core/components/com_members/admin/controllers/members.php:846`](../../../core/components/com_members/admin/controllers/members.php#L846) with `[$data, true, $this->getError()]`
- [`core/components/com_members/models/member.php:477`](../../../core/components/com_members/models/member.php#L477) with `[$data, true, $this->getError()]`
- [`core/libraries/Hubzero/User/User.php:1167`](../../../core/libraries/Hubzero/User/User.php#L1167) with `[$data, true, $this->getError()]`

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

- [`core/components/com_members/admin/controllers/accessgroups.php:311`](../../../core/components/com_members/admin/controllers/accessgroups.php#L311) with `$afterData`

No plugin in the source tree listens for this event.

## `user.onUserAfterSave`

Fired from:

- [`core/components/com_cart/lib/handlers/type/accessgroupmembershiptypehandler.php:56`](../../../core/components/com_cart/lib/handlers/type/accessgroupmembershiptypehandler.php#L56) with `[$table->toArray(), false, true, null]`
- [`core/libraries/Hubzero/User/User.php:1121`](../../../core/libraries/Hubzero/User/User.php#L1121) with `[$data, $isNew, $result, $this->getError()]`

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

- [`core/libraries/Hubzero/Auth/Guard.php:265`](../../../core/libraries/Hubzero/Auth/Guard.php#L265) with `[$response, $options]`

No plugin in the source tree listens for this event.

## `user.onUserAuthorisationFailure`

Fired from:

- [`core/libraries/Hubzero/Auth/Manager.php:75`](../../../core/libraries/Hubzero/Auth/Manager.php#L75) with `[(array) $authorisation]`

No plugin in the source tree listens for this event.

## `user.onUserBeforeDelete`

Fired from:

- [`core/components/com_members/models/member.php:444`](../../../core/components/com_members/models/member.php#L444) with `[$data]`
- [`core/libraries/Hubzero/User/User.php:1143`](../../../core/libraries/Hubzero/User/User.php#L1143) with `[$data]`

No plugin in the source tree listens for this event.

## `user.onUserBeforeDeleteGroup`

Fired from:

- [`core/components/com_members/admin/controllers/accessgroups.php:301`](../../../core/components/com_members/admin/controllers/accessgroups.php#L301) with `[$data]`

No plugin in the source tree listens for this event.

## `user.onUserBeforeSave`

Fired from:

- [`core/libraries/Hubzero/User/User.php:1085`](../../../core/libraries/Hubzero/User/User.php#L1085) with `[$oldUser->toArray(), $isNew, $data]`

Listeners:

- `plg_user_domainrestriction` — [`onUserBeforeSave($user, $isNew, $new)`](../../../core/plugins/user/domainrestriction/domainrestriction.php)

## `user.onUserBeforeSaveProfile`

Fired from:

- [`core/components/com_members/models/member.php:284`](../../../core/components/com_members/models/member.php#L284) with `[$user, $profile, $access]`

No plugin in the source tree listens for this event.

## `user.onUserDeidentify`

Fired from:

- [`core/components/com_members/admin/controllers/members.php:1207`](../../../core/components/com_members/admin/controllers/members.php#L1207) with `[$id]`

Listeners:

- `plg_user_hubzero` — [`onUserDeidentify($user_id)`](../../../core/plugins/user/hubzero/hubzero.php)
- `plg_user_ldap` — [`onUserDeidentify($user_id)`](../../../core/plugins/user/ldap/ldap.php)
- `plg_user_middleware` — [`onUserDeidentify($user_id)`](../../../core/plugins/user/middleware/middleware.php)

## `user.onUserLogin`

Fired from:

- [`core/libraries/Hubzero/Auth/Manager.php:95`](../../../core/libraries/Hubzero/Auth/Manager.php#L95) with `[(array) $response, $options]`
- [`core/libraries/Hubzero/User/User.php:659`](../../../core/libraries/Hubzero/User/User.php#L659) with `[$data]`

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

- [`core/libraries/Hubzero/Auth/Manager.php:137`](../../../core/libraries/Hubzero/Auth/Manager.php#L137) with `[(array) $response]`

Listeners:

- `plg_user_xusers` — [`onUserLoginFailure($response)`](../../../core/plugins/user/xusers/xusers.php)

## `user.onUserLogout`

Fired from:

- [`core/components/com_login/site/controllers/auth.php:842`](../../../core/components/com_login/site/controllers/auth.php#L842) with `[$parameters, $options]`
- [`core/components/com_users/site/controllers/auth.php:907`](../../../core/components/com_users/site/controllers/auth.php#L907) with `[$parameters, $options]`
- [`core/libraries/Hubzero/Auth/Manager.php:175`](../../../core/libraries/Hubzero/Auth/Manager.php#L175) with `[$parameters, $options]`

Listeners:

- `plg_user_hubzero` — [`onUserLogout($user, $options = [])`](../../../core/plugins/user/hubzero/hubzero.php)
- `plg_user_xusers` — [`onUserLogout($user, $options = [])`](../../../core/plugins/user/xusers/xusers.php)

## `user.onUserLogoutFailure`

Fired from:

- [`core/components/com_login/site/controllers/auth.php:852`](../../../core/components/com_login/site/controllers/auth.php#L852) with `[$parameters]`
- [`core/components/com_users/site/controllers/auth.php:917`](../../../core/components/com_users/site/controllers/auth.php#L917) with `[$parameters]`
- [`core/libraries/Hubzero/Auth/Manager.php:189`](../../../core/libraries/Hubzero/Auth/Manager.php#L189) with `[$parameters]`

No plugin in the source tree listens for this event.
