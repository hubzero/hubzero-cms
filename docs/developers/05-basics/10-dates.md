<!--
status: rewritten
reviewed-against: 2.4-main @ a668500422
reviewed: 2026-09-09
source: https://help.hubzero.org/documentation/240/webdevs/basics/dates
-->
# Dates

A hub has members in every time zone, so it keeps one rule: **store and
compare in UTC, convert only for display.** Every `datetime` column in the
schema holds UTC. Every date that reaches a member's screen has been
converted on the way out.

[`Hubzero\Utility\Date`](../../../core/libraries/Hubzero/Utility/Date.php)
extends PHP's `DateTime` and enforces that rule. The `Date` facade is the
short way to build one.

## Creating a date

```php
use Date;

$now     = Date::of('now');
$created = Date::of($row->get('created'));
```

`Date::of($date = 'now', $tz = null)` returns a new object each time; there
is no shared instance. `Date::getRoot()` is `Date::of('now')`.

**When `$tz` is omitted the string is read as UTC.** That is the important
default. `Date::of('2015-06-01 09:00:00')` is nine in the morning UTC, not
nine in the morning wherever the server is. A value that came out of the
database is already UTC, so reading it back needs no time zone:

```php
$created = Date::of($row->get('created'));
```

A value that came from a **member typing into a form** is in their time zone,
and has to be told so:

```php
$fields['publish_up'] = Date::of($fields['publish_up'], Config::get('offset'))->toSql();
```

`Config::get('offset')` is the hub's configured zone. `$tz` accepts a
`DateTimeZone`, an identifier string such as `America/New_York`, or a
numeric offset resolved through the class's own table of common zones.

The constructor accepts anything `strtotime()` does, and treats a purely
numeric argument as a Unix timestamp:

| Input | Example |
|---|---|
| MySQL datetime | `2009-10-02 15:25:00` |
| Unix timestamp | `1254497100` |
| RFC 2822 | `Fri, 2 Oct 2009 15:25:00 +0000` |
| RFC 3339 / ISO 8601 | `2009-10-02T15:25:00+00:00` |
| Plain English | `2 October 2009`, `now`, `-1 year` |

Where the input carries its own offset, that offset is honoured and the
result still represents the same instant.

## Storing

```php
$row->set('modified', Date::of('now')->toSql());
```

`toSql($local = false, $dbo = null)` formats for the database using the
driver's own date format, in UTC unless `$local` is true. Leave `$local`
alone. Every comparison the CMS makes — `publish_up <= now`, a cron job's
`next_run` — assumes UTC on both sides, and one row written in local time
sorts and filters wrongly against all the others.

## Displaying

`toLocal($format = '')` converts to the viewing member's zone and formats:

```php
echo Date::of($row->get('created'))->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
```

It reads `User::getParam('timezone', Config::get('offset'))` — the member's
own preference where they have set one, the hub's offset otherwise — so the
same row renders differently for two members, which is the point.

The format is a **PHP `date()` format string**, not a `strftime()` one:
`'d M Y'`, not `'%d %b %Y'`. Put it in a language key rather than a view so
a hub can change it:

| Key | Value |
|---|---|
| `DATE_FORMAT_HZ1` | `d M Y` |
| `DATE_FORMAT_LC` | `l, d F Y` |
| `DATE_FORMAT_LC2` | `l, d F Y H:i` |
| `DATE_FORMAT_LC3` | `d F Y` |
| `DATE_FORMAT_LC4` | `Y-m-d` |
| `TIME_FORMAT_HZ1` | `g:i a` |

Called with no format, `toLocal()` uses `Date::$format`, which is
`Y-m-d H:i:s`.

| Method | Returns |
|---|---|
| `toLocal($format = '')` | Formatted in the viewer's time zone |
| `toTimeZone($tz, $format = null)` | Formatted in a zone you name |
| `format($format, $local = false, $translate = true)` | Formatted; **UTC unless `$local`** |
| `toSql($local = false, $dbo = null)` | Database datetime |
| `toISO8601($local = false)` | `2009-10-06T12:54:37+00:00` |
| `toRFC822($local = false)` | `Tue, 06 Oct 2009 12:54:37 +0000` |
| `toUnix()` | Seconds since the epoch |
| `relative($unit = null, $time = null)` | `3 days ago`, `2 weeks ago` |

`format()` defaults to UTC, so it is what a machine-readable attribute
wants and `toLocal()` is what the human-readable text beside it wants:

```php
<time datetime="<?php echo Date::of($row->get('created'))->format('Y-m-d\TH:i:s\Z'); ?>">
    <?php echo Date::of($row->get('created'))->toLocal(Lang::txt('DATE_FORMAT_HZ1')); ?>
</time>
```

`format()` also translates day and month names by default: `D`, `l`, `M` and
`F` are resolved through `Lang::txt()` against `MONDAY`, `MON`, `JANUARY`,
`JANUARY_SHORT` and their siblings. Pass `false` as the third argument where
you need the raw PHP output — `toSql()`, `toISO8601()` and `toRFC822()`
already do.

`relative()` renders an age rather than a date, using the
`JLIB_HTML_DATE_RELATIVE_*` keys under a month and `"%d %s ago"` above it.
`format('relative')` is a shorthand for it.

## Moving a date

`add($modifier)` and `subtract($modifier)` prefix the string with `+` or `-`
and pass it to `modify()`. They are marked deprecated; use `modify()`, or
`DateTime`'s own `add()`/`sub()` with a `DateInterval`, in new code.

```php
$cutoff = Date::of('now')->modify('-30 days')->toSql();
```

`setTimezone($tz)` accepts a string as well as a `DateTimeZone` and records
the zone on the object, so subsequent `format($f, true)` calls use it.

> **Warning:** `toTimeZone()` and `toLocal()` change the object's own time
> zone as a side effect, because `toTimeZone()` calls `setTimezone()`.
> Formatting the same object first for display and then for storage gives
> the wrong answer for the second. Build a fresh `Date::of(...)` for each
> output, as the views in the tree do.

> **Note:** Legacy rows can hold `0000-00-00 00:00:00`. Test the column
> before constructing a date from it; `Date::of('0000-00-00 00:00:00')` does
> not represent a date anybody wants to show.
