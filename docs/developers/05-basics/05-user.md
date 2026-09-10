<!--
status: rewritten
reviewed-against: 2.4-main @ 348f0057c2
reviewed: 2026-09-10
source: https://help.hubzero.org/documentation/240/webdevs/basics/user
-->
# Users & profiles

The `User` facade resolves
[`Hubzero\User\Manager`](../../../core/libraries/Hubzero/User/Manager.php),
which hands out
[`Hubzero\User\User`](../../../core/libraries/Hubzero/User/User.php)
objects. `Hubzero\User\User` is a
[`Relational`](../06-database.md#orm) model over `#__users`, so everything the
ORM offers — `get()`, `set()`, `save()`, relationships, query scopes — is
available on it.

Three separate things are called "the user" on a hub, and reaching for the
wrong one is the mistake this page exists to prevent:

| You want | Ask for | Lives in |
|---|---|---|
| The account — id, name, username, email, blocked | `Hubzero\User\User`, via the `User` facade | `#__users` |
| The profile — bio, organisation, phone, ORCID | `Components\Members\Models\Member` | `#__user_profiles` |
| Community group membership | `->groups()` on either | `#__xgroups` and its maps |

The first two look identical from the outside — `Member` extends `User`, and
both answer `get()`. They are not identical, and asking the wrong one for a
profile field gets you a default rather than an error. That is the section
on [extended profile fields](#extended-profile-fields) below.

## The current user

Any call on the facade that is not `getInstance()` is forwarded to the
current user, because the manager's `__call()` passes it on:

```php
use User;

$id    = User::get('id');
$name  = User::get('name');
$email = User::get('email');
```

The current user comes from the session, and is a `User` with no id when
nobody is logged in. `User::get('id')` returns `0` rather than `null` in that
case, which is why so much code can compare against it without a guard.

That `0` is also the trap. Writing `User::get('id')` into a `created_by`
column without checking `isGuest()` first records a row owned by nobody, and
every later `getInstance(0)` on it returns an empty `User` whose `name` is
blank. The row is not wrong enough to notice: the booking exists, the list
renders, and the owner column is simply empty. Check first.

## Other users

```php
$author = User::getInstance($booking->get('created_by'));
```

`getInstance($id = null)` takes a numeric id, a username, or an email
address. With no argument it returns the current user. Resolved users are
cached for the request, so asking twice costs one query. An id that matches
nothing gives you an empty `User` — check `get('id')` before using it.

Two things follow from how it resolves:

- Anything `is_numeric()` is treated as an **id**, never as a username. A
  hub that allows all-digit usernames cannot look one up this way.
- When the id you pass is the current user's, you get back the object **in
  the session**, not a copy. `set()` on it changes the logged-in user for
  the rest of the request. Load a fresh model with the ORM if you mean to
  modify somebody.

The ORM's own finders are there for anything more selective:

```php
$user = Hubzero\User\User::oneByUsername('janedoe');
$user = Hubzero\User\User::oneByEmail('jane@example.org');
$user = Hubzero\User\User::oneOrFail($id);      // throws if absent
```

## The columns

`#__users` holds the account, and nothing more:

| Column | Notes |
|---|---|
| `id` | The numeric user id. Reference this from your own tables |
| `name` | Display name — "Jane Doe" |
| `username` | Login name |
| `email` | Email address |
| `password` | Hashed. Never read it; see `Hubzero\User\Password` |
| `block` | `1` when the account is blocked |
| `activation` | Non-zero once the email address is confirmed |
| `registerDate` | Set automatically on create |
| `registerIP` | Set automatically on create |
| `lastvisitDate` | Updated by `setLastVisit()` |
| `access` | The account's view access level |
| `params` | Per-member preferences, as a `Registry` |

Two keys are not columns and are handled in `get()`: `guest` returns
`isGuest()`, and `uidNumber` is an alias for `id`.

Preferences in `params` are read with `getParam()`, not `get()`:

```php
$tz = User::getParam('timezone', Config::get('offset'));
```

`setParam($key, $value)` and `defParam($key, $value)` write and default one.

## Logged in or not

```php
if (User::isGuest())
{
    // logged OUT
    App::abort(403, Lang::txt('Login required'));
}
```

`User::get('guest')` is the same test. Note that `isGuest()` does more than
read a flag on a cloud-hosted hub: it will accept a signed JWT cookie and
populate the user from it. Always call it rather than inspecting the
property yourself.

## Extended profile fields

Bio, gender, organisation, disability and the rest are **not** on
`Hubzero\User\User`. They live in `#__user_profiles` as key/value rows and
are reached through
[`Components\Members\Models\Member`](../../../core/components/com_members/models/member.php),
which extends `Hubzero\User\User` and loads them on first miss:

```php
use Components\Members\Models\Member;

$member = Member::oneOrFail($id);

$bio    = $member->get('bio');
$gender = $member->get('gender');
```

A key that occurs several times for one member — a multi-value field such as
`disability` — comes back as an array; a key that occurs once comes back as
a string.

> **Warning:** `User::getInstance($id)->get('bio')` returns the default —
> `null`, or whatever you passed as a second argument. It does not raise, and
> it does not tell you the column does not exist there. A profile field that
> renders as blank for every member on the page, including members you know
> have filled it in, is this. The profile rows are only collected by
> `Member::get()`, so ask for a `Member` whenever you want anything beyond
> the account columns.

`$member->picture($anonymous = 0, $thumbnail = true, $serveFile = true)`
returns a URL for the member's picture, falling back to a generated
placeholder, and `$member->link($type = '')` builds the URL of their
profile.

## Access groups

Access groups are the ACL groups an administrator assigns, not community
groups:

```php
foreach (User::getInstance($id)->accessgroups() as $map)
{
    echo $map->get('group_id');
}
```

`accessgroups()` is a one-to-many relationship to `Hubzero\Access\Map`, so
it returns a query you can constrain, and iterating it runs it.
`getAuthorisedGroups()` returns the group ids as a flat array, including
inherited parents, and `getAuthorisedViewLevels()` the view levels those
groups grant.

Permissions are checked with `authorise($action, $assetname = null)`
(`authorize()` is a spelling alias):

```php
if (!User::authorise('core.edit', 'com_blog'))
{
    App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
}
```

## Community group membership

Community groups — the `#__xgroups` ones — hang off the user model:

```php
$managed = User::getInstance($id)->groups('managers');
```

`groups($role = 'all')` accepts `all`, `members`, `managers`, `applicants`
or `invitees`, and returns an array of row objects. It is computed once per
request.

The underlying query is
`Hubzero\User\Helper::getGroups($uid, $type = 'all', $cat = null)`, which
returns an **array** — empty when there are none, never `false` — of objects
carrying:

| Field | Meaning |
|---|---|
| `gidNumber` | The group's numeric id |
| `cn` | The group alias, as it appears in a URL |
| `description` | The group's title |
| `published` | `0` or `1` |
| `approved` | `0` or `1` |
| `join_policy` | How the group admits members |
| `registered` | `1` if the member applied; `0` if they were invited |
| `regconfirmed` | `1` once the application was accepted |
| `manager` | `1` if the member manages the group |

`Helper::getCommonGroups($uid, $pid)` returns the groups two members share,
which is what profile pages use to decide how much to show.
