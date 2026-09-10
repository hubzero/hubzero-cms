<!--
status: rewritten
reviewed-against: 2.4-main @ 348f0057c2
reviewed: 2026-09-10
screenshots: none
source: https://help.hubzero.org/documentation/240/webdevs/index/releasenotes
source-id: 3423
modified: 2014-03-18
-->
# Release notes

Where to find what changed in a release, and the one historic change that
still explains most of what you read in the framework.

Read it for one of two reasons: you want to know what a version number means
and where the record is, or you are staring at a class name in an old
extension that no longer exists anywhere. The tables below are the second
answer. They describe what code *was*; nothing new should be written to
either column's left-hand side.

## Where release notes live

This page is not a changelog, and no changelog is published yet. The
authoritative record of what changed is the repository itself: the tags
and commit history on
[hubzero/hubzero-cms](https://github.com/hubzero/hubzero-cms). Each release
line has its own `X.Y-main` branch; `2.4-main` is the current one, and a
release is cut from it. Upgrade steps for a running hub are being
rewritten alongside the new web installer; see
[Installing a hub](../../managers/00-installing.md).

The running version is the `HVERSION` constant, defined in
[`core/bootstrap/app.php`](../../../core/bootstrap/app.php) and shown in
the administrator's **System Information** screen and the `mod_version`
module.

<!--include: core/bootstrap/app.php:39-39-->

> **Warning:** `Hubzero\Version\Version::VERSION` is a second, stale version
> string. It reads `2.1.0` and is only used by `muse repository --version`,
> which therefore reports the wrong version. Use `HVERSION`.

## The 2.0 namespacing

The change that most affects code you read today happened at 2.0, when the
framework library was namespaced. Extensions written before that use
underscored class names that no longer exist. The rule is mechanical:
`Hubzero_User_Profile` became `Hubzero\User\Profile`.

Anything still calling the old names needs the table below. Where a class
also moved, the new name is not a straight translation:

| Removed | Now |
|---|---|
| `Hubzero_Group` | [`Hubzero\User\Group`](../../../core/libraries/Hubzero/User/Group.php) |
| `Hubzero_Group_Helper` | `Hubzero\User\Group\Helper` |
| `Hubzero_Group_InviteEmail` | `Hubzero\User\Group\InviteEmail` |
| `Hubzero_Geo` | [`Hubzero\Geocode\Geocode`](../../../core/libraries/Hubzero/Geocode/Geocode.php) |
| `Hubzero\ItemList` | [`Hubzero\Base\ItemList`](../../../core/libraries/Hubzero/Base/ItemList.php) |
| `Hubzero\Model` | [`Hubzero\Base\Model`](../../../core/libraries/Hubzero/Base/Model.php) |
| `Hubzero\Object` | [`Hubzero\Base\Obj`](../../../core/libraries/Hubzero/Base/Obj.php) |
| `Hubzero_Document` | [`Hubzero\Document\Assets`](../../../core/libraries/Hubzero/Document/Assets.php) |
| `Hubzero_Component` | `Hubzero\Component\SiteController` or `Hubzero\Component\AdminController` |
| `Hubzero_Api_Controller` | [`Hubzero\Component\ApiController`](../../../core/libraries/Hubzero/Component/ApiController.php) |
| `Hubzero_Browser` | [`Hubzero\Browser\Detector`](../../../core/libraries/Hubzero/Browser/Detector.php) |
| `Hubzero_Ldap` | [`Hubzero\Utility\Ldap`](../../../core/libraries/Hubzero/Utility/Ldap.php) |

> **Note:** The 2.0 notes said the object base class became
> `Hubzero\Base\Object`. It did not stay there. `Object` became a reserved
> word in PHP 7, so the class is `Hubzero\Base\Obj`. An empty
> `core/libraries/Hubzero/Base/Object.php` is still in the tree and declares
> nothing.

The view helpers were split across three utility classes:

| Removed | Now |
|---|---|
| `Hubzero_View_Helper_Html::niceidformat()` | `Hubzero\Utility\Str::pad()` |
| `Hubzero_View_Helper_Html::formatSize()` | `Hubzero\Utility\Number::formatBytes()` |
| `Hubzero_View_Helper_Html::shortenText()` | `Hubzero\Utility\Str::truncate()` |
| `Hubzero_View_Helper_Html::purifyText()` | `Hubzero\Utility\Sanitize::stripAll()` |
| `Hubzero_View_Helper_Html::str_highlight()` | `Hubzero\Utility\Str::highlight()` |
| `Hubzero_View_Helper_Html::timeAgo()` | `Date::of($date)->relative()` |

> **Note:** The 2.0 notes named `Hubzero\Utility\String`. That class was
> renamed to [`Hubzero\Utility\Str`](../../../core/libraries/Hubzero/Utility/Str.php)
> when `String` became a reserved word in PHP 7. `timeAgo()` was documented
> as `JHTML::_('date.relative', $date)`; there is no `date` HTML builder, and
> the method is [`Hubzero\Utility\Date::relative()`](../../../core/libraries/Hubzero/Utility/Date.php).

The browser detector's accessors were shortened at the same time, and the
short names are what the class has today:

| Removed | Now |
|---|---|
| `getBrowser()` | `name()` |
| `getBrowserVersion()` | `version()` |
| `getBrowserMajorVersion()` | `major()` |
| `getBrowserMinorVersion()` | `minor()` |
| `getOs()` | `platform()` |
| `getOsVersion()` | `platformVersion()` |
| `getUserAgent()` | `agent()` |

## What went away

- **`ximport()`.** Namespaced classes are autoloaded, so the old manual
  import function was deprecated at 2.0 and no longer exists anywhere in the
  tree. Delete the calls; nothing replaces them.

## What arrived

These are all still current, and each has its own chapter:

- **Sub-views.** `$this->view('layout')` loads a view from inside a view.
  See [Component views](../09-components/07-views.md).
- **Asset helpers.** `$this->css()` and `$this->js()` push a stylesheet or
  script to the document from a view, and chain. The signature has changed
  since 2.0 — see [Component assets](../09-components/08-assets.md).
- **Geocoding.** [`Hubzero\Geocode\Geocode`](../../../core/libraries/Hubzero/Geocode/Geocode.php)
  fires a plugin event and any plugin in the `geocode` group may answer.
  Sixteen ship in `core/plugins/geocode`, several of which need an account
  with the service before they return anything.

## Older legacy names

An extension written against the pre-Hubzero class names rather than against
Hubzero 1.x needs a different table. That is the
[Upgrade guide](04-upgrade.md).
