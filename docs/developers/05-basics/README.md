<!--
status: rewritten
reviewed-against: 2.4-main @ 348f0057c2
reviewed: 2026-09-10
source: https://help.hubzero.org/documentation/240/webdevs/basics
-->
# The basics

The handful of things every extension touches: reading the request,
writing the response, finding configuration, translating strings, working
with the current user, tagging things, handling dates, running scheduled
work, and seeing what went wrong.

The examples through this section build one small component: `com_bookings`,
which lets a lab take reservations on its instruments. It is the ordinary
shape of the work — a list, a form, a save, a nightly tidy-up — and it
touches every page here.

Each of these is reached through a facade — `Request`, `Response`,
`Config`, `Lang`, `User`, `Date` — which is a short name in the root
namespace standing in for an object in the application container.

> **Note:** The facades are aliased in the **root** namespace, and almost
> every file you write is namespaced. Inside a namespace an unqualified
> `Request` means `Your\Namespace\Request`, which does not exist, and PHP
> raises a fatal error the moment the line runs. Import each facade you use:
>
> ```php
> namespace Components\Blog\Site\Controllers;
>
> use Request;
> use Config;
> use Lang;
> ```
>
> `\Request::getInt('id')` works too. Nothing catches a missing import
> before the line runs, so a rarely taken error path can carry the fault for
> a long time; `php tools/lint/missing-facade-imports.php` finds them, and
> it runs in continuous integration. See
> [facades](../03-foundation/06-facades.md).

## In this section

- [Requests](01-requests.md) — reading input safely, and what the request
  knows about itself.
- [Responses](02-responses.md) — headers, content, and sending.
- [Redirect](11-redirect.md) — `App::redirect()`, and what it does to the
  rest of your method.
- [Config](03-config.md) — global, component, plugin and module
  configuration.
- [Languages](04-languages.md) — INI files, key naming, `Lang::txt()`, and
  overrides.
- [Users & profiles](05-user.md) — the current user, other users, extended
  profile fields, and group membership.
- [Tags](06-tags.md) — attaching tags to your component's objects.
- [Debugging](08-debugging.md) — debug mode, dumping variables, and the
  logs.
- [Scheduled tasks](09-cron.md) — registering work for the cron runner.
- [Dates](10-dates.md) — the one subject where getting it wrong is silent:
  the platform stores and compares in UTC and converts only for display.

## A worked shape

Most controller tasks look like this, and touch four of the above in a
dozen lines:

```php
namespace Components\Bookings\Site\Controllers;

use Hubzero\Component\SiteController;
use Components\Bookings\Models\Booking;
use Request;
use Notify;
use Config;
use Route;
use Lang;
use Date;
use App;

class Bookings extends SiteController
{
    public function saveTask()
    {
        // Every write is guarded by the form token
        Request::checkToken();

        $fields = Request::getArray('fields', array(), 'post');

        $row = Booking::oneOrNew($fields['id'])->set($fields);

        // The member typed a local time; the column holds UTC
        $row->set('starts', Date::of($fields['starts'], Config::get('offset'))->toSql());

        if (!$row->save())
        {
            Notify::error($row->getError());

            return $this->editTask($row);
        }

        App::redirect(
            Route::url('index.php?option=' . $this->_option),
            Lang::txt('COM_BOOKINGS_BOOKING_SAVED')
        );
    }
}
```

Input arrives typed rather than raw, the write is guarded by the token, the
date is read in the hub's time zone and written in UTC, the message is a
language key that must exist in the component's own INI file, and the
redirect ends the request.

Four of those five have a silent failure mode. Miss the token check and the
task still works. Miss the time zone and the booking still saves. Miss the
language key and the page still renders. Miss the redirect and the member
still sees their booking. Each chapter says what that looks like.
