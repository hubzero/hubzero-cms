<!--
status: rewritten
reviewed-against: 2.4-main @ 348f0057c2
reviewed: 2026-09-10
source: https://help.hubzero.org/documentation/240/webdevs/foundation/providers
-->
# Service providers

A service provider registers something into the application container and,
optionally, does setup once everything is registered. Between them they
build the whole runtime: the database connection, the session, the router,
the document, the template, the plugin dispatcher, the mailer.

## The list of providers

Each client has its own list. The application reads up to three files and
merges them in order, so a hub can add providers of its own without touching
the platform:

1. `core/bootstrap/<client>/services.php`
2. `app/bootstrap/<client>/services.php`

The site list runs to twenty-five, in the order the application registers
them, beginning with rate limiting, events, translation, and the database,
and ending with the mailer, menu, and feed.

> **Note:** Every provider in the list is registered on every request. They
> are not deferred, and there is no mechanism for registering one only when
> the service it provides is asked for.

## Writing one

A provider extends `Hubzero\Base\ServiceProvider` and implements `register()`.
The base class stores the application as `$this->app`; registering a service
means assigning a closure to a key on it, which the container calls the first
time the key is read:

```php
namespace Bootstrap\Site\Providers;

use Hubzero\Base\ServiceProvider;
use Hubzero\Routing\Manager;

class RouterServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app['router'] = function($app)
        {
            return new Manager($app, array(PATH_CORE, PATH_APP));
        };
    }
}
```

The closure is what makes the *service* lazy: nothing constructs a `Manager`
until something reads `$this->app['router']`. The provider itself still runs
on every request.

Register the service under the same key a facade resolves, and the facade
reaches it. `Route` resolves `router`, so the provider above is what
`Route::url()` ends up calling. See [Facades](06-facades.md).

## Booting

After every provider has registered, the application walks the list again and
calls `boot()` on each one that defines it. Only then is it safe to use
another provider's service, because only then is every key present:

```php
public function boot()
{
    if ($this->app['config']->get('force_ssl') == 2)
    {
        if (!$this->app['request']->isSecure())
        {
            // redirect to https and stop
        }
    }
}
```

`boot()` is optional. `register()` is not.

## Middleware

A middleware provider is a service provider that also sits in the request
pipeline. It extends `Hubzero\Base\Middleware`, which extends
`ServiceProvider`, and must implement `handle()`:

```php
use Hubzero\Base\Middleware;
use Hubzero\Http\Request;

class ExampleServiceProvider extends Middleware
{
    public function handle(Request $request)
    {
        // inspect or change the request on the way in
        $response = $this->next($request);
        // inspect or change the response on the way out
        return $response;
    }
}
```

`$this->next($request)` hands control to the next middleware and returns the
response, so anything before that call runs on the way in and anything after
it on the way out. A middleware that returns without calling `next()` stops
the request there.

Middleware may register services as well; the router, component, and document
providers all do both.

Of the site's twenty-five providers, three are middleware: the router, the
component dispatcher, and the document. The rest only register services.
