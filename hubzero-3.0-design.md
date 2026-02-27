# Hubzero&reg; 3.0: Laravel Migration Architecture

Copyright &copy; 2026 Purdue University. All Rights Reserved.

A strangler-fig migration plan to replace `core/libraries/Hubzero/` with Laravel
while preserving the CMS extension architecture (components, plugins, modules)
as Composer packages.

**Constraints:**
- Database schema unchanged (tables, columns, prefixes stay as-is)
- Component/plugin/module/event systems preserved functionally
- Site, administrator, and API entry points all supported
- Laravel Octane + FrankenPHP worker mode from day one
- Optional Blade template support (existing PHP templates remain first-class)
- Multi-tenancy with three-layer override: group → tenant → shared packages
- Supergroups preserved as nested tenants within the tenant system (§21)
- All extensions are Composer packages with Laravel service providers
- Filament for admin panel infrastructure
- Laravel Notifications for multi-channel delivery (email, database, broadcast)
- Laravel Scout + Meilisearch replacing custom Solr search
- Laravel Reverb for real-time WebSocket features (optional per-deployment)
- Laravel Pennant feature flags for gradual migration rollout
- Frontend-agnostic: Blade, Livewire, Inertia, HTMX all first-class
- Design token system with Blade component library for consistent, themeable UI (§10)
- Layered bot protection: honeypot, behavioral scoring, Turnstile CAPTCHA (§27)
- Incremental migration — hybrid operation at every stage

---

## Table of Contents

1. [Foundation: Laravel as the Outer Shell](#1-foundation-laravel-as-the-outer-shell)
2. [Service Container & Providers](#2-service-container--providers)
3. [Multi-Client Architecture (Site / Admin / API)](#3-multi-client-architecture-site--admin--api)
4. [Routing & Menu System](#4-routing--menu-system)
5. [Component System](#5-component-system)
6. [Plugin & Event System](#6-plugin--event-system)
7. [Module System](#7-module-system)
8. [Database & ORM](#8-database--orm)
9. [View & Document Rendering Pipeline](#9-view--document-rendering-pipeline)
10. [Template System](#10-template-system)
11. [Extension Override System](#11-extension-override-system)
12. [Authentication & Authorization](#12-authentication--authorization)
13. [Session, Cache, Mail, Queue](#13-session-cache-mail-queue)
14. [Facades, Helpers & Language](#14-facades-helpers--language)
15. [Octane / FrankenPHP Considerations](#15-octane--frankenphp-considerations)
16. [CLI (Muse → Artisan)](#16-cli-muse--artisan)
17. [Extension Developer Experience](#17-extension-developer-experience)
18. [Testing](#18-testing)
19. [Asset Pipeline](#19-asset-pipeline)
20. [Migration Phases](#20-migration-phases)
21. [Supergroups (Nested Tenants)](#21-supergroups-nested-tenants)
22. [Metrics & Usage Analytics](#22-metrics--usage-analytics)
23. [Notifications](#23-notifications)
24. [Broadcasting & Real-Time](#24-broadcasting--real-time)
25. [Search](#25-search)
26. [Feature Flags](#26-feature-flags)
27. [Bot Protection & Abuse Prevention](#27-bot-protection--abuse-prevention)

---

## 1. Foundation: Laravel as the Outer Shell

### Current State
- Single `index.php` entry point → `new Application()->run()`
- `Application` extends a Pimple-based `Container` (closure-only, no auto-wiring)
- Client detected by URL segment (`/admin/`, `/api/`, etc.)
- Middleware stack built from ServiceProviders that extend `Middleware`

### Proposed Architecture

```
public/index.php (Laravel entry point, separate document root)
    └── Laravel Application (Illuminate\Foundation\Application)
            ├── Kernel::handle($request)
            │     ├── Tenant resolution middleware
            │     ├── Client detection middleware (site/admin/api)
            │     ├── Standard Laravel middleware pipeline
            │     └── Router dispatch
            └── Octane worker loop wraps this
```

**Key decisions:**
- Replace `Hubzero\Base\Application` with `Illuminate\Foundation\Application`
- Replace `Hubzero\Container\Container` (Pimple) with Laravel's IoC container (auto-wiring)
- `public/index.php` entry point with `public/` as Laravel's document root
- Legacy `index.php` at repo root stays untouched (see MVP roadmap)
- Single root `composer.json`
- FrankenPHP worker mode via Laravel Octane from the start
- All extensions (components, plugins, modules, templates, language packs) are
  Composer packages in `packages/` with hierarchical organization by type
- Multi-tenancy from day one: three-layer override (group → tenant → shared)
- Supergroups as nested tenants — same override mechanism, scoped to URL prefix (§21)
- No `app/` override layer — single-hub deployments are just one tenant
- Filament for admin panel (replaces Joomla-era admin template infrastructure)

**Directory layout:**

```
hubzero-cms/
├── bootstrap/                    ← Laravel bootstrap
│   ├── app.php                   ← Creates Laravel Application
│   ├── providers.php             ← Service provider list
│   └── cache/                    ← Compiled services/routes
│
├── config/                       ← Laravel config
│   ├── app.php
│   ├── database.php
│   ├── hubzero.php               ← Hubzero-specific config (tenancy, etc.)
│   └── ...
│
├── packages/                     ← All extensions as Composer packages
│   ├── hubzero/                  ← Hubzero core distribution
│   │   ├── components/
│   │   │   ├── blog/
│   │   │   │   ├── composer.json         # hubzero/component-blog
│   │   │   │   ├── src/
│   │   │   │   │   ├── BlogServiceProvider.php
│   │   │   │   │   ├── Http/Controllers/
│   │   │   │   │   └── Models/
│   │   │   │   ├── resources/
│   │   │   │   │   ├── views/
│   │   │   │   │   └── lang/
│   │   │   │   ├── routes/
│   │   │   │   ├── database/migrations/
│   │   │   │   ├── tests/
│   │   │   │   └── config.php
│   │   │   ├── members/
│   │   │   │   ├── composer.json         # hubzero/component-members
│   │   │   │   └── ...
│   │   │   └── support/
│   │   │       └── ...
│   │   │
│   │   ├── plugins/
│   │   │   ├── content/
│   │   │   │   ├── formathtml/
│   │   │   │   │   ├── composer.json     # hubzero/plugin-content-formathtml
│   │   │   │   │   └── src/
│   │   │   │   └── emailcloak/
│   │   │   │       ├── composer.json     # hubzero/plugin-content-emailcloak
│   │   │   │       └── src/
│   │   │   ├── members/
│   │   │   │   └── dashboard/
│   │   │   │       └── ...
│   │   │   └── system/
│   │   │       └── cache/
│   │   │           └── ...
│   │   │
│   │   ├── modules/
│   │   │   ├── menu/
│   │   │   │   ├── composer.json         # hubzero/module-menu
│   │   │   │   └── src/
│   │   │   ├── login/
│   │   │   └── breadcrumbs/
│   │   │
│   │   ├── templates/
│   │   │   ├── hubzero/
│   │   │   │   ├── composer.json         # hubzero/template-hubzero
│   │   │   │   ├── src/
│   │   │   │   │   └── HubzeroTemplateServiceProvider.php
│   │   │   │   ├── views/
│   │   │   │   └── assets/
│   │   │   └── admin/
│   │   │       └── ...
│   │   │
│   │   ├── languages/
│   │   │   ├── en/
│   │   │   │   ├── composer.json         # hubzero/lang-en
│   │   │   │   ├── src/
│   │   │   │   │   └── EnLanguageServiceProvider.php
│   │   │   │   └── strings/
│   │   │   │       ├── blog.php
│   │   │   │       └── members.php
│   │   │   └── fr/
│   │   │       └── ...
│   │   │
│   │   └── framework/            ← Hubzero framework library (all extensions depend on this)
│   │       ├── composer.json             # hubzero/framework
│   │       ├── resources/
│   │       │   ├── css/
│   │       │   │   ├── tokens.css       ← Design tokens (custom properties) (§10)
│   │       │   │   ├── base.css         ← Element defaults (typography, links)
│   │       │   │   └── components.css   ← Blade component styles
│   │       │   └── views/
│   │       │       └── components/      ← Blade component templates (hub-card, hub-button, etc.)
│   │       └── src/
│   │           ├── Foundation/           ← Base service providers (ComponentServiceProvider, etc.)
│   │           ├── Plugin/               ← Plugin/event infrastructure (PluginManager, HubzeroEventDispatcher)
│   │           ├── Module/               ← Module infrastructure (ModuleManager)
│   │           ├── Tenant/               ← Multi-tenancy (TenantManager, GroupPackageLoader, TenantPackageLoader)
│   │           ├── Extension/            ← Extension management (ExtensionManager, ExtensionResolver)
│   │           ├── Document/             ← Legacy document pipeline (§9)
│   │           ├── Routing/              ← Menu aliases, content routes, legacy URL bridge
│   │           ├── Translation/          ← .ini loader bridge
│   │           ├── Auth/                 ← Custom guards, policies (hubzero-token, HubzeroUserProvider)
│   │           ├── View/
│   │           │   └── Components/      ← Blade component classes (HubCard, HubButton, etc.) (§10)
│   │           ├── Analytics/           ← AnalyticsMiddleware, RecordAnalyticsHit (§22)
│   │           ├── Search/              ← Scout configuration, tenant index prefixes (§25)
│   │           ├── BotProtection/       ← BotScoreMiddleware, BlockedIpMiddleware (§27)
│   │           └── Facades/              ← Bridge facades (Lang::txt, Event::trigger, etc.)
│   │
│   └── acme/                     ← Operator-added packages (shared across all tenants)
│       └── components/
│           └── widget/
│               ├── composer.json         # acme/component-widget
│               └── src/
│
├── index.php                     ← Original HubZero entry point (unchanged)
├── public/                       ← Laravel document root
│   └── index.php                 ← Laravel entry point
│
├── routes/                       ← Laravel route files (top-level)
│   ├── web.php
│   ├── admin.php
│   └── api.php
│
├── storage/                      ← Laravel storage
│   ├── framework/
│   ├── groups/                  ← Supergroup nested tenants (§21)
│   │   ├── {gidNumber}/
│   │   │   ├── packages/        ← Group-scoped extension overrides
│   │   │   ├── storage/         ← Group uploads, cache, logs
│   │   │   ├── config/          ← Optional group-specific DB config
│   │   │   └── migrations/      ← Group-specific migrations
│   │   └── ...
│   └── logs/
│
├── tests/                        ← Cross-package integration tests
│   ├── Feature/
│   └── TestCase.php
│
├── vendor/                       ← Composer vendor (packages symlinked here)
├── composer.json                 ← Single root Composer config
├── phpunit.xml                   ← Discovers tests across all packages
├── .env                          ← Environment config
│
└── tenants/                      ← Per-tenant overrides and additions
    ├── hub1.example.com/
    │   ├── packages/             ← Tenant-specific extensions (mirrors packages/)
    │   │   └── acme/
    │   │       ├── components/
    │   │       │   └── widget/   ← Same package, just tenant-scoped
    │   │       └── templates/
    │   │           └── custom-theme/
    │   ├── config/
    │   ├── storage/
    │   └── .env
    └── hub2.example.com/
        └── ...
```

**Root `composer.json` path repositories:**

```json
{
    "repositories": [
        { "type": "path", "url": "packages/*/framework" },
        { "type": "path", "url": "packages/*/components/*" },
        { "type": "path", "url": "packages/*/plugins/*/*" },
        { "type": "path", "url": "packages/*/modules/*" },
        { "type": "path", "url": "packages/*/templates/*" },
        { "type": "path", "url": "packages/*/languages/*" }
    ],
    "require": {
        "hubzero/framework": "*",
        "hubzero/component-blog": "*",
        "hubzero/component-members": "*",
        "hubzero/plugin-content-formathtml": "*",
        "hubzero/module-menu": "*",
        "hubzero/template-hubzero": "*",
        "hubzero/lang-en": "*"
    }
}
```

Composer resolves path repos as symlinks into `vendor/`, so packages are
developed in-place under `packages/` but autoloaded via `vendor/`. The
wildcard in the vendor slot (`packages/*/components/*`) lets operator-added
vendors (e.g., `packages/acme/`) work without modifying `composer.json`
repositories — only a `composer require acme/component-widget` is needed.

**Tenant directories** mirror the same `packages/` hierarchy so a single
extension package can be installed in either location without modification:
- `packages/acme/components/widget/` — shared across all tenants
- `tenants/hub1.example.com/packages/acme/components/widget/` — tenant-only

The override chain is: **group packages → tenant packages → shared packages → Laravel core** (see §21 for groups).

**Entry points:** Legacy `index.php` stays at the repo root untouched — the
existing document root is preserved. Laravel uses the conventional
`public/index.php` with `public/` as its own document root.

---

## 2. Service Container & Providers

### Current State
- Pimple-derived container: `$app['service'] = function($app) { ... };`
- ~25 service providers per client, registered from `bootstrap/{Client}/services.php`
- No constructor injection / auto-wiring
- Providers that extend `Middleware` participate in the HTTP stack

### Proposed Architecture

**Replace with Laravel's IoC container.** This is the single biggest architectural win:
constructor injection, contextual binding, auto-wiring, tagged bindings.

**Bridge pattern for transition:**

```php
// During migration, a HubzeroServiceProvider bridges old registrations
class HubzeroServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Bridge: make old $app['key'] access work via Laravel container
        // Gradually remove as services are converted to proper bindings
    }
}
```

**Provider mapping** (current → Laravel):

| Current Provider | Laravel Equivalent | Notes |
|---|---|---|
| `EventServiceProvider` | `Illuminate\Events\EventServiceProvider` | Custom subclass for Hubzero events (§6) |
| `DatabaseServiceProvider` | `Illuminate\Database\DatabaseServiceProvider` | Config bridge to existing DB settings |
| `RouterServiceProvider` | `Illuminate\Routing\RoutingServiceProvider` | + Hubzero component routing (§4) |
| `SessionServiceProvider` | `Illuminate\Session\SessionServiceProvider` | Map to existing session table |
| `CacheServiceProvider` | `Illuminate\Cache\CacheServiceProvider` | Direct replacement |
| `MailerServiceProvider` | `Illuminate\Mail\MailServiceProvider` | Direct replacement; email via queue (§13) |
| `FilesystemServiceProvider` | `Illuminate\Filesystem\FilesystemServiceProvider` | Already uses Flysystem |
| `LogServiceProvider` | `Illuminate\Log\LogServiceProvider` | Already uses Monolog |
| `AuthServiceProvider` | `Illuminate\Auth\AuthServiceProvider` | Custom guard for Hubzero users (§12) |
| `DocumentServiceProvider` | **NEW** `Hubzero\DocumentServiceProvider` | Preserved until Blade migration (§9) |
| `MenuServiceProvider` | **NEW** `Hubzero\MenuServiceProvider` | Preserved |
| `TranslationServiceProvider` | `Illuminate\Translation\TranslationServiceProvider` | Bridge for `Lang::txt()` + `.ini` loader (§14) |
| `EditorServiceProvider` | **NEW** `Hubzero\EditorServiceProvider` | Preserved |
| `ErrorServiceProvider` | Laravel's exception handler | Direct replacement |
| (none — ad-hoc email) | `Illuminate\Notifications\NotificationServiceProvider` | Multi-channel notifications (§23) |
| (none) | `Illuminate\Broadcasting\BroadcastServiceProvider` | Real-time via Reverb (§24) |
| Custom Solr integration | `Laravel\Scout\ScoutServiceProvider` | Full-text search (§25) |
| (none) | `Laravel\Pennant\PennantServiceProvider` | Feature flags (§26) |
| (none) | `Hubzero\View\ComponentLibraryServiceProvider` | Blade component library (§10) |
| (none) | `Hubzero\Tenant\TenantBrandingServiceProvider` | Per-tenant branding CSS (§10) |

Component, plugin, module, and template service providers are no longer
system-level — each extension package provides its own (see §5–§7, §10).

**Middleware separation:** Current system conflates service providers and middleware.
Laravel cleanly separates these. Each current `Middleware` provider becomes:
- A standard `ServiceProvider` (for DI registration)
- A standard Laravel `Middleware` class (for HTTP pipeline)

---

## 3. Multi-Client Architecture (Site / Admin / API)

### Current State
- Client detected at boot by URL segment (`/admin/`, `/api/`)
- Each client loads its own `services.php` and `aliases.php`
- `$app['client']` object available globally
- Components have `site/`, `admin/`, `api/` subdirectories

### Proposed Architecture

**Use Laravel's route grouping + middleware groups** — this is idiomatic Laravel
and maps cleanly to the current client concept.

```php
// routes/web.php — Site (default)
// BotScoreMiddleware and BlockedIpMiddleware run globally before
// route middleware — see §27 for the full middleware stack order.
Route::middleware(['web', 'hubzero.site'])->group(function () {
    // Component routes registered dynamically (§4)
});

// routes/admin.php — Administrator (legacy bridge only)
Route::prefix('admin')
    ->middleware(['web', 'hubzero.admin', 'auth.admin'])
    ->group(function () {
        // Filament owns admin routing for migrated components.
        // This file only handles the legacy catch-all for un-migrated
        // admin controllers during the transition. Remove when migration complete.
    });

// routes/api.php — API
Route::prefix('api')
    ->middleware(['api', 'hubzero.api', 'throttle:api'])
    ->group(function () {
        // API component routes (rate-limited — §12)
    });
```

**The `hubzero.site` / `hubzero.admin` / `hubzero.api` middleware** replaces
client detection. It:
1. Sets the client context (`app('hubzero.client')`)
2. Loads client-specific services (document, toolbar, etc.)
3. Sets the component subdirectory context (`site/`, `admin/`, `api/`)

**Supergroup routes** under `/groups/{cn}/` use `SupergroupMiddleware` which
enters group context on the `TenantManager` before dispatching (§21). Group
managers also get a scoped Filament panel at `/groups/{cn}/manage/`.

**Admin panel:** Filament provides the admin shell — sidebar navigation, auth,
layout, dark mode, tenant switching. Components register Filament Resources
for standard CRUD or custom pages for specialized admin UI. Filament replaces
the Joomla-era admin template infrastructure entirely. See §5 for how
components integrate with Filament.

**API authentication:** The existing token-based auth system is preserved via
a custom Laravel auth guard (`hubzero-token` driver), so existing API clients
continue to work unchanged. A second guard (`api-oauth` via Laravel Passport)
can be added alongside when OAuth2 support is needed. API routes accept
either method via `auth:api,api-oauth` middleware.

**Benefit:** Laravel's route caching works. Octane keeps the router in memory.
No per-request client detection overhead.

---

## 4. Routing & Menu System

### Current State
- `Routing\Router` with ordered parse/build rule closures
- Menu-based SEF routing: URL path matched against `#__menu` items
- Per-component `router.php` with `parse()` / `build()` methods
- `Route::url('index.php?option=com_blog&task=entry&alias=foo')` builds SEF URLs
- Two-layer: global router finds the component, component router parses remaining segments

### Proposed Architecture

Components own their routes via service providers. Menus become a navigation +
optional URL alias layer. Content pages register as database-driven routes.

#### Component Routes

Each component package registers its own routes:

```php
// packages/hubzero/components/blog/routes/site.php
use Illuminate\Support\Facades\Route;

Route::prefix('blog')->group(function () {
    Route::get('/', [EntriesController::class, 'index'])->name('blog.index');
    Route::get('/{year}', [EntriesController::class, 'browse'])->name('blog.browse');
    Route::get('/{year}/{month}/{alias}', [EntriesController::class, 'entry'])->name('blog.entry');
    Route::post('/{year}/{month}/{alias}/comments', [CommentsController::class, 'store']);
});
```

Each component's service provider loads its own routes:

```php
class BlogServiceProvider extends ComponentServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/site.php');
    }
}
```

**URL generation:** `Route::url()` facade replaced with Laravel's `route()` helper:

```php
// Old: Route::url('index.php?option=com_blog&task=entry&alias=foo')
// New: route('blog.entry', ['year' => 2024, 'month' => 3, 'alias' => 'foo'])
```

**Backward compatibility bridge** during migration:

```php
// Shim that translates old-style Route::url() calls to named routes
Route::macro('url', function ($query) {
    return app(LegacyUrlBuilder::class)->build($query);
});
```

#### Content Page Routes

Content pages (com_content) have URLs at arbitrary paths (`/about/team`,
`/policies/privacy`) with no component prefix. These are registered as
exact-match routes from the database:

```php
class ContentServiceProvider extends ComponentServiceProvider
{
    protected function registerContentRoutes(): void
    {
        $pages = DB::table('content')
            ->join('categories', 'content.catid', '=', 'categories.id')
            ->where('content.state', 1)
            ->select('content.alias as page_alias', 'categories.path as category_path')
            ->get();

        foreach ($pages as $page) {
            $path = trim($page->category_path . '/' . $page->page_alias, '/');

            Route::get($path, [ContentController::class, 'show'])
                ->name('content.page.' . Str::slug($path, '.'))
                ->defaults('_content_path', $path);
        }
    }
}
```

#### Route Caching

Laravel's built-in `route:cache` only serializes statically-defined routes.
Content page routes and menu alias routes come from the database, so they
need their own caching strategy.

`php artisan hubzero:routes:cache` builds a cached PHP file
(`bootstrap/cache/hubzero_routes.php`) containing all DB-driven routes as
static registrations. This file is loaded at boot instead of querying the DB.

**Automatic rebuild:** Eloquent model observers on the `ContentPage` and
`Menu` models call `hubzero:routes:cache` after create/update/delete. With
Octane, the observer also triggers `octane:reload` to refresh workers.

```php
class ContentPageObserver
{
    public function saved(ContentPage $page)
    {
        Artisan::call('hubzero:routes:cache');

        if (app()->bound(Server::class)) {
            Artisan::call('octane:reload');
        }
    }
}
```

**Manual rebuild:** The Filament admin panel includes a "Rebuild Routes"
button on the route health dashboard for operators who suspect routes are
out of sync. This calls the same `hubzero:routes:cache` command.

Without Octane, the cached file is read on every request (fast file include,
no DB query). With Octane, routes sit in memory after worker boot.

#### Menu Aliases

Menu items are primarily navigation entries that link to named routes. They
can optionally define a URL alias to override a component's default path:

```php
class MenuRouteRegistrar
{
    public function register(): void
    {
        $aliases = DB::table('menu')->whereNotNull('alias')->get();

        foreach ($aliases as $item) {
            Route::get($item->alias . '/{path?}', function () use ($item) {
                // Resolve the named route's controller, merge menu params
            })
            ->where('path', '.*')
            ->defaults('_menu_id', $item->id)
            ->name('menu.' . $item->id);
        }
    }
}
```

#### Route Precedence

Registration order determines priority:

```php
// routes/web.php

// 1. Content page routes (exact-match paths from DB — /about/team, /members)
//    Can override component index routes intentionally

// 2. Menu alias routes (operator-defined URL overrides)

// 3. Group routes (/groups/{cn}/... — SupergroupMiddleware enters group context)
//    Group-specific routes registered by GroupPackageLoader (§21)

// 4. Component routes (parameterized — /members/{username}, /blog/{year}/{alias})
//    Registered via service providers
//    Feature flags (§26) can gate between migrated and legacy controllers

// 5. Legacy catch-all for un-migrated components (temporary, removed after migration)
Route::fallback([LegacyDispatchController::class, 'handle']);
```

Content pages have highest priority because they're exact-match routes.
This means a content page at `/members` overrides the members component
index page, but `/members/blah` still routes to the component because
the exact content match doesn't cover parameterized paths. This is
intentional — operators can create custom landing pages that overlay
component index routes.

**Route health dashboard:** The Filament admin panel includes a route
health view showing all registered routes, highlighting where content
pages or menu aliases override component routes. This makes overrides
visible and intentional rather than surprising.

---

## 5. Component System

### Current State
- Components in `core/components/com_{name}/` with `site/`, `admin/`, `api/` dirs
- `Component\Loader` resolves path (app → core), renders via ob_start()
- `SiteController` / `AdminController` / `ApiController` base classes
- Task dispatch: `{task}Task()` method convention
- Component config in `#__extensions.params` (JSON), schema in `config/config.xml`
- Component enabled/disabled via `#__extensions` table

### Proposed Architecture

Components are Composer packages in `packages/{vendor}/components/{name}/`.

**Package structure:**

```
packages/hubzero/components/blog/
├── composer.json
├── src/
│   ├── BlogServiceProvider.php
│   ├── Filament/                 ← Filament admin resources
│   │   ├── Resources/
│   │   │   └── EntryResource.php
│   │   └── Pages/
│   ├── Models/
│   │   ├── Entry.php             ← Eloquent model
│   │   └── Comment.php
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Site/
│   │   │   │   ├── EntriesController.php
│   │   │   │   └── CommentsController.php
│   │   │   └── Api/
│   │   │       └── EntriesController.php
│   │   ├── Requests/             ← Form request validation
│   │   └── Resources/            ← API resources (JSON transformers)
│   ├── Notifications/            ← Notification classes (§23)
│   ├── Policies/                 ← Authorization policies (§12)
│   └── Events/
├── resources/
│   ├── views/
│   │   ├── site/
│   │   │   └── entries/
│   │   │       ├── index.blade.php
│   │   │       └── show.blade.php
│   │   └── admin/
│   └── lang/
│       └── en/
│           └── blog.php
├── routes/
│   ├── site.php
│   └── api.php
├── database/
│   ├── migrations/
│   └── factories/
├── tests/
│   ├── Feature/
│   └── Unit/
└── config.php
```

**`composer.json` for a component:**

```json
{
    "name": "hubzero/component-blog",
    "description": "Hubzero Blog component",
    "type": "library",
    "require": {
        "hubzero/framework": "^3.0",
        "hubzero/component-members": "^3.0"
    },
    "autoload": {
        "psr-4": {
            "Hubzero\\Component\\Blog\\": "src/"
        }
    },
    "extra": {
        "laravel": {
            "providers": [
                "Hubzero\\Component\\Blog\\BlogServiceProvider"
            ]
        }
    }
}
```

Laravel's package auto-discovery reads the `extra.laravel.providers` field
and registers the service provider automatically — no manual registration needed.

**Service provider (extends typed base class — see §17):**

```php
namespace Hubzero\Component\Blog;

use Hubzero\Foundation\ComponentServiceProvider;

class BlogServiceProvider extends ComponentServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config.php', 'blog');
    }

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/site.php');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'blog');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'blog');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }

    public function registerPermissions(): array
    {
        return [
            'blog.manage' => 'Manage blog entries',
            'blog.create' => 'Create blog entries',
            'blog.edit'   => 'Edit blog entries',
            'blog.delete' => 'Delete blog entries',
        ];
    }

    public function registerNavigation(): array
    {
        return [
            'blog' => [
                'label' => 'Blog',
                'icon'  => 'heroicon-o-pencil',
                'route' => 'admin.blog.index',
            ],
        ];
    }
}
```

**Admin via Filament:** Simple components use Filament Resources for standard
CRUD (list, create, edit, view). Complex components (e.g., com_publications
with its curation workflow, versioning, batch import) use Filament custom
pages — a first-class Filament feature that gives full Livewire control while
still providing the admin shell (sidebar, navigation, auth, notifications).
Filament's form and table builder components can be used inside custom pages.

```php
// Simple component: Filament Resource
class EntryResource extends Resource
{
    protected static ?string $model = Entry::class;
    protected static ?string $navigationGroup = 'Blog';

    public static function form(Form $form): Form { ... }
    public static function table(Table $table): Table { ... }
}

// Complex component: Filament custom page
class PublicationCuration extends Page
{
    protected static string $view = 'publications::admin.curation';
    protected static ?string $navigationGroup = 'Publications';
    // Full Livewire component — do whatever you want here
}
```

**Backward compatibility:** Un-migrated components keep their current structure.
The `LegacyDispatchController` handles them through the existing `Component\Loader`.
This catch-all is temporary and removed when all components are migrated.
Feature flags (§26) gate individual components between legacy and migrated
versions — operators can roll out migrated components per-tenant or per-user
and instantly rollback if issues arise.

**Controller evolution:**

```php
// Current: SiteController with {task}Task() convention
class Entries extends SiteController
{
    public function displayTask() { ... }
    public function editTask() { ... }
    public function saveTask() { ... }
}

// New: Standard Laravel controller
class EntriesController extends Controller
{
    public function index(Request $request) { ... }    // was displayTask
    public function show(Entry $entry) { ... }         // route model binding
    public function edit(Entry $entry) { ... }
    public function store(StoreEntryRequest $request) { ... }  // form request validation
    public function update(UpdateEntryRequest $request, Entry $entry) { ... }
}
```

**Component config:** `#__extensions.params` continues to store runtime config.
Accessed via a `ComponentConfig` service that wraps the DB values into Laravel config:

```php
// Old: Component::params('com_blog')->get('feeds_enabled')
// New: config('components.blog.feeds_enabled')
//  or: app('component.config')->get('blog', 'feeds_enabled')
```

**Component enabled/disabled:** See §17 for the enable/disable system.

---

## 6. Plugin & Event System

### Current State
- 37 plugin groups (content, members, groups, system, etc.)
- Plugins loaded from DB (`#__extensions`), ordered by `ordering` column
- `Plugin::import('members')` loads all plugins of a type
- `Event::trigger('members.onMembersAreas', [...])` dispatches
- Dot-prefix triggers lazy-loading of plugin group
- Return values collected into array
- `WrappedListener` unpacks Event args into positional method params
- Plugins have their own views, params, language files

### Proposed Architecture

**This is where we intentionally diverge from "standard Laravel."** Laravel's event
system is 1:1 (one event class → one or more listeners). Hubzero's plugin system
is N:M (one plugin class handles many events, events return collected arrays).

**Preserve the plugin/event architecture as a first-class Laravel package.**

Plugins are Composer packages in `packages/{vendor}/plugins/{group}/{name}/`:

```
packages/hubzero/plugins/content/formathtml/
├── composer.json                     # hubzero/plugin-content-formathtml
├── src/
│   ├── FormatHtmlServiceProvider.php
│   └── FormatHtml.php                ← Plugin class
├── resources/
│   ├── views/
│   └── lang/
└── config.php
```

**`composer.json` for a plugin:**

```json
{
    "name": "hubzero/plugin-content-formathtml",
    "description": "Hubzero content formatting plugin",
    "type": "library",
    "autoload": {
        "psr-4": {
            "Hubzero\\Plugin\\Content\\FormatHtml\\": "src/"
        }
    },
    "extra": {
        "laravel": {
            "providers": [
                "Hubzero\\Plugin\\Content\\FormatHtml\\FormatHtmlServiceProvider"
            ]
        }
    }
}
```

**System-level plugin infrastructure:**

```php
// Registered as a core service provider
class HubzeroPluginServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('hubzero.plugins', function ($app) {
            return new PluginManager($app, $app['db']);
        });

        $this->app->singleton('hubzero.events', function ($app) {
            return new HubzeroEventDispatcher($app);
        });
    }
}
```

**`HubzeroEventDispatcher`** wraps Laravel's event system but adds:
- Group-based lazy loading (`'members.onMembersAreas'` auto-imports `members` plugins)
- Collected return values (array of all listener responses)
- Priority ordering from DB `ordering` column
- `WrappedListener`-style argument unpacking

```php
class HubzeroEventDispatcher
{
    public function trigger(string $event, array $args = []): array
    {
        if (str_contains($event, '.')) {
            [$group, $event] = explode('.', $event, 2);
            $this->plugins->import($group);
        }

        return $this->plugins->dispatch($event, $args);
    }
}
```

**Plugin base class updated:**

```php
namespace Hubzero\Plugin;

abstract class Plugin
{
    protected Registry $params;
    protected string $type;     // group name
    protected string $name;     // plugin name

    // Existing method signature preserved — plugins don't need to change
    // public function onContentPrepare($context, &$article, &$params, $page) { ... }
}
```

**Facade preserved:**

```php
// These continue to work unchanged in component/plugin code:
Event::trigger('content.onContentPrepare', [$context, &$article, &$params, $page]);
Plugin::import('members');
Plugin::params('formathtml', 'content');
```

**Plugin views:** Plugin view rendering preserved. Template override paths maintained.

**Notifications from events:** Plugins that currently send email directly
(e.g., `onGroupUserAdded` sending a welcome email) should dispatch Laravel
Notification classes instead (§23). The notification handles channel routing,
user preferences, and queued delivery — plugins just trigger them.

**Future path:** Individual events can optionally be converted to Laravel event classes
for better IDE support and type safety, while the dispatcher handles both styles:

```php
// New-style (optional, for new code):
class ContentPrepared implements HubzeroEvent
{
    public function __construct(
        public string $context,
        public object &$article,
        public Registry &$params,
        public int $page = 0,
    ) {}
}

Event::trigger(new ContentPrepared($context, $article, $params, $page));
```

---

## 7. Module System

### Current State
- Modules in `core/modules/mod_{name}/`
- Position assignment in DB (`#__modules.position`, `#__modules_menu.menuid`)
- Template calls `$app['module']->position('sidebar')` to render
- Module chrome (wrapper HTML) from `templates/system/html/modules.php`
- Layout override via `{template}/html/{module_name}/{layout}.php`

### Proposed Architecture

Modules are Composer packages in `packages/{vendor}/modules/{name}/`:

```
packages/hubzero/modules/menu/
├── composer.json                     # hubzero/module-menu
├── src/
│   ├── MenuServiceProvider.php
│   └── Menu.php                      ← Module class
└── resources/
    └── views/
        └── default.blade.php
```

**Modules map well to Laravel's view composers + Blade components**, but we preserve
the database-driven position system since it's what makes CMS modules useful.

```php
class ModuleManager
{
    public function position(string $position, array $attribs = []): string
    {
        $modules = $this->getModulesForPosition($position);  // DB query, cached
        $output = '';

        foreach ($modules as $module) {
            $output .= $this->render($module, $attribs);
        }

        return $output;
    }

    public function render(object $module, array $attribs = []): string
    {
        $class = $this->resolveClassName($module);
        $instance = app()->make($class, ['params' => $module->params, 'module' => $module]);
        // Modules can now use constructor injection!

        return $instance->render();
    }
}
```

**In templates (Blade or PHP):**

```blade
{{-- Blade syntax --}}
{!! $modules->position('sidebar') !!}

@if($modules->count('sidebar') > 0)
    <aside>{!! $modules->position('sidebar') !!}</aside>
@endif
```

```php
<!-- PHP template syntax (preserved) -->
<?php echo $this->app['module']->position('sidebar'); ?>
```

**Module chrome** becomes Blade components or preserved as-is:

```blade
{{-- Module chrome as Blade component --}}
<x-module-chrome style="rounded" :module="$module">
    {!! $content !!}
</x-module-chrome>
```

**Group-scoped modules:** Supergroups (§21) use the same `ModuleManager` with
a scope filter. The `#__xgroups_modules` table is unified into `#__modules`
with `scope = 'group'` and `scope_id = {gidNumber}` columns. When inside a
group context, `ModuleManager::position()` automatically includes group-scoped
modules alongside site-scoped ones.

---

## 8. Database & ORM

### Current State
- `Hubzero\Database\Relational` — custom Eloquent-like ORM
- Relationship names: `oneToMany()`, `belongsToOne()`, `manyToMany()`, `oneShiftsToMany()`
- Query builder: `Hubzero\Database\Query`
- Table prefix: `#__` (replaced at query time)
- Multiple dialect support (MySQL, MariaDB, Postgres, SQLite)
- Driver: custom PDO wrappers
- **Database schema is NOT changing**

### Proposed Architecture

**Use Eloquent directly.** The mapping is mechanical:

| Hubzero Relational | Eloquent |
|---|---|
| `$table = '#__blog_entries'` | `$table = 'blog_entries'` (prefix in config) |
| `oneToMany('Comment')` | `hasMany(Comment::class)` |
| `belongsToOne('User')` | `belongsTo(User::class)` |
| `manyToMany('Tag')` | `belongsToMany(Tag::class)` |
| `oneToManyThrough('Section')` | `hasManyThrough(...)` |
| `oneShiftsToMany(...)` | `morphMany(...)` |
| `manyShiftsToMany(...)` | `morphToMany(...)` |
| `->including('comments')` | `->with('comments')` |
| `$initiate = ['created']` | `const CREATED_AT = 'created'` |
| `$rules = ['title' => 'notempty']` | Form Request validation |
| `Rows` collection | `Illuminate\Database\Eloquent\Collection` |

**Table prefix:** Laravel supports table prefixes natively:

```php
// config/database.php
'mysql' => [
    'prefix' => '',  // no prefix needed if tables don't use #__
    // OR use a custom grammar to strip #__ at query time
],
```

Since the current `#__` prefix is replaced at query time and the actual tables may
or may not have a prefix, we configure Laravel's prefix to match the actual DB.

**Bridge for un-migrated models:**

```php
// Relational base class shimmed to extend Eloquent during transition
namespace Hubzero\Database;

class Relational extends \Illuminate\Database\Eloquent\Model
{
    public function oneToMany($related, $foreignKey = null)
    {
        return $this->hasMany($related, $foreignKey);
    }

    public function belongsToOne($related, $foreignKey = null)
    {
        return $this->belongsTo($related, $foreignKey);
    }

    // ... other method bridges
}
```

This lets existing model code work unchanged while new models use Eloquent directly.

**Search indexing:** Models that should be full-text searchable add the
`Searchable` trait (§25). Scout automatically syncs the search index when
models are created, updated, or deleted — no manual indexing needed.

**Raw queries:** Any code using `$db->setQuery()` directly gets a bridge:

```php
// Old: App::get('db')->setQuery($sql)->loadObjectList()
// Bridge: DB::select($sql)  (Laravel's query builder)
```

### Database Migrations

**The existing schema is not changing** — Hubzero 3.0 uses the same tables and
columns. The migration strategy is: baseline snapshot + Laravel migrations
going forward.

**Baseline migrations:** Each component package includes a baseline migration
that represents its current schema. This is a one-time schema reset — the
opportunity to have a clean, authoritative definition of every table.

```php
// packages/hubzero/components/blog/database/migrations/0001_create_blog_tables.php
class CreateBlogTables extends Migration
{
    public function up()
    {
        if (Schema::hasTable('blog_entries')) {
            return; // Existing hub — tables already present
        }

        Schema::create('blog_entries', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('fulltxt')->nullable();
            $table->tinyInteger('state')->default(0);
            $table->timestamp('created')->nullable();
            $table->unsignedBigInteger('created_by')->default(0);
            // ... full current schema
        });
    }
}
```

On **existing hubs**, a one-time setup command seeds Laravel's `migrations`
table with all baseline migrations marked as already run:

```bash
php artisan hubzero:migrate:baseline
# Marks all 0001_* baseline migrations as complete without executing them
# Imports muse migration history so nothing re-runs
```

On **fresh installs**, baseline migrations run normally and create all tables.

**Future schema changes** use standard Laravel migrations within each package:

```php
// packages/hubzero/components/blog/database/migrations/2026_03_15_add_featured_to_blog_entries.php
class AddFeaturedToBlogEntries extends Migration
{
    public function up()
    {
        Schema::table('blog_entries', function (Blueprint $table) {
            $table->boolean('featured')->default(false)->after('state');
        });
    }

    public function down()
    {
        Schema::table('blog_entries', function (Blueprint $table) {
            $table->dropColumn('featured');
        });
    }
}
```

**Per-package migrations** are loaded via `$this->loadMigrationsFrom()` in each
component's service provider. Laravel discovers and runs them in timestamp order
across all packages with a single `php artisan migrate`.

**Multi-tenancy consideration:** If tenants share a database with table prefixes
or separate databases, Laravel's migration system supports both — tenant-aware
migrations run against the correct connection/prefix via the TenantManager.

**Group database isolation:** Supergroups (§21) can optionally have their own
database via a dynamic `group` connection registered by `GroupDatabaseServiceProvider`.
Group-specific migrations live in `storage/groups/{gidNumber}/migrations/` and
run against the group's connection.

---

## 9. View & Document Rendering Pipeline

### Current State

```
Component controller → View::loadTemplate() → PHP template file
    ↓ (output captured via ob_start)
DocumentServiceProvider → Document::setBuffer($content, 'component')
    ↓
Document::parse() → require template/index.php (captures full page)
    ↓
Document::render() → replaces <jdoc:include> tags with rendered content
    ↓
Response sent
```

Key pieces:
- `Document\Base` accumulates CSS, JS, meta tags throughout the request
- `Document\Type\Html` assembles the full page
- `<jdoc:include type="head|component|modules|message">` directives in templates
- `Head` renderer compiles all accumulated assets into `<head>` HTML

### Proposed Architecture

**Phase 1: Preserve the Document pipeline, adapt to Laravel lifecycle**

The Document system is the hardest to replace because it's deeply intertwined with
how components, modules, and templates add assets. Keep it initially:

```php
class DocumentServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('document', function ($app) {
            return new Hubzero\Document\Manager();
        });
    }
}

class DocumentMiddleware
{
    public function handle($request, $next)
    {
        $response = $next($request);

        // Only for HTML responses from Hubzero components
        if ($this->isHubzeroHtmlResponse($response)) {
            $document = app('document');
            $document->setBuffer($response->getContent(), 'component');
            $document->parse($this->getTemplateParams());
            $rendered = $document->render();
            $response->setContent($rendered);
        }

        return $response;
    }
}
```

**Phase 2: Blade integration**

Add Blade as an alternative rendering engine. Components can opt in:

```php
// New-style controller returns a Blade view
class EntriesController extends Controller
{
    public function show(Entry $entry)
    {
        return view('blog::site.entries.show', compact('entry'));
    }
}
```

For Blade views, the Document middleware is bypassed. Instead, a Blade layout handles
the full page:

```blade
{{-- hubzero::layouts.base — the framework's base layout that all templates extend --}}
{{-- Templates extend this with @extends('hubzero::layouts.base') — see §10 --}}
<!DOCTYPE html>
<html>
<head>
    @include('hubzero::partials.head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>document.cookie='_js=1;path=/;SameSite=Lax';</script> {{-- bot detection (§27) --}}
    @hubzeroAssets {{-- renders accumulated Document CSS/JS for legacy components --}}
    @stack('styles')
</head>
<body>
    @yield('header')
    <x-notification-bell />

    <main>
        @yield('body')
    </main>

    @yield('footer')
    @stack('scripts')
</body>
</html>
```

**The key insight:** During the hybrid phase, both rendering paths coexist:
- Legacy components → Document pipeline → `<jdoc:include>` template
- Migrated components → Blade views → Blade layout

The `DocumentMiddleware` detects which path to use based on the response type.

**Asset management bridge:** `$this->css()` / `$this->js()` / `Document::addScript()`
continue to work. For Blade views, a `@hubzeroAssets` directive renders accumulated
Document assets into the Blade layout head.

### Frontend Agnosticism

HubZero 3.0 is intentionally **not opinionated** about frontend rendering. The
framework provides plumbing for multiple approaches and lets extension developers
choose what fits their component. Blade + Alpine is the *slight preference* (it
works with zero build step and ships with Laravel), but all of the following are
first-class citizens:

| Approach | Best For | Build Step | Ships With |
|---|---|---|---|
| **Blade + Alpine** | Most pages, progressive enhancement | No | Laravel |
| **Livewire** | Server-driven reactivity (forms, filters) | No | Filament dep |
| **Inertia (Vue/React/Svelte)** | SPA-like interfaces, rich client state | Yes (Vite) | `composer require inertiajs/inertia-laravel` |
| **HTMX** | Progressive enhancement, partial swaps | No | Script tag |
| **Legacy PHP views** | Un-migrated components | No | Document pipeline |

A controller just returns a response — the framework doesn't care how:

```php
// Blade (default — no build step, works everywhere)
return view('blog::entries.show', compact('entry'));

// Livewire (component is the view — already available via Filament)
return view('blog::entries.index'); // contains <livewire:entry-filter />

// Inertia (SPA-like — extension opts in via inertia middleware)
return Inertia::render('Blog/Entries/Show', ['entry' => $entry]);

// HTMX (progressive enhancement — returns fragment for swap)
if ($request->header('HX-Request')) {
    return view('blog::entries._list', compact('entries'));
}
return view('blog::entries.index', compact('entries'));

// Legacy (Document pipeline handles it — no changes needed)
$this->view->display();
```

**Admin panel:** Filament (Livewire) is the default admin infrastructure, but
extension developers can register custom admin routes outside the Filament panel
using Inertia, HTMX, or plain Blade if their admin UI requires it.

**Asset coexistence:** Extensions using Vite (for Inertia/Vue/React) register
their own `vite.config.js` and entry points. The template's `@vite` directive
and the Document pipeline's `@hubzeroAssets` directive coexist — Vite-built
assets and legacy `$this->css()`/`$this->js()` calls all render into the same
page head without conflict.

**Real-time via Echo:** The base template layout includes Laravel Echo (§24) —
a lightweight JavaScript library that connects to Reverb's WebSocket server.
Echo powers the notification bell, live tool session status, and project
activity feeds. It works with all frontend approaches (Blade, Livewire,
Inertia, HTMX) since it's a standalone `<script>` tag.

**The guiding principle:** The framework handles routing, middleware, auth, and
data access. What happens between the controller and the browser is the extension
developer's choice.

---

## 10. Template System

### Current State
- Templates in `app/templates/` and `core/templates/`
- `index.php` executed via `ob_start()` + require
- `<jdoc:include>` tags replaced by renderers
- Active template selected from DB (`#__template_styles`)
- Template params from `templateDetails.xml`
- Module chrome functions in `templates/{name}/html/modules.php`
- View overrides in `templates/{name}/html/{com_name}/{view}/`

### Proposed Architecture

Templates are Composer packages in `packages/{vendor}/templates/{name}/`:

```
packages/hubzero/templates/hubzero/
├── composer.json                         # hubzero/template-hubzero
├── src/
│   └── HubzeroTemplateServiceProvider.php
├── views/
│   ├── index.php                         ← Legacy PHP template
│   ├── index.blade.php                   ← Blade version (optional)
│   ├── html/                             ← View overrides
│   │   ├── com_blog/
│   │   │   └── entries/
│   │   │       └── show.blade.php
│   │   └── modules.php                   ← Module chrome
│   └── error.php
├── assets/
│   ├── css/
│   └── js/
└── config/
    └── params.php
```

**Template service provider:**

```php
class HubzeroTemplateServiceProvider extends TemplateServiceProvider
{
    public function boot()
    {
        // Register this template's view overrides
        $this->loadViewsFrom(__DIR__.'/../views', 'template');

        // Publish assets to public directory
        $this->publishes([
            __DIR__.'/../assets' => public_path('templates/hubzero'),
        ], 'template-assets');
    }
}
```

**Existing PHP templates continue to work** through the Document pipeline.
**New Blade templates** can be added alongside — the TemplateManager checks for
`.blade.php` first and routes to the appropriate rendering pipeline.

**View overrides for Blade:** Laravel already has view namespace overriding:

```php
// BlogServiceProvider::boot()
$this->loadViewsFrom(__DIR__.'/../resources/views', 'blog');

// Template override:
// packages/hubzero/templates/hubzero/views/vendor/blog/site/entries/show.blade.php
// automatically overrides the component's view
```

This is Laravel's native equivalent of the `html/com_blog/` override system.

**Group templates:** Supergroups (§21) have their own template packages in
`storage/groups/{gidNumber}/packages/{vendor}/templates/{name}/`. Group templates
extend `hubzero::layouts.group` — a base layout that wraps group content within
the site template, preserving site chrome while letting the group control the
inner content area.

### Design System & CSS Architecture

#### The Problem

Current HubZero CSS is a layered accumulation that's difficult for newcomers:

```
Joomla system CSS → HubZero framework CSS → template CSS →
component CSS → plugin CSS → module CSS → inline overrides
```

Each layer uses its own class naming conventions, specificity tricks, and
assumptions about the layers below. There's no shared design vocabulary.
Building a new template requires understanding the full stack history — which
selectors to override, which to avoid, and which framework CSS is load-bearing
vs. cosmetic.

#### Design Tokens via CSS Custom Properties

HubZero 3.0 templates are powered by **CSS custom properties** (design tokens)
rather than a utility framework or preprocessor. This gives template authors
theming capability with no build step, and hub operators can change branding
(colors, fonts, spacing) without touching template code.

```css
/* packages/hubzero/framework/resources/css/tokens.css */
:root {
    /* Color palette */
    --hub-color-primary: #2563eb;
    --hub-color-primary-hover: #1d4ed8;
    --hub-color-secondary: #64748b;
    --hub-color-success: #16a34a;
    --hub-color-warning: #d97706;
    --hub-color-danger: #dc2626;

    /* Surfaces */
    --hub-color-bg: #ffffff;
    --hub-color-bg-alt: #f8fafc;
    --hub-color-border: #e2e8f0;
    --hub-color-text: #1e293b;
    --hub-color-text-muted: #64748b;

    /* Typography */
    --hub-font-sans: system-ui, -apple-system, sans-serif;
    --hub-font-mono: ui-monospace, 'Cascadia Code', monospace;
    --hub-font-size-sm: 0.875rem;
    --hub-font-size-base: 1rem;
    --hub-font-size-lg: 1.125rem;
    --hub-font-size-xl: 1.25rem;

    /* Spacing scale */
    --hub-space-1: 0.25rem;
    --hub-space-2: 0.5rem;
    --hub-space-3: 0.75rem;
    --hub-space-4: 1rem;
    --hub-space-6: 1.5rem;
    --hub-space-8: 2rem;

    /* Layout */
    --hub-content-width: 72rem;
    --hub-sidebar-width: 18rem;
    --hub-radius: 0.375rem;
    --hub-shadow: 0 1px 3px rgb(0 0 0 / 0.1);
    --hub-shadow-lg: 0 10px 15px rgb(0 0 0 / 0.1);
}

/* Dark mode — template opts in with a class or media query */
[data-theme="dark"] {
    --hub-color-bg: #0f172a;
    --hub-color-bg-alt: #1e293b;
    --hub-color-border: #334155;
    --hub-color-text: #f1f5f9;
    --hub-color-text-muted: #94a3b8;
}
```

Templates override tokens, not selectors:

```css
/* A custom template only needs to redefine the tokens it wants to change */
:root {
    --hub-color-primary: #059669;     /* green instead of blue */
    --hub-font-sans: 'Inter', system-ui, sans-serif;
    --hub-content-width: 80rem;       /* wider layout */
}
```

Hub operators can set per-tenant branding without creating a full template —
a small CSS file that overrides a handful of tokens is enough to change the
entire look. This can be stored per-tenant and loaded by the template
service provider.

#### Blade Component Library

The framework ships a library of Blade components for common HubZero UI
patterns. These components use the design tokens internally, so they
automatically adopt the active template's theme. Extension developers use
them instead of writing raw HTML with hand-picked CSS classes.

```blade
{{-- Cards --}}
<x-hub-card>
    <x-slot:header>{{ $publication->title }}</x-slot:header>
    <x-hub-badge :variant="$publication->state" />
    <x-hub-author-list :authors="$publication->authors" />
    <x-slot:footer>
        <x-hub-button :href="route('publications.show', $publication)">
            View
        </x-hub-button>
    </x-slot:footer>
</x-hub-card>

{{-- Forms --}}
<x-hub-form :action="route('publications.store')">
    <x-hub-input name="title" label="Title" required />
    <x-hub-textarea name="abstract" label="Abstract" rows="4" />
    <x-hub-select name="category" label="Category" :options="$categories" />
    <x-hub-file-upload name="attachment" label="Supporting Files" multiple />
    <x-hub-submit>Publish</x-hub-submit>
</x-hub-form>

{{-- Data tables --}}
<x-hub-data-table :items="$entries" :columns="[
    'title' => 'Title',
    'author.name' => 'Author',
    'created_at' => 'Date',
    'state' => 'Status',
]" :sortable="true" :searchable="true" />

{{-- Navigation --}}
<x-hub-tabs :active="$tab">
    <x-hub-tab name="overview" label="Overview" />
    <x-hub-tab name="files" label="Files" :count="$fileCount" />
    <x-hub-tab name="citations" label="Citations" :count="$citationCount" />
</x-hub-tabs>

{{-- Alerts & feedback --}}
<x-hub-alert type="success">Your submission has been published.</x-hub-alert>
<x-hub-empty-state icon="document" message="No publications found." />
```

Component implementations live in the framework package:

```
packages/hubzero/framework/
├── resources/
│   ├── css/
│   │   ├── tokens.css                ← design tokens
│   │   └── components.css            ← component styles (uses tokens)
│   └── views/
│       └── components/
│           ├── hub-card.blade.php
│           ├── hub-button.blade.php
│           ├── hub-input.blade.php
│           ├── hub-data-table.blade.php
│           ├── hub-badge.blade.php
│           ├── hub-tabs.blade.php
│           ├── hub-alert.blade.php
│           └── ...
└── src/
    └── View/
        └── Components/
            ├── HubCard.php
            ├── HubButton.php
            ├── HubDataTable.php
            └── ...
```

**Template override of components:** Templates can override any component's
view by placing a file at `views/vendor/hubzero/components/hub-card.blade.php`.
This is Laravel's standard vendor view publishing — no custom override system
needed.

#### CSS Layer Architecture

CSS `@layer` rules replace the fragile specificity cascade. Layers define
explicit priority, so a template author never needs to count specificity or
use `!important`:

```css
/* Loaded by the framework */
@layer tokens, base, components, layout;

/* tokens.css — design tokens (custom properties) */
@layer tokens {
    :root { /* ... */ }
}

/* base.css — element defaults (typography, links, lists) */
@layer base {
    body { font-family: var(--hub-font-sans); color: var(--hub-color-text); }
    a { color: var(--hub-color-primary); }
    /* ... */
}

/* components.css — Blade component styles */
@layer components {
    .hub-card { /* ... */ }
    .hub-button { /* ... */ }
    .hub-input { /* ... */ }
}

/* Template's own CSS — highest priority, overrides anything above */
@layer layout {
    /* Template-specific layout, navigation, footer, etc. */
}
```

This means a template stylesheet at the `layout` layer always wins over
framework component styles without specificity wars. Extension CSS that
targets framework components works regardless of which template is active.

#### What a New Template Looks Like

A minimal template is a Blade layout, a CSS file, and a service provider.
No Joomla history required:

```blade
{{-- views/index.blade.php --}}
@extends('hubzero::layouts.base')

@section('head')
    <link rel="stylesheet" href="{{ asset('templates/mytheme/theme.css') }}">
@endsection

@section('header')
    <header class="site-header">
        <a href="/">{{ config('app.name') }}</a>
        <x-hub-main-menu />
        <x-notification-bell />
    </header>
@endsection

@section('body')
    <div class="site-content">
        <aside>{!! $modules->position('sidebar') !!}</aside>
        <main>@yield('content')</main>
    </div>
@endsection

@section('footer')
    <footer class="site-footer">
        {!! $modules->position('footer') !!}
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}</p>
    </footer>
@endsection
```

```css
/* assets/css/theme.css — the entire template-specific CSS */
:root {
    --hub-color-primary: #7c3aed;    /* purple brand */
    --hub-content-width: 76rem;
}

.site-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: var(--hub-space-4) var(--hub-space-6);
    border-bottom: 1px solid var(--hub-color-border);
}

.site-content {
    display: grid;
    grid-template-columns: var(--hub-sidebar-width) 1fr;
    max-width: var(--hub-content-width);
    margin: 0 auto;
    gap: var(--hub-space-6);
    padding: var(--hub-space-6);
}

.site-footer {
    padding: var(--hub-space-6);
    text-align: center;
    color: var(--hub-color-text-muted);
    border-top: 1px solid var(--hub-color-border);
}
```

That's a complete, functional template. A web developer who has never seen
HubZero can build one in an afternoon.

#### Per-Tenant Branding

Hub operators often want to change colors and logo without creating a full
template. The `TenantBrandingServiceProvider` loads a small per-tenant CSS
override from the tenant's storage:

```php
class TenantBrandingServiceProvider extends ServiceProvider
{
    public function boot(TenantManager $tenants): void
    {
        if (!$tenant = $tenants->current()) { return; }

        $brandingPath = $tenants->tenantPath() . '/branding.css';

        if (file_exists($brandingPath)) {
            // Inject after template CSS — token overrides take effect
            app('document.assets')->addStylesheet($brandingPath);
        }
    }
}
```

A tenant's `branding.css` might be as simple as:

```css
:root {
    --hub-color-primary: #b91c1c;
    --hub-font-sans: 'Roboto', system-ui, sans-serif;
}
```

This is manageable from the Filament admin panel — a branding settings page
that writes token overrides to the file, no CSS knowledge required from the
hub operator.

#### Tailwind Compatibility

The framework does not ship Tailwind by default — the design token system is
self-contained and requires no build step. However, templates and extensions
that prefer Tailwind can use it freely:

- Tailwind's `@theme` directive (v4) can reference CSS custom properties,
  so `--hub-color-primary` becomes available as `text-primary` etc.
- Tailwind's class scoping prevents conflicts with the framework CSS
- Extensions using Tailwind include their own compiled CSS via Vite

This is consistent with the frontend-agnostic philosophy (§9) — the framework
provides conventions, not mandates.

#### Legacy CSS Migration Path

During the hybrid phase, legacy component views still load their own CSS
through `$this->css()` / `Document::addStylesheet()`. This CSS renders into
the page head alongside the token-based framework CSS. The CSS `@layer`
architecture prevents conflicts — legacy styles that don't declare a layer
land in the implicit default layer, which has lower priority than the
template's `layout` layer but can still override `base` and `components`.

As components migrate to Blade views and the component library, their
legacy CSS files are gradually replaced. No big-bang CSS rewrite required.

---

## 11. Extension Override System

### Current State
- `PATH_APP` checked before `PATH_CORE` everywhere (components, plugins, modules, templates)
- `ClassLoader` searches `app/` before `core/` for autoloading
- `app/templates/{name}/html/` overrides component/plugin/module views
- `app/config/` is the only config source

### Proposed Architecture

The old `app/` → `core/` override system is replaced by two mechanisms:

1. **Shared packages** — operator-added Composer packages in `packages/{vendor}/`
   that apply to all tenants. Standard Composer: `composer require acme/component-widget`.

2. **Tenant packages** — runtime-discovered packages in
   `tenants/{hostname}/packages/{vendor}/` that apply to a single tenant.
   Installed via admin panel or filesystem.

**Override chain: group packages → tenant packages → shared packages → Laravel core**

```php
class ExtensionResolver
{
    public function __construct(
        protected TenantManager $tenants,
    ) {}

    /**
     * Find the path to an extension, checking group first, then tenant,
     * then shared packages.
     */
    public function resolve(string $type, string $vendor, string $name): ?string
    {
        $paths = array_filter([
            // Group-level override (when inside a supergroup — §21)
            $this->groupPackagePath($type, $vendor, $name),
            // Tenant-level override
            $this->tenants->tenantPath()
                ? $this->tenants->tenantPath() . "/packages/{$vendor}/{$type}/{$name}"
                : null,
            // Shared packages
            base_path("packages/{$vendor}/{$type}/{$name}"),
        ]);

        foreach ($paths as $path) {
            if (is_dir($path)) {
                return $path;
            }
        }

        return null;
    }

    /**
     * Return all paths where the extension exists (for view fallback chains).
     */
    public function resolveAll(string $type, string $vendor, string $name): array
    {
        $found = [];
        $candidates = array_filter([
            $this->groupPackagePath($type, $vendor, $name),
            $this->tenants->tenantPath()
                ? $this->tenants->tenantPath() . "/packages/{$vendor}/{$type}/{$name}"
                : null,
            base_path("packages/{$vendor}/{$type}/{$name}"),
        ]);

        foreach ($candidates as $path) {
            if (is_dir($path)) {
                $found[] = $path;
            }
        }

        return $found;
    }

    protected function groupPackagePath(string $type, string $vendor, string $name): ?string
    {
        $group = $this->tenants->currentGroup();
        if (!$group) return null;

        return $this->tenants->groupPath($group) . "/packages/{$vendor}/{$type}/{$name}";
    }
}
```

**TenantManager** (resolves tenant from hostname, optionally enters group context):

```php
class TenantManager
{
    protected ?string $tenantBasePath;
    protected ?string $currentTenant = null;
    protected ?Group $currentGroup = null;

    public function __construct()
    {
        // e.g. base_path('tenants') or /srv/tenants — null means tenancy disabled
        $this->tenantBasePath = config('hubzero.tenant_base_path');
    }

    public function resolve(Request $request): void
    {
        if (!$this->tenantBasePath) return;
        $this->currentTenant = $request->getHost();
    }

    public function tenantPath(): ?string
    {
        if (!$this->currentTenant || !$this->tenantBasePath) return null;
        $path = $this->tenantBasePath . '/' . $this->currentTenant;
        return is_dir($path) ? $path : null;
    }

    // --- Group context (supergroups — §21) ---

    public function enterGroup(Group $group): void
    {
        $this->currentGroup = $group;
    }

    public function leaveGroup(): void
    {
        $this->currentGroup = null;
    }

    public function currentGroup(): ?Group
    {
        return $this->currentGroup;
    }

    public function groupPath(Group $group): string
    {
        return storage_path("groups/{$group->gidNumber}");
    }

    /**
     * Base path for the current scope. Group path takes priority when
     * inside a group context, otherwise returns tenant path.
     */
    public function currentPath(): ?string
    {
        if ($this->currentGroup) {
            return $this->groupPath($this->currentGroup);
        }

        return $this->tenantPath();
    }
}
```

**Single-hub deployments** simply set `TENANT_BASE_PATH` in `.env` pointing
to a directory with one tenant (or omit it to disable tenancy entirely).

**For shared packages (Composer-managed):** Standard Composer autoloading via
`vendor/`. No custom autoloader needed. Laravel's package auto-discovery
registers service providers automatically.

**For tenant packages (runtime-discovered):** A `TenantPackageLoader` scans
the tenant's `packages/` directory at boot, reads each package's `composer.json`
for PSR-4 and service provider entries, and registers them. This runs after
Composer's autoloader so tenant classes can override shared package classes.

```php
class TenantPackageLoader
{
    public function boot(string $tenantPath): void
    {
        $packagesPath = $tenantPath . '/packages';
        if (!is_dir($packagesPath)) return;

        foreach ($this->discoverPackages($packagesPath) as $package) {
            // Register PSR-4 autoloading
            $autoload = $package['autoload']['psr-4'] ?? [];
            foreach ($autoload as $namespace => $path) {
                spl_autoload_register(/* PSR-4 loader for $namespace => $path */);
            }

            // Register service providers
            $providers = $package['extra']['laravel']['providers'] ?? [];
            foreach ($providers as $provider) {
                app()->register($provider);
            }
        }
    }

    protected function discoverPackages(string $basePath): array
    {
        // Scan: packages/{vendor}/{type}/{name}/composer.json
        // and:  packages/{vendor}/{type}/{group}/{name}/composer.json (plugins)
        return glob patterns...
    }
}
```

**`GroupPackageLoader`** uses the same mechanism for supergroup packages (§21) —
it scans `storage/groups/{gidNumber}/packages/` and registers PSR-4 autoloading
and service providers per request via `SupergroupMiddleware`.

**ExtensionInstaller** (admin panel package management):

```php
class ExtensionInstaller
{
    /**
     * Install a package from a zip upload.
     *
     * @param string $scope 'tenant', 'shared', or 'group'
     */
    public function install(UploadedFile $zipFile, string $scope = 'tenant'): void
    {
        $basePath = match ($scope) {
            'group'  => $this->tenants->groupPath($this->tenants->currentGroup()) . '/packages',
            'tenant' => $this->tenants->tenantPath() . '/packages',
            'shared' => base_path('packages'),
        };

        // Extract, validate composer.json, copy to $basePath/{vendor}/{type}/{name}/
        // Run composer dump-autoload if shared
        // Register service provider dynamically if tenant
    }
}
```

A single package is structurally identical regardless of where it's installed:
- `packages/acme/components/widget/` — shared, Composer-managed
- `tenants/hub1.example.com/packages/acme/components/widget/` — tenant-only, runtime-loaded
- `storage/groups/{gidNumber}/packages/acme/components/widget/` — group-only, runtime-loaded (§21)

---

## 12. Authentication & Authorization

### Current State
- `Hubzero\Auth\Guard` + `Manager`
- `User` facade → user object with profile data
- MFA via `Hubzero\Auth\Factor`
- ACL via `Hubzero\Access` (Joomla-derived)
- OAuth provider/consumer support
- Auth plugins: `authentication.*` group
- Custom token-based API authentication

### Proposed Architecture

**Use Laravel's Auth system with a custom guard and user provider:**

```php
// config/auth.php
'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'hubzero',
    ],
    'api' => [
        'driver' => 'hubzero-token',   // existing token auth — no breaking changes
        'provider' => 'hubzero',
    ],
    'api-oauth' => [
        'driver' => 'passport',        // future: OAuth2 via Laravel Passport
        'provider' => 'hubzero',
    ],
],

'providers' => [
    'hubzero' => [
        'driver' => 'hubzero',
        'model' => Hubzero\Models\User::class,
    ],
],
```

```php
class HubzeroUserProvider implements UserProvider
{
    // Queries existing #__users and #__xprofiles tables
    // Returns User model that implements Authenticatable
}
```

**API authentication:** The existing token-based system is wrapped in the
`hubzero-token` guard driver so existing API clients continue to work with
zero changes. When OAuth2 support is needed, Laravel Passport is added as a
second guard. API routes accept either auth method:

```php
Route::middleware('auth:api,api-oauth')->group(function () {
    // Works with legacy tokens OR OAuth2 tokens
});
```

**Auth plugins bridge:** The `authentication.*` plugin group continues to work.
The custom guard triggers `Event::trigger('authentication.onAuthenticate', ...)`
during login, collecting results from auth plugins (LDAP, Shibboleth, etc.).

**Rate limiting:** Laravel's built-in rate limiter protects API endpoints,
login attempts, and search queries from abuse:

```php
// app/Providers/RouteServiceProvider.php
RateLimiter::for('api', function (Request $request) {
    return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
});

RateLimiter::for('login', function (Request $request) {
    return Limit::perMinute(5)->by($request->ip());
});

RateLimiter::for('search', function (Request $request) {
    return Limit::perMinute(30)->by($request->user()?->id ?: $request->ip());
});
```

Rate limits are applied via middleware (`throttle:api`, `throttle:login`) and
return `429 Too Many Requests` with `Retry-After` headers automatically. Rate
limiting is the first layer of the broader bot protection system — see §27 for
behavioral scoring, CAPTCHA challenges, and IP blocking.

### Authorization (Policies & Gates)

**ACL migration:** The current `Hubzero\Access` (Joomla-derived) ACL system
maps to Laravel's Gate/Policy system:

```php
// Current: User::authorise('core.edit', 'com_blog')
// Laravel: Gate::allows('edit', $blog)  or  $this->authorize('edit', $blog)
```

**Policies** provide per-model authorization that integrates with Filament and
controllers:

```php
class PublicationPolicy
{
    public function view(User $user, Publication $publication): bool
    {
        return $publication->state === 'published'
            || $publication->created_by === $user->id
            || $user->can('publications.manage');
    }

    public function update(User $user, Publication $publication): bool
    {
        return $publication->created_by === $user->id
            || $user->can('publications.edit');
    }

    public function delete(User $user, Publication $publication): bool
    {
        return $user->can('publications.delete');
    }

    public function curate(User $user, Publication $publication): bool
    {
        return $user->hasRole('curator');
    }
}
```

**Group-level authorization:** Policies check group membership and role for
group-scoped actions:

```php
class GroupPolicy
{
    public function manage(User $user, Group $group): bool
    {
        return $group->isManager($user->id);
    }

    public function viewContent(User $user, Group $group): bool
    {
        return $group->access === Group::ACCESS_PUBLIC
            || $group->isMember($user->id);
    }
}
```

Filament Resources automatically use policies for authorization — if a
`PublicationPolicy` exists, Filament hides create/edit/delete buttons for
users who lack permission. No additional configuration needed.

**Migration path:** Preserve `Hubzero\Access` during transition. Policies
are added per-component as each is migrated. The `HubzeroAccessBridge` maps
legacy `User::authorise()` calls to Gate checks during the hybrid phase.

---

## 13. Session, Cache, Mail, Queue

These are straightforward migrations — direct replacements with config bridges.

| Service | Current | Laravel | Migration |
|---|---|---|---|
| **Session** | Custom manager, DB/file storage | `Illuminate\Session` | Config bridge to existing session table |
| **Cache** | Custom manager (File, APC, Memcache, etc.) | `Illuminate\Cache` | Direct driver mapping |
| **Mail** | Wraps Symfony Mailer | `Illuminate\Mail` | Direct replacement; dispatch via queue |
| **Filesystem** | Wraps League\Flysystem | `Illuminate\Filesystem` | Already same underlying library |
| **Logging** | Wraps Monolog | `Illuminate\Log` | Already same underlying library |

**Session table:** If using database sessions, point Laravel at the existing
`#__session` table (or migrate to Laravel's `sessions` table).

### Queue System

Laravel queues are adopted from the start. The immediate win is email — every
hub currently sends emails synchronously during HTTP requests. Moving email to
the queue makes pages noticeably faster.

**Queue driver:** Database driver initially (no extra infrastructure — uses a
`jobs` table). Switch to Redis later if throughput demands it.

**Queue consumers beyond email:** The queue handles notifications (§23),
search index syncing via Scout (§25), analytics recording (§22), and
broadcast event delivery via Reverb (§24). All of these run asynchronously
so user-facing requests stay fast.

**Cron plugin migration:** Current cron plugins split into two categories:

| Current Cron Task | Laravel Equivalent |
|---|---|
| Periodic tasks (digest emails, session cleanup, log pruning) | Laravel scheduler via `schedule:run` |
| Async work triggered by user actions (index content, send notifications) | Queue jobs dispatched during requests |

```php
// Scheduled task — replaces cron plugin
// ->onOneServer() ensures only one server runs the job in multi-server
// deployments (§15 Horizontal Scaling)
$schedule->job(new CleanExpiredSessions)->daily()->onOneServer();
$schedule->job(new SendDigestEmails)->dailyAt('06:00')->onOneServer();

// Async job — dispatched during a request, user doesn't wait
class PublicationsController extends Controller
{
    public function store(Request $request)
    {
        $publication = Publication::create($request->validated());

        IndexPublication::dispatch($publication);

        // Notifications via Laravel's notification system (§23)
        Notification::send($reviewers, new PublicationSubmitted($publication));

        return redirect()->route('publications.show', $publication);
    }
}
```

The cron plugin group is preserved during migration — each `onCron*` method
dispatches the equivalent Laravel job for a gradual transition.

### Per-Tenant File Storage

HubZero hubs store user-generated files (uploads, media, project files, etc.)
that must be isolated per tenant. The storage layer is **driver-agnostic** —
operators choose local disk or object storage (S3, MinIO, etc.) via environment
config, and the framework handles per-tenant path/prefix isolation transparently.

**Storage isolation strategy:** shared bucket/volume with per-tenant prefix.
This is the dominant pattern in multi-tenant Laravel (used by both
stancl/tenancy and spatie/laravel-multitenancy) because it balances isolation
with operational simplicity — one bucket to manage, backup, and monitor.

```php
// TenantStorageServiceProvider — registered by hubzero/framework

class TenantStorageServiceProvider extends ServiceProvider
{
    public function boot(TenantManager $tenants): void
    {
        if (!$tenant = $tenants->current()) {
            return;
        }

        $driver = config('filesystems.disks.tenant.driver', 'local');

        if ($driver === 'local') {
            // Local disk: tenants/{hostname}/storage/app
            config(['filesystems.disks.tenant' => [
                'driver' => 'local',
                'root'   => $tenants->currentPath() . '/storage/app',
            ]]);
        } else {
            // Object storage (S3, MinIO, etc.): shared bucket, tenant prefix
            config(["filesystems.disks.tenant.root" => "tenants/{$tenant}"]);
        }

        // Make 'tenant' the default disk
        config(['filesystems.default' => 'tenant']);
    }
}
```

**Usage is identical regardless of storage backend:**

```php
// All storage calls automatically scoped to current tenant
Storage::put('uploads/paper.pdf', $contents);
// Local:  tenants/hub1.example.com/storage/app/uploads/paper.pdf
// S3:     s3://hubzero-bucket/tenants/hub1.example.com/uploads/paper.pdf

Storage::url('uploads/paper.pdf');
// Returns tenant-scoped URL via configured disk
```

**Environment configuration:**

```env
# Local storage (default — simplest for single-server deployments)
FILESYSTEM_DISK=local

# Object storage (S3-compatible — for scaled/cloud deployments)
FILESYSTEM_DISK=s3
AWS_BUCKET=hubzero-storage
AWS_DEFAULT_REGION=us-east-1
```

**Per-tenant bucket** (strongest isolation) is also supported — operators set
`AWS_BUCKET` per tenant in tenant-specific config. The `TenantManager` merges
tenant-level config overrides before the storage provider boots.

**Group storage:** When inside a supergroup context (§21), `TenantManager::currentPath()`
returns the group path (`storage/groups/{gidNumber}`), so the same
`TenantStorageServiceProvider` automatically scopes storage to the group's
directory without additional configuration. Group uploads are isolated from
both the tenant's uploads and other groups' uploads.

**Directory layout (local driver):**

```
tenants/
├── hub1.example.com/
│   ├── packages/           ← tenant extension overrides (§11)
│   └── storage/
│       ├── app/            ← user uploads, project files
│       │   ├── uploads/
│       │   ├── projects/
│       │   └── media/
│       ├── framework/      ← cache, sessions, views
│       └── logs/           ← tenant-specific logs
└── hub2.example.com/
    └── ...
```

---

## 14. Facades, Helpers & Language

### Current State
23 facades (`App`, `Config`, `Route`, `Request`, `User`, `Lang`, `Event`, etc.)
accessed via `Hubzero\Facades\Facade` base class.

### Proposed Architecture

**Replace with Laravel facades where equivalents exist:**

| Hubzero Facade | Laravel Replacement | Notes |
|---|---|---|
| `App` | `App` | Same concept, different container |
| `Config` | `Config` | Direct replacement |
| `Request` | `Request` | Direct replacement |
| `Response` | `Response` | Direct replacement |
| `Route` | `Route` | Different API (§4) — bridge needed |
| `Event` | Custom `Event` | Hubzero event dispatcher (§6) |
| `User` | `Auth` | `Auth::user()` replaces `User::get()` |
| `Lang` | `__()` / `trans()` | Bridge: `Lang::txt()` → `__()` |
| `Log` | `Log` | Direct replacement |
| `Cache` | `Cache` | Direct replacement |
| `Session` | `Session` | Direct replacement |
| `Date` | `Carbon` | Direct replacement |
| `Filesystem` | `Storage` | Direct replacement |
| `Plugin` | Custom `Plugin` | Preserved (§6) |
| `Component` | Custom `Component` | Preserved (§5) |
| `Module` | Custom `Module` | Preserved (§7) |
| `Document` | Custom `Document` | Preserved until Blade (§9) |
| `Pathway` | Custom `Pathway` | Breadcrumbs — preserved or use package |
| `Toolbar` | Filament | Replaced by Filament admin UI |
| `Submenu` | Filament | Replaced by Filament navigation |
| `Notify` | `Notification` / `session()->flash()` | Flash messages for UI; `Notification` for user alerts (§23) |
| `Html` | Custom `Html` | Form/HTML helpers — preserved or use package |
| `Editor` | Custom `Editor` | WYSIWYG — preserved |

### Language System

**Migration phase:** A custom `.ini` translation loader reads existing Joomla-style
`.ini` language files directly. No conversion needed — existing translations work
immediately.

```php
class IniTranslationLoader implements Loader
{
    public function load($locale, $group, $namespace = null): array
    {
        $path = $this->findIniFile($locale, $group, $namespace);
        if (!$path) return [];

        return parse_ini_file($path, false, INI_SCANNER_RAW);
    }
}
```

**Per-component conversion:** As each component is packaged, its language strings
are converted from `.ini` to Laravel PHP arrays — this is part of the migration
checklist. The `Lang::txt()` bridge checks for a Laravel translation first,
falls back to the `.ini` key:

```php
class LangFacade extends Facade
{
    public static function txt(string $key, ...$args): string
    {
        // Try Laravel translation first (PHP array format)
        $translated = __($key, [], app()->getLocale());

        // Fall back to .ini key if not found
        if ($translated === $key) {
            $translated = self::loadFromIni($key);
        }

        return $args ? vsprintf($translated, $args) : $translated;
    }
}
```

The `.ini` loader is removed once all components are converted to PHP arrays.

**Language packs as Composer packages:**

```
packages/hubzero/languages/fr/
├── composer.json                     # hubzero/lang-fr
├── src/
│   └── FrLanguageServiceProvider.php
└── strings/
    ├── blog.php                      ← Laravel PHP array format
    ├── members.php
    └── ...
```

---

## 15. Octane / FrankenPHP Considerations

### Worker Mode Requirements

In Octane worker mode, the application boots once and handles many requests.
This means:

**Must be stateless between requests:**
- No static mutable state that leaks between requests
- No `global` variables persisted across requests
- Singletons must be request-scoped where appropriate

**Current Hubzero problems for worker mode:**
1. `static $loaded` in `Plugin\Loader::import()` — memoizes loaded plugins per-request
2. `Document\Base::$_buffer` is static — accumulates across requests
3. `define()` constants (`PATH_COMPONENT`, `JPATH_BASE`, etc.) — can't redefine
4. `$_GET` / `$_POST` manipulation in routing (`$request->setVar()`)
5. `session_write_close()` / `ob_end_flush()` in `Application::__destruct()`

**Solutions:**

```php
// 1. Replace static memoization with request-scoped container bindings
$this->app->scoped('hubzero.plugins.loaded', function () {
    return new PluginRegistry();
});

// 2. Document becomes request-scoped
$this->app->scoped('document', function ($app) {
    return new Hubzero\Document\Manager();
});

// 3. Replace define() with container values
$this->app->scoped('hubzero.paths', function () {
    return new PathRegistry();  // ->component, ->componentSite, etc.
});
// Bridge: component_path() helper function

// 4. Use Laravel's Request object (already immutable per-request in Octane)
// No $_GET manipulation needed with proper routing

// 5. Octane handles response sending — no manual ob/session management
```

**Octane configuration:**

```php
// config/octane.php
return [
    'server' => 'frankenphp',
    'workers' => env('OCTANE_WORKERS', 'auto'),
    'max_requests' => env('OCTANE_MAX_REQUESTS', 500),

    // Warm these services on boot (shared across requests)
    'warm' => [
        'hubzero.plugins',      // plugin registry from DB
        'hubzero.menu',         // menu tree from DB
        'hubzero.extensions',   // enabled extensions list
    ],

    // Flush these between requests
    'flush' => [
        'document',
        'hubzero.paths',
        'hubzero.plugins.loaded',
        // Group context is managed by SupergroupMiddleware (enter/leave per request)
    ],
];
```

**Warm caching benefit:** Menu items, enabled extensions, plugin registry, and
content page routes are all read from DB once at worker boot instead of every
request. Massive performance win. When DB data changes (new content page, plugin
enabled/disabled), `php artisan octane:reload` refreshes workers.

**Reverb alongside Octane:** Laravel Reverb (§24) is a separate long-running
PHP process that handles WebSocket connections. Both Octane and Reverb are
managed by the same process supervisor (systemd, Supervisor, or Docker
Compose). Reverb is optional — hubs that don't need real-time features skip
it entirely. Feature flags (§26) can gate real-time features per-tenant.

### Horizontal Scaling

The architecture is horizontally scalable by design — any number of app servers
behind a load balancer can serve any tenant request because TenantManager resolves
tenant context from the request hostname, not from local state. To run multiple
servers, ensure all shared state is externalized:

- **Redis** for session, cache, and queue drivers (`SESSION_DRIVER=redis`,
  `CACHE_STORE=redis`, `QUEUE_CONNECTION=redis`)
- **S3 / MinIO** for tenant file storage (already supported by
  `TenantStorageServiceProvider`)
- **Scheduler** — add `->onOneServer()` to scheduled commands so only one
  server executes each job (requires Redis lock)
- **Reverb** — set Redis as the pub/sub backend so broadcasts reach WebSocket
  clients regardless of which server they connect to
- Tenant packages on a shared mount (`/srv/tenants` via NFS or similar) or
  baked into the deployment artifact

No architectural changes are required — these are deployment configuration choices.

---

## 16. CLI (Muse → Artisan)

### Current State
- `core/bin/muse` — CLI entry point
- Custom command system in `Hubzero\Console\`
- Commands for migrations, user management, cron, etc.

### Proposed Architecture

**Replace with Artisan.** Artisan is designed to be extended — every package can
register its own commands via service providers. No custom CLI framework needed.

**Command mapping:**

| Current Muse | Artisan Equivalent |
|---|---|
| `muse migration run` | `php artisan migrate` (built-in) |
| `muse migration run --extension=com_blog` | `php artisan migrate --path=packages/hubzero/components/blog/database/migrations` |
| `muse user create` | `php artisan hubzero:user:create` |
| `muse cron run` | `php artisan schedule:run` (built-in) |
| `muse cache clear` | `php artisan cache:clear` (built-in) |
| `muse repository` | `php artisan hubzero:repository` |
| (new) | `php artisan hubzero:migrate-supergroup {gidNumber}` — converts legacy supergroups (§21) |
| (new) | `php artisan hubzero:routes:cache` — caches DB-driven routes (§4) |

Half the muse commands are reimplementations of things Artisan already provides
(migrations, cache, config). Those disappear entirely. Hubzero-specific commands
become `hubzero:*` commands.

**Per-package commands:** Components can register their own commands:

```php
class BlogServiceProvider extends ComponentServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                ImportBlogEntriesCommand::class,
            ]);
        }
    }
}
```

**Preserve `muse` as a wrapper** for backward compatibility:

```bash
#!/bin/bash
# bin/muse
exec php artisan "$@"
```

### Maintenance Mode

Laravel's built-in `php artisan down` provides maintenance mode with features
the current system lacks:

```bash
# Enable maintenance mode with pre-rendered view and secret bypass
php artisan down --render="errors::503" --secret="admin-bypass-token"

# Operators access the site during maintenance via:
# https://hub.example.com/admin-bypass-token

# Allow specific IPs (e.g., campus network)
php artisan down --allow=192.168.1.0/24

# Bring the hub back up
php artisan up
```

The maintenance view is pre-rendered (not compiled at request time) so it works
even if the application is in a broken state. Octane workers receive the "down"
signal and serve the maintenance response without restart.

### Automated Backups

`spatie/laravel-backup` provides scheduled, automated backups with health
monitoring:

```php
// config/backup.php
'backup' => [
    'source' => [
        'databases' => ['mysql'],
        'files' => [
            'include' => [
                storage_path('groups'),    // supergroup files
                base_path('tenants'),      // tenant files
                storage_path('app'),       // uploads
            ],
        ],
    ],
    'destination' => [
        'disks' => ['s3-backup'],  // or 'local' for on-disk backups
    ],
],

// Scheduled in Console/Kernel.php
$schedule->command('backup:run')->dailyAt('02:00');
$schedule->command('backup:clean')->dailyAt('03:00');  // retention policy
$schedule->command('backup:monitor')->dailyAt('04:00'); // health check
```

A Filament dashboard widget shows backup status — last backup time, size,
and health. Alerts via notification (§23) if a backup fails.

---

## 17. Extension Developer Experience

This section covers tooling and conventions that make it easy to develop, manage,
and distribute Hubzero extensions. These patterns are borrowed from Winter CMS,
nwidart/laravel-modules, Bagisto, and Statamic — all Laravel-based CMSes
with proven extension systems.

### Typed Base Service Providers

The `hubzero/framework` package (`packages/hubzero/framework/`) provides all
framework-level infrastructure that extensions depend on: base service providers,
plugin/event system, module manager, tenant resolution, extension management,
document pipeline, routing bridges, translation loader, auth guards, bridge
facades, analytics middleware, search configuration, design tokens, and the
Blade component library (§10). Every extension package `require`s
`hubzero/framework`.

Rather than raw `ServiceProvider` classes where every package reinvents
registration, the framework provides typed base classes with a structured contract:

```php
namespace Hubzero\Foundation;

abstract class ComponentServiceProvider extends ServiceProvider
{
    /**
     * Register permissions this component provides.
     * Used by Filament admin to build the permissions UI.
     */
    public function registerPermissions(): array { return []; }

    /**
     * Register Filament admin navigation items.
     */
    public function registerNavigation(): array { return []; }

    /**
     * Register admin settings pages.
     */
    public function registerSettings(): array { return []; }
}

abstract class PluginServiceProvider extends ServiceProvider
{
    /**
     * Register events this plugin listens to.
     */
    public function registerEvents(): array { return []; }
}

abstract class ModuleServiceProvider extends ServiceProvider
{
    // Module-specific registration hooks
}

abstract class TemplateServiceProvider extends ServiceProvider
{
    // Template-specific registration hooks
}
```

These provide introspection — the system can ask "what permissions does this
component register?" or "what events does this plugin handle?" without digging
through boot code. They're still standard Laravel service providers underneath.

### Enable/Disable Without Uninstall

A database flag per extension per tenant. Files stay on disk, but the service
provider doesn't get loaded. Useful for debugging ("disable this plugin, see if
the problem goes away") and lets tenant admins toggle extensions without
filesystem access.

```php
// #__extensions table
Schema::table('extensions', function (Blueprint $table) {
    $table->string('package_name');    // e.g. hubzero/component-blog
    $table->string('type');            // component, plugin, module, template, language
    $table->boolean('enabled')->default(true);
    $table->string('tenant')->nullable();  // null = all tenants
    $table->string('scope')->default('site');  // site, group
    $table->unsignedBigInteger('scope_id')->nullable();  // group gidNumber (§21)
    $table->json('params')->nullable();
});
```

The extension loader checks this table before registering service providers:

```php
class ExtensionManager
{
    public function getEnabled(
        string $type,
        ?string $tenant = null,
        ?int $groupId = null,
    ): Collection {
        return DB::table('extensions')
            ->where('type', $type)
            ->where('enabled', true)
            ->where(function ($q) use ($tenant) {
                $q->whereNull('tenant')
                  ->orWhere('tenant', $tenant);
            })
            ->where(function ($q) use ($groupId) {
                $q->where('scope', 'site');
                if ($groupId) {
                    $q->orWhere(function ($q) use ($groupId) {
                        $q->where('scope', 'group')
                          ->where('scope_id', $groupId);
                    });
                }
            })
            ->get();
    }
}
```

### Artisan Scaffolding Generators

Every package type has a generator that scaffolds the full directory structure:

```bash
# Generate a new component
php artisan make:component blog --vendor=hubzero
# → packages/hubzero/components/blog/
#   ├── composer.json
#   ├── src/BlogServiceProvider.php
#   ├── src/Http/Controllers/Site/BlogController.php
#   ├── src/Filament/Resources/BlogResource.php
#   ├── resources/views/site/index.blade.php
#   ├── routes/site.php
#   ├── database/factories/
#   ├── tests/Feature/
#   └── config.php

# Generate a new plugin
php artisan make:plugin content/formathtml
# → packages/hubzero/plugins/content/formathtml/
#   ├── composer.json
#   ├── src/FormatHtmlServiceProvider.php
#   └── src/FormatHtml.php

# Generate a new module
php artisan make:module menu --vendor=hubzero
# → packages/hubzero/modules/menu/
#   ├── composer.json
#   ├── src/MenuServiceProvider.php
#   ├── src/Menu.php
#   └── resources/views/default.blade.php

# Generate a new template
php artisan make:template mytheme
# → packages/hubzero/templates/mytheme/
#   ├── composer.json
#   ├── src/MyThemeTemplateServiceProvider.php
#   ├── views/index.blade.php
#   └── assets/css/ assets/js/
```

Generators ensure every extension follows the same conventions and includes
a valid `composer.json` with auto-discovery configuration, starter tests,
and factories. The `--vendor` flag defaults to `hubzero` but supports
third-party vendors.

### Package Naming Convention

All packages follow a predictable naming scheme:

| Type | Composer Name | Namespace |
|---|---|---|
| Framework | `hubzero/framework` | `Hubzero\Framework\` |
| Component | `hubzero/component-blog` | `Hubzero\Component\Blog\` |
| Plugin | `hubzero/plugin-content-formathtml` | `Hubzero\Plugin\Content\FormatHtml\` |
| Module | `hubzero/module-menu` | `Hubzero\Module\Menu\` |
| Template | `hubzero/template-hubzero` | `Hubzero\Template\Hubzero\` |
| Language | `hubzero/lang-en` | `Hubzero\Language\En\` |

The type prefix makes packages sortable and immediately identifiable.
Inter-package dependencies use standard Composer `require`:

```json
{
    "require": {
        "hubzero/component-members": "^3.0"
    }
}
```

No custom dependency resolution needed — Composer handles it natively.

---

## 18. Testing

### Strategy

Adopt Laravel's testing conventions. Per-package `tests/` directories with
factories ship with each component/plugin package. Root `tests/` for
cross-package integration tests. One root `phpunit.xml` discovers all
test directories.

**Feature tests** (HTTP requests against the full app) and **model factories**
are the standard pattern for each migrated component:

```php
class BlogTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_view_blog_index()
    {
        $entry = Entry::factory()->published()->create();

        $this->get('/blog')
            ->assertOk()
            ->assertSee($entry->title);
    }

    public function test_guest_cannot_create_entry()
    {
        $this->post('/blog', ['title' => 'Test'])
            ->assertRedirect('/login');
    }

    public function test_author_can_create_entry()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/blog', ['title' => 'My Post', 'content' => 'Hello'])
            ->assertRedirect('/blog/my-post');

        $this->assertDatabaseHas('blog_entries', ['title' => 'My Post']);
    }
}
```

**Per-package test structure:**

```
packages/hubzero/components/blog/
├── tests/
│   ├── Feature/
│   │   ├── ViewEntriesTest.php
│   │   └── CreateEntryTest.php
│   └── Unit/
│       └── EntryModelTest.php
├── database/
│   └── factories/
│       └── EntryFactory.php
```

The `make:component` generator scaffolds starter tests and factories
for every new component.

---

## 19. Asset Pipeline

### Strategy

Hybrid approach:

**Templates** use Vite (Laravel's built-in integration) for the site shell —
layout CSS/JS with minification, content hashing, and hot module replacement
during development. Template assets go through a build step for production.

**Component/plugin/module assets** remain direct files with no build step.
Each extension is self-contained — CSS and JS files can be edited in place
and results seen immediately on reload. Components publish assets to `public/`
via their service provider:

```php
$this->publishes([
    __DIR__.'/../resources/assets' => public_path('components/blog'),
], 'component-assets');
```

Assets are loaded per-page via Blade stacks:

```blade
{{-- Template uses Vite for the site shell --}}
@vite(['resources/css/app.css', 'resources/js/app.js'])

{{-- Component assets added individually --}}
@stack('styles')
@stack('scripts')
```

HTTP/2 on FrankenPHP makes individual file loading performant — no need
to bundle 61 components' assets into one file.

**Framework CSS** — the design token system (§10) ships as part of the
`hubzero/framework` package and is loaded by the base layout. `tokens.css`,
`base.css`, and `components.css` are loaded via CSS `@layer` rules so they
integrate cleanly with both template CSS and legacy component CSS. Template
authors override design tokens (custom properties), not selectors.

---

## 20. Migration Phases

### Phase 0: Preparation (Foundation)
- [ ] Set up Laravel skeleton alongside existing code
- [ ] Unified Composer config (single root `composer.json`)
- [ ] Set up `packages/` directory structure with path repositories
- [ ] Create `hubzero/framework` package with base service providers, facades, and bridges
- [ ] Laravel Application boots, delegates to Hubzero for actual work
- [ ] All existing functionality works unchanged through legacy bridge
- [ ] FrankenPHP + Octane serving the legacy bridge
- [ ] `.ini` translation loader in place

### Phase 1: Infrastructure Swap
- [ ] Replace Pimple container with Laravel IoC
- [ ] Replace session/cache/mail/filesystem with Illuminate equivalents
- [ ] Replace error handling with Laravel exception handler
- [ ] Bridge facades: Hubzero facades → Laravel facades where possible
- [ ] `Relational` base class shimmed to extend Eloquent
- [ ] Laravel queue system in place (database driver)
- [ ] Email dispatched via queue
- [ ] Laravel notification system in place (mail + database channels) (§23)
- [ ] User notification preferences table and account settings UI
- [ ] Laravel Pennant installed for migration feature flags (§26)
- [ ] `spatie/laravel-backup` configured with scheduled backups (§16)
- [ ] All existing code still works through shims

### Phase 2: Routing & Request Lifecycle
- [ ] Laravel router handles all requests
- [ ] Legacy catch-all route dispatches un-migrated components
- [ ] Content page routes registered from database
- [ ] Menu alias routes registered from database
- [ ] Route precedence: content → menu → component → legacy catch-all
- [ ] `hubzero:routes:cache` command with model observers for auto-rebuild
- [ ] `Route::url()` bridge translates old-style URLs
- [ ] Admin routes via Filament
- [ ] API routes via `/api` prefix group with `hubzero-token` guard
- [ ] Rate limiting middleware on API, login, and search routes (§12)
- [ ] `BotScoreMiddleware` and `BlockedIpMiddleware` in global stack (§27)
- [ ] `spatie/laravel-honeypot` on registration and content submission forms (§27)
- [ ] Cloudflare Turnstile on registration, password reset, and contact forms (§27)
- [ ] Friendly bot allowlist (Googlebot, CrossRef, DOAJ, etc.) (§27)
- [ ] Filament admin page for IP blocklist management (§27)
- [ ] Tenant resolution middleware in place
- [ ] Laravel Pulse installed with Filament integration
- [ ] `AnalyticsMiddleware` + `RecordAnalyticsHit` queue job (§22)
- [ ] `analytics_hits` and `analytics_monthly` tables
- [ ] GeoIP via `stevebauman/location` + MaxMind GeoLite2
- [ ] Filament analytics report pages (overview, geographic, component detail)
- [ ] Monthly rollup job and detail row retention policy

### Phase 3: Plugin & Event System
- [ ] `HubzeroEventDispatcher` wrapping Laravel events
- [ ] Plugin loading via `PluginManager` (DB-backed, ordered)
- [ ] All 37 plugin groups working through new dispatcher
- [ ] `Event::trigger()` and `Plugin::import()` facades preserved
- [ ] Cron plugins migrated to Laravel scheduler + queue jobs
- [ ] Octane-safe (request-scoped plugin state)

### Phase 4: Extension Packaging (Iterative)
- [ ] Artisan scaffolding generators (`make:component`, `make:plugin`, etc.)
- [ ] First component fully packaged (pick a simple one: `com_blog`?)
- [ ] Baseline database migrations per component
- [ ] `hubzero:migrate:baseline` command for existing hubs
- [ ] Migration template/checklist for packaging remaining extensions
- [ ] Extensions migrated one at a time, legacy catch-all handles the rest
- [ ] Language strings converted from `.ini` to PHP arrays per component
- [ ] Per-package tests and factories scaffolded
- [ ] `Searchable` trait added to models as components are migrated (§25)
- [ ] Policies added per-component for Gate/authorization (§12)
- [ ] Notifications converted from ad-hoc email to Notification classes (§23)
- [ ] Feature flags gate legacy vs. migrated component routes (§26)
- [ ] (This phase runs in parallel with other phases — ~61 components,
       ~200 plugins, ~100 modules)

### Phase 5: Multi-Tenancy & Override System
- [ ] TenantManager resolving tenants from request hostname
- [ ] TenantPackageLoader discovering and loading tenant packages
- [ ] ExtensionResolver with three-layer path resolution (group → tenant → shared)
- [ ] ExtensionInstaller for admin panel uploads (tenant + shared scope)
- [ ] Enable/disable per extension per tenant via `#__extensions`
- [ ] TenantStorageServiceProvider with local and S3 drivers
- [ ] Per-tenant `storage/` directory layout (app, framework, logs)
- [ ] Supergroup nested tenant support (TenantManager group context)
- [ ] SupergroupMiddleware and GroupPackageLoader
- [ ] Group-scoped Filament admin panel
- [ ] Migrate `#__xgroups_modules` to `#__modules` with group scope
- [ ] `hubzero:migrate-supergroup` Artisan command
- [ ] Group template conversion (legacy PHP → Blade layout packages)
- [ ] Per-tenant search index prefixes via Scout/Meilisearch (§25)

### Phase 6: View & Template System
- [ ] Blade available as optional view engine
- [ ] Document system preserved for legacy templates
- [ ] New Blade layouts can render modules via `$modules->position()`
- [ ] Vite integration for template assets
- [ ] Template override system working for both Blade and PHP views
- [ ] Design tokens (`tokens.css`) and CSS layer architecture in place (§10)
- [ ] Blade component library (`x-hub-card`, `x-hub-form`, etc.) (§10)
- [ ] Default HubZero template built on design tokens and component library
- [ ] Per-tenant branding via `TenantBrandingServiceProvider` (§10)
- [ ] Filament admin page for tenant branding settings (colors, fonts, logo)
- [ ] Laravel Reverb (WebSocket server) deployed alongside Octane (§24)
- [ ] Laravel Echo integrated in base template layout
- [ ] Real-time notification bell in site template
- [ ] Meilisearch deployed and `scout:import` run for all searchable models (§25)

### Phase 7: Cleanup
- [ ] Remove `core/libraries/Hubzero/` classes replaced by Laravel
- [ ] Remove shim/bridge code for fully-migrated extensions
- [ ] Remove legacy routing catch-all (when all components migrated)
- [ ] Remove `.ini` translation loader (when all strings converted)
- [ ] Audit for Octane safety (no static state leaks)
- [ ] Performance benchmarking vs. old system
- [ ] Route health dashboard in Filament admin
- [ ] Remove migration feature flags (all components migrated) (§26)
- [ ] Remove legacy Solr integration (Scout/Meilisearch handles all search)
- [ ] Remove `Hubzero\Access` ACL (all components using Policies)
- [ ] Remove legacy Joomla/HubZero system CSS (all components using design tokens)

---

## 21. Supergroups (Nested Tenants)

### Current State

HubZero "supergroups" (Type 3 groups) are mini-tenants within a hub. A supergroup
gets its own custom template, pages, components, modules, CSS/JS, optional isolated
database, and filesystem tree at `/site/groups/{gidNumber}/`. The supergroup system
plugin intercepts requests under `/groups/{cn}/`, loads the group's custom template
(a mini-Document pipeline with `<group:include>` tags), and executes custom PHP
via `eval()`.

**Current capabilities:**
- Custom template (`index.php`, header/footer, CSS/JS)
- Custom PHP pages executed via `eval()` — no sandboxing
- Custom components loaded via `include` — full framework access
- Group-scoped modules via `#__xgroups_modules`
- Optional isolated database (`sg_{groupname}`)
- Group-specific migrations, macros, uploads, language files
- GitLab integration for version-controlled group code

**Current problems:**
- `eval()` for PHP execution — largest security risk in the codebase
- No resource limits (CPU, memory) on custom code
- Custom components have full framework access with no isolation
- Parallel rendering pipeline (`Template` extends `Document`) duplicates core logic
- Database credentials stored in writable group directory

### Proposed Architecture: Supergroups as Nested Tenants

Supergroups become a lightweight tenant layer *within* the existing tenant system.
The `TenantManager` gains a second resolution level — the **group context** — and
the override chain extends naturally:

```
group packages → tenant packages → shared packages → Laravel core
```

This eliminates `eval()`, the parallel template pipeline, and the custom component
loader. Supergroups become architecturally identical to tenants, just scoped to a
URL prefix (`/groups/{cn}/`) instead of a hostname.

#### Group Context Resolution

The `TenantManager` (§11) gains `enterGroup()`/`leaveGroup()`/`currentGroup()`
methods. When inside a group context, `currentPath()` returns the group path
(`storage/groups/{gidNumber}`) instead of the tenant path. This means all
existing infrastructure that uses `currentPath()` — storage, extension
resolution, package loading — automatically scopes to the group.

#### Middleware

```php
// Applied to all routes under /groups/{cn}/
class SupergroupMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $group = Group::where('cn', $request->route('cn'))
            ->where('type', Group::TYPE_SUPER)
            ->firstOrFail();

        app(TenantManager::class)->enterGroup($group);

        // Load group packages (same mechanism as tenant packages)
        app(GroupPackageLoader::class)->boot($group);

        $response = $next($request);

        app(TenantManager::class)->leaveGroup();

        return $response;
    }
}
```

#### Override Chain with Groups

The `ExtensionResolver` (§11) checks group → tenant → shared. When inside a
group context, the resolver prepends the group's package path to the lookup
chain automatically. No code changes needed in extensions — the three-layer
override is transparent.

#### Group Filesystem Layout

```
storage/groups/{gidNumber}/
├── packages/                    ← Group-scoped extension overrides
│   └── {vendor}/
│       ├── components/
│       │   └── {name}/          ← Group-specific component (proper Laravel package)
│       ├── plugins/
│       ├── modules/
│       └── templates/
│           └── {name}/          ← Group's custom template (Blade layout)
├── storage/
│   ├── app/                     ← Group uploads, project files
│   │   ├── uploads/
│   │   └── media/
│   ├── framework/               ← Group-level cache
│   └── logs/                    ← Group-specific logs
├── config/
│   └── database.php             ← Optional: group-specific DB connection
└── migrations/                  ← Group-specific database migrations
```

#### Group Templates

Instead of the legacy `<group:include>` tag parser, group templates are standard
Blade layouts. The group's template package registers itself like any template:

```php
// storage/groups/123/packages/hubzero/templates/research-lab/src/ResearchLabServiceProvider.php

class ResearchLabServiceProvider extends TemplateServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../views', 'group-template');
    }
}
```

```blade
{{-- storage/groups/123/packages/hubzero/templates/research-lab/views/layout.blade.php --}}
@extends('hubzero::layouts.group')

<head>
    @vite(['resources/css/app.css'])  {{-- or plain CSS link --}}
    @stack('styles')
</head>

@section('group-content')
    {!! $modules->position('group-header') !!}

    @yield('content')

    {!! $modules->position('group-footer') !!}
@endsection
```

The `hubzero::layouts.group` base layout wraps group content within the site
template, preserving the site header/footer/navigation while letting the group
control the inner content area. This matches the current behavior where
supergroups render inside the main site chrome.

#### Group Pages

Current supergroup pages (PHP files with `eval()`) are replaced with proper
Blade views or Livewire/Inertia components within the group's template package.
Static content pages use the same `ContentPage` model that powers site pages:

```php
// Group pages are ContentPage records scoped to the group
ContentPage::where('scope', 'group')
    ->where('scope_id', $group->gidNumber)
    ->where('alias', $pageAlias)
    ->firstOrFail();
```

Group managers edit pages through a Filament panel scoped to their group (see
Group Admin Panel below).

#### Group Modules

Group modules use the existing Module system with a scope filter:

```php
// ModuleManager already loads modules by position — add group scope
$modules = ModuleManager::position('group-header')
    ->where('scope', 'group')
    ->where('scope_id', $group->gidNumber)
    ->get();
```

The `#__xgroups_modules` and `#__xgroups_modules_menu` tables migrate to the
standard `#__modules` table with `scope = 'group'` and `scope_id = {gidNumber}`
columns, unifying the module system.

#### Group Database Isolation

Groups that need their own database tables use a dynamic database connection:

```php
// GroupDatabaseServiceProvider — registered by GroupPackageLoader when
// a group has config/database.php

class GroupDatabaseServiceProvider extends ServiceProvider
{
    public function boot(TenantManager $tenants): void
    {
        $group = $tenants->currentGroup();
        if (!$group) return;

        $configPath = $tenants->groupPath($group) . '/config/database.php';
        if (!file_exists($configPath)) return;

        $config = require $configPath;

        // Register a dynamic connection for this group
        config(["database.connections.group" => array_merge(
            config('database.connections.mysql'),
            $config,
        )]);
    }
}

// Group models that use the isolated database
class GroupModel extends Model
{
    protected $connection = 'group';
}
```

#### Group Admin Panel

Group managers get a **scoped Filament panel** for managing their group. This
replaces the current group management interface in com_groups:

```php
// GroupPanelProvider — registered when group context is active

class GroupPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('group-admin')
            ->path('groups/{cn}/manage')
            ->resources([
                GroupPageResource::class,      // Edit pages
                GroupModuleResource::class,     // Manage modules
                GroupMemberResource::class,     // Manage members
                GroupSettingsResource::class,   // Group settings
            ])
            ->middleware([SupergroupMiddleware::class]);
    }
}
```

Group managers can:
- Edit pages (rich text, Blade, or markdown)
- Manage group modules and their positions
- Upload assets (CSS, JS, images)
- Manage members, roles, and permissions
- Configure group settings and template parameters
- Install group-scoped extension packages (if permitted by hub admin)

What group managers **cannot** do (without hub admin permission):
- Execute arbitrary PHP code (no `eval()`, no raw PHP pages)
- Access the main site's database directly
- Override site-level middleware or auth
- Install packages that register arbitrary service providers

#### Security Model

The security improvements over the current supergroup system:

| Concern | Current (2.x) | Proposed (3.0) |
|---|---|---|
| PHP execution | `eval()` — unrestricted | Blade templates only — no arbitrary PHP |
| Component code | `include` — full access | Proper packages — hub admin must approve |
| Database access | Shared credentials in file | Scoped connection via config — no raw creds |
| Resource limits | None | Standard PHP/FPM limits apply per-request |
| Asset loading | Custom tag parser | Standard Blade/Vite — same as site |
| Template engine | Parallel Document pipeline | Standard Blade — same engine as site |
| Package install | Filesystem copy | ExtensionInstaller with scope validation |

**Privilege escalation path:** Hub admins can grant individual groups the ability
to install custom packages with service providers. This is the controlled
equivalent of the current "supergroup" designation — but the code must be a
proper Laravel package, not raw PHP. The hub admin reviews and approves the
package before it runs.

#### Migration from Legacy Supergroups

```php
// Artisan command to migrate a legacy supergroup to nested tenant structure
// php artisan hubzero:migrate-supergroup {gidNumber}

class MigrateSupergroup extends Command
{
    protected $signature = 'hubzero:migrate-supergroup {gidNumber}';

    public function handle(): void
    {
        $group = Group::findOrFail($this->argument('gidNumber'));

        // 1. Create storage/groups/{gidNumber}/ directory structure
        $this->createDirectoryStructure($group);

        // 2. Convert template to Blade layout package
        $this->migrateTemplate($group);

        // 3. Convert PHP pages to ContentPage records
        $this->migratePages($group);

        // 4. Migrate #__xgroups_modules to #__modules with group scope
        $this->migrateModules($group);

        // 5. Move uploads to storage/groups/{gidNumber}/storage/app/
        $this->migrateUploads($group);

        // 6. Convert custom components to package stubs (manual review needed)
        $this->stubComponents($group);

        // 7. Migrate database config if isolated DB exists
        $this->migrateDatabaseConfig($group);

        $this->info("Supergroup {$group->cn} migrated. Review:");
        $this->info("  - Template: storage/groups/{$group->gidNumber}/packages/");
        $this->info("  - Component stubs need manual conversion to Laravel packages");
    }
}
```

---

## 22. Metrics & Usage Analytics

### Current State

HubZero has no unified metrics layer. Usage data is gathered through:
- Offline Apache log processing scripts
- Custom per-component hit counters scattered across the codebase
- Monthly batch jobs that parse logs into usage tables
- Manual SQL queries for ad-hoc reporting
- No real-time visibility into hub usage

This produces approximate data, runs on delay, and can't answer dimensional
questions like "downloads by country for com_publications last quarter."

### Proposed Architecture: Two-Layer Metrics

Two complementary layers, each purpose-built for its role:

| Layer | Tool | Purpose |
|---|---|---|
| **Operational monitoring** | Laravel Pulse | Real-time: slow queries, queue health, cache stats, server load |
| **Usage analytics** | Custom `AnalyticsHit` model | Reports: hits by component/region/IP/time, downloads, unique visitors |

#### Layer 1: Laravel Pulse (Operational)

Laravel Pulse is Laravel's official, free, self-hosted monitoring tool. It
provides real-time dashboards for:
- Server CPU, memory, disk usage
- Slowest endpoints, queries, jobs, outgoing requests
- Cache hit/miss rates
- Queue throughput (pending, processed, failed)
- Most active users

Pulse pre-aggregates data into time buckets for efficient display. A Filament
integration (`ralphjsmit/laravel-pulse`) embeds Pulse cards directly in the
admin dashboard.

Custom Pulse recorders can track HubZero-specific operational metrics:

```php
// Custom recorder — tracks component rendering time
class ComponentRenderRecorder
{
    public function record(Request $request, Response $response): void
    {
        $component = $request->route('component');
        if (!$component) return;

        Pulse::record('component_render', $component)
            ->avg($this->elapsed($request));
    }
}
```

**Pulse is not sufficient for usage reports** because its pre-aggregation
discards the dimensions (IP, region, user) needed for the reporting layer.

#### Layer 2: Usage Analytics (Reporting)

A fact table records every meaningful request with all dimensions needed for
slicing. GeoIP enrichment and the DB insert happen in a queue job — zero
latency added to requests.

**Schema:**

```php
Schema::create('analytics_hits', function (Blueprint $table) {
    $table->id();
    $table->timestamp('hit_at')->index();
    $table->string('component', 50)->index();      // com_blog, com_publications
    $table->string('action', 50);                   // view, download, submit, search
    $table->string('url', 500);
    $table->string('ip_hash', 64);                  // SHA-256 of IP (privacy-safe)
    $table->string('ip_raw', 45)->nullable();       // optional — configurable retention
    $table->string('country_code', 2)->nullable();
    $table->string('region', 100)->nullable();
    $table->string('city', 100)->nullable();
    $table->unsignedBigInteger('user_id')->nullable();
    $table->string('session_hash', 64)->nullable(); // unique visitor counting
    $table->string('device_type', 20)->nullable();  // desktop, mobile, tablet, bot
    $table->string('referer_domain', 255)->nullable();
    $table->unsignedSmallInteger('response_ms')->nullable();
    $table->string('tenant', 255)->nullable();
    $table->unsignedBigInteger('group_id')->nullable(); // supergroup context (§21)
    $table->unsignedInteger('file_size')->nullable();   // for download tracking
    $table->unsignedTinyInteger('bot_score')->default(0); // from BotScoreMiddleware (§27)

    // Composite indexes for common report queries
    $table->index(['component', 'hit_at']);
    $table->index(['country_code', 'hit_at']);
    $table->index(['tenant', 'hit_at']);
    $table->index(['group_id', 'hit_at']);
});
```

**Recording middleware:**

```php
class AnalyticsMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($this->shouldSkip($request)) {
            return $response;
        }

        // Dispatch to queue — GeoIP + DB insert happen off the request path
        RecordAnalyticsHit::dispatch(
            component: $request->route('component') ?? $this->inferComponent($request),
            action: $this->inferAction($request, $response),
            url: $request->path(),
            ip: $request->ip(),
            userAgent: $request->userAgent(),
            userId: $request->user()?->id,
            sessionId: $request->session()->getId(),
            referer: $request->header('referer'),
            responseMs: $this->elapsed($request),
            tenant: app(TenantManager::class)->current(),
            groupId: app(TenantManager::class)->currentGroup()?->gidNumber,
            botScore: $request->attributes->get('bot_score', 0),  // from BotScoreMiddleware (§27)
        );

        return $response;
    }

    protected function shouldSkip(Request $request): bool
    {
        // Skip static assets, admin pages, health checks
        // Note: bot traffic is NOT skipped — bot_score is recorded for
        // abuse analysis and defense layer reporting (§27)
        return $request->is('admin/*', '_health', 'livewire/*');
    }
}
```

**Queue job with GeoIP enrichment:**

```php
class RecordAnalyticsHit implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function handle(): void
    {
        // GeoIP lookup via local MaxMind DB — no external API calls
        $location = Location::get($this->ip);

        AnalyticsHit::create([
            'hit_at'         => now(),
            'component'      => $this->component,
            'action'         => $this->action,
            'url'            => $this->url,
            'ip_hash'        => hash('sha256', $this->ip),
            'ip_raw'         => config('hubzero.analytics.store_raw_ip') ? $this->ip : null,
            'country_code'   => $location?->countryCode,
            'region'         => $location?->regionName,
            'city'           => $location?->cityName,
            'user_id'        => $this->userId,
            'session_hash'   => hash('sha256', $this->sessionId),
            'device_type'    => $this->parseDeviceType($this->userAgent),
            'referer_domain' => $this->extractDomain($this->referer),
            'response_ms'    => $this->responseMs,
            'tenant'         => $this->tenant,
            'group_id'       => $this->groupId,
            'bot_score'      => $this->botScore,
        ]);
    }
}
```

**GeoIP:** `stevebauman/location` with MaxMind's free GeoLite2 database
installed locally. The `.mmdb` file ships with the hub — no external API
calls, no rate limits, works offline. Updated monthly via a scheduled
`geoip:update` command.

#### Report Queries

Standard Eloquent — no special query engine needed at HubZero's scale:

```php
// Page hits by component this month
AnalyticsHit::query()
    ->where('hit_at', '>=', now()->startOfMonth())
    ->groupBy('component')
    ->selectRaw('component, COUNT(*) as hits, COUNT(DISTINCT session_hash) as unique_visitors')
    ->orderByDesc('hits')
    ->get();

// Geographic breakdown for publication downloads
AnalyticsHit::query()
    ->where('component', 'com_publications')
    ->where('action', 'download')
    ->whereBetween('hit_at', [$start, $end])
    ->groupBy('country_code')
    ->selectRaw('country_code, COUNT(*) as downloads')
    ->orderByDesc('downloads')
    ->get();

// Top content by region
AnalyticsHit::query()
    ->where('country_code', 'US')
    ->where('region', 'California')
    ->whereBetween('hit_at', [$start, $end])
    ->groupBy('component', 'url')
    ->selectRaw('component, url, COUNT(*) as hits')
    ->orderByDesc('hits')
    ->limit(50)
    ->get();

// Supergroup usage
AnalyticsHit::query()
    ->whereNotNull('group_id')
    ->whereBetween('hit_at', [$start, $end])
    ->groupBy('group_id', 'component')
    ->selectRaw('group_id, component, COUNT(*) as hits')
    ->get();
```

#### Data Retention

Detail rows are valuable for ad-hoc investigation but grow quickly. A
monthly rollup job compresses old data into a summary table:

```php
// Scheduled: first of each month at 3 AM
$schedule->job(new RollupAnalytics)->monthlyOn(1, '03:00');
```

```php
Schema::create('analytics_monthly', function (Blueprint $table) {
    $table->date('month');
    $table->string('component', 50);
    $table->string('action', 50);
    $table->string('country_code', 2)->nullable();
    $table->string('region', 100)->nullable();
    $table->string('tenant', 255)->nullable();
    $table->unsignedBigInteger('group_id')->nullable();
    $table->unsignedBigInteger('hits');
    $table->unsignedBigInteger('unique_visitors');
    $table->unsignedBigInteger('authenticated_users');

    $table->primary(['month', 'component', 'action', 'country_code', 'tenant']);
});
```

**Retention policy:**
- Detail rows (`analytics_hits`): 90 days — then pruned after rollup
- Monthly summaries (`analytics_monthly`): kept indefinitely
- Configurable via `config('hubzero.analytics.detail_retention_days')`

#### Admin Dashboard

Filament report pages provide the operator-facing dashboard:

- **Overview:** total hits, unique visitors, top components (current month)
- **Geographic:** world map or table by country/region, filterable by component
- **Component detail:** per-component hits over time, top URLs, download counts
- **Supergroup usage:** per-group breakdown for hub admins
- **Export:** CSV export for all report views (for external reporting tools)

Reports support date range filtering, component filtering, and tenant scoping.
Group managers see only their group's analytics in the group admin panel (§21).

#### Privacy Considerations

- **IP hashing** enabled by default — `ip_raw` only stored when explicitly
  configured (`ANALYTICS_STORE_RAW_IP=true`)
- **Bot tracking** — bot traffic recorded with `bot_score` for abuse analysis (§27);
  reports can filter by score to show only human traffic
- **Data minimization** — user agent stored as category (desktop/mobile/tablet),
  not raw string
- **Retention limits** — detail data auto-pruned; only aggregates kept long-term
- **Tenant isolation** — analytics queries are always scoped to the current tenant

---

## 23. Notifications

### Current State

HubZero sends notifications through ad-hoc email calls scattered across
components — `Hubzero\Mail\Message` or PHP's `mail()` called directly in
controllers, plugins, and model callbacks. There is no unified notification
system, no in-app notification center, no multi-channel delivery, and no
user preference for how they receive notifications.

Common notification triggers:
- Group invite/join/approve
- Publication status change (submitted, approved, published)
- Forum reply / comment
- Support ticket update
- Project activity (file upload, todo change)
- Tool session status change
- Member profile message

### Proposed Architecture

**Laravel's notification system** provides a single `Notification` class that
delivers to multiple channels — email, database (in-app), broadcast (real-time
via §24), SMS, Slack, etc. — from one place.

**Notification class pattern:**

```php
namespace Hubzero\Component\Publications\Notifications;

class PublicationStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Publication $publication,
        public string $oldStatus,
        public string $newStatus,
    ) {}

    /**
     * Channels are determined per-user — respects user preferences.
     */
    public function via(object $notifiable): array
    {
        return $notifiable->notificationPreferences('publication.status_changed');
        // e.g. ['mail', 'database', 'broadcast']
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Publication \"{$this->publication->title}\" {$this->newStatus}")
            ->line("Your publication status changed from {$this->oldStatus} to {$this->newStatus}.")
            ->action('View Publication', route('publications.show', $this->publication));
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'publication_id' => $this->publication->id,
            'title' => $this->publication->title,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toDatabase($notifiable));
    }
}
```

**Sending notifications:**

```php
// From a controller, observer, or event listener
$publication->author->notify(new PublicationStatusChanged(
    $publication, 'draft', 'submitted'
));

// Notify multiple users
Notification::send($reviewers, new PublicationStatusChanged(
    $publication, 'submitted', 'under_review'
));
```

**In-app notification center** via the database channel:

```php
// User's unread notifications (Blade or API)
$notifications = auth()->user()->unreadNotifications;

// Mark as read
$notification->markAsRead();

// API endpoint for notification bell
Route::get('/api/notifications', function (Request $request) {
    return $request->user()->unreadNotifications()->limit(20)->get();
});
```

**User notification preferences:**

```php
// #__user_notification_preferences table
Schema::create('user_notification_preferences', function (Blueprint $table) {
    $table->unsignedBigInteger('user_id');
    $table->string('notification_type');           // publication.status_changed
    $table->json('channels');                      // ["mail", "database"]
    $table->primary(['user_id', 'notification_type']);
});

// User model
class User extends Authenticatable
{
    public function notificationPreferences(string $type): array
    {
        $pref = $this->preferences()->where('notification_type', $type)->first();

        // Default: email + database for everything
        return $pref?->channels ?? ['mail', 'database'];
    }
}
```

Users configure their preferences in account settings — a single Filament page
that lists all notification types with channel toggles. This replaces the
current fragmented email preference system.

**All notifications are queued** (`ShouldQueue`) so they never block the HTTP
request. The queue worker handles email rendering, database inserts, and
broadcast delivery asynchronously.

**Supergroup awareness:** Group notifications are scoped — group managers can
configure which notification types are active for their group, and group-specific
notifications only go to group members.

---

## 24. Broadcasting & Real-Time

### Current State

HubZero has no real-time communication layer. All UI updates require full page
reloads or polling via jQuery AJAX. Tool session status is polled. Notifications
arrive only as email.

### Proposed Architecture

**Laravel Reverb** is Laravel's first-party, self-hosted WebSocket server.
It runs as a separate process alongside Octane and integrates natively with
Laravel's broadcasting system. No third-party service (Pusher, Ably) needed.

**Use cases for HubZero:**

| Feature | Current | With Broadcasting |
|---|---|---|
| Notification bell | None (email only) | Real-time badge count + dropdown |
| Tool session status | jQuery polling every 5s | WebSocket push on state change |
| Project activity feed | Page reload | Live activity stream |
| Group announcements | Email blast | In-app toast + feed |
| Support ticket updates | Email + page reload | Live status updates |
| Collaborative editing | Not supported | Presence channels for awareness |

**Broadcasting setup:**

```php
// config/broadcasting.php
'default' => env('BROADCAST_CONNECTION', 'reverb'),

'connections' => [
    'reverb' => [
        'driver' => 'reverb',
        'key' => env('REVERB_APP_KEY'),
        'secret' => env('REVERB_APP_SECRET'),
        'app_id' => env('REVERB_APP_ID'),
        'options' => [
            'host' => env('REVERB_HOST', '0.0.0.0'),
            'port' => env('REVERB_PORT', 8080),
            'scheme' => env('REVERB_SCHEME', 'https'),
        ],
    ],
],
```

**Broadcasting events:**

```php
// Tool session status change — pushed to the user's private channel
class ToolSessionUpdated implements ShouldBroadcast
{
    public function __construct(
        public ToolSession $session,
    ) {}

    public function broadcastOn(): Channel
    {
        return new PrivateChannel("user.{$this->session->user_id}");
    }

    public function broadcastAs(): string
    {
        return 'tool.session.updated';
    }
}

// Project activity — pushed to the project's presence channel
class ProjectActivityRecorded implements ShouldBroadcast
{
    public function __construct(
        public Project $project,
        public string $activity,
        public User $actor,
    ) {}

    public function broadcastOn(): Channel
    {
        return new PresenceChannel("project.{$this->project->id}");
    }
}
```

**Frontend — Laravel Echo (JavaScript client):**

```js
// Notification bell — listens on authenticated user's private channel
Echo.private(`user.${userId}`)
    .notification((notification) => {
        updateNotificationBell(notification);
    });

// Tool session status
Echo.private(`user.${userId}`)
    .listen('.tool.session.updated', (e) => {
        updateSessionStatus(e.session);
    });

// Project presence — see who's online
Echo.join(`project.${projectId}`)
    .here((users) => { showOnlineUsers(users); })
    .joining((user) => { userJoined(user); })
    .leaving((user) => { userLeft(user); });
```

Echo is framework-agnostic JavaScript — works with Blade, Livewire, Inertia,
HTMX, or any frontend approach (§9). A single `<script>` tag in the template
base layout initializes Echo with the authenticated user's token.

**Reverb runs alongside Octane** — both are long-running PHP processes managed
by the same process supervisor (systemd, Supervisor, or Docker). Reverb handles
WebSocket connections; Octane handles HTTP requests.

**Optional:** Broadcasting is not required. Hubs that don't need real-time
features simply don't start the Reverb process. The notification system falls
back to database + email channels. Broadcasting can be enabled per-deployment
via `BROADCAST_CONNECTION=reverb` in `.env`.

---

## 25. Search

### Current State

HubZero uses a custom Solr integration for full-text search. Components
implement `onContentSearch` plugin events, and a Solr indexing system runs
alongside the hub. The search UI is powered by `com_search` which queries
Solr directly. Configuration is complex, Solr requires its own JVM process,
and the integration is tightly coupled.

### Proposed Architecture

**Laravel Scout** provides a clean, model-level search abstraction with
swappable backends.

**Recommended backend: Meilisearch** — self-hosted, Rust-based, fast, built
for CMS/content search. Lightweight alternative to Solr with no JVM dependency.
Database driver available for simple deployments that don't need full-text
capabilities.

```php
// Any Eloquent model becomes searchable by adding the trait
class Publication extends Model
{
    use Searchable;

    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'abstract' => $this->abstract,
            'authors' => $this->authors->pluck('name')->join(', '),
            'tags' => $this->tags->pluck('tag')->toArray(),
            'type' => $this->type,
            'published_at' => $this->published_at,
            'component' => 'com_publications',
        ];
    }

    /**
     * Determine if the model should be searchable.
     */
    public function shouldBeSearchable(): bool
    {
        return $this->state === 'published';
    }
}

class BlogEntry extends Model
{
    use Searchable;

    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => strip_tags($this->fulltxt),
            'author' => $this->author->name,
            'tags' => $this->tags->pluck('tag')->toArray(),
            'component' => 'com_blog',
        ];
    }
}
```

**Searching:**

```php
// Simple search
$results = Publication::search('quantum physics')->get();

// Filtered search
$results = Publication::search('quantum')
    ->where('type', 'journal_article')
    ->get();

// Cross-model search via com_search controller
class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('q');

        $results = collect([
            'publications' => Publication::search($query)->take(10)->get(),
            'blog'         => BlogEntry::search($query)->take(10)->get(),
            'members'      => Member::search($query)->take(10)->get(),
            'groups'       => Group::search($query)->take(10)->get(),
            'resources'    => Resource::search($query)->take(10)->get(),
        ])->filter(fn ($items) => $items->isNotEmpty());

        return view('search::results', compact('results', 'query'));
    }
}
```

**Indexing is automatic** — Scout observes Eloquent model events (create, update,
delete) and syncs the search index via queue jobs. No manual indexing scripts
needed. Bulk import for initial index:

```bash
php artisan scout:import "Hubzero\Component\Publications\Models\Publication"
```

**Backend configuration:**

```php
// config/scout.php
'driver' => env('SCOUT_DRIVER', 'meilisearch'),

'meilisearch' => [
    'host' => env('MEILISEARCH_HOST', 'http://127.0.0.1:7700'),
    'key' => env('MEILISEARCH_KEY'),
],
```

**Migration path:** The existing `onContentSearch` plugin events are preserved
during transition. `com_search` can query both Scout and the legacy plugin
events, merging results. As components add the `Searchable` trait, their
search plugins become unnecessary.

**Tenant awareness:** Each tenant gets its own search index prefix
(`{hostname}_publications`, `{hostname}_blog`), configured automatically
by the `TenantManager` at boot. Supergroup content can optionally be indexed
into the group's own prefixed index.

---

## 26. Feature Flags

### Current State

HubZero has no feature flag system. New features are deployed all-or-nothing.
The strangler-fig migration has no mechanism to gradually roll out migrated
components, test them with specific users, or quickly rollback.

### Proposed Architecture

**Laravel Pennant** is Laravel's first-party feature flag package. It stores
flags in the database and resolves them per-user, per-tenant, or globally.

**Primary use case: Gradual migration rollout.**

During the strangler-fig migration, each component can be flagged between
its legacy and migrated version:

```php
// Define feature flags for migration
class MigrationFeatures
{
    // Resolve per-tenant — operators can opt in to migrated components
    public function blogMigrated(): bool
    {
        return match (true) {
            app(TenantManager::class)->current() === 'beta.example.com' => true,
            default => false,
        };
    }

    // Resolve per-user — let specific users test migrated UI
    public function newPublicationsUi(User $user): bool
    {
        return $user->isBetaTester();
    }

    // Global — flip for everyone when ready
    public function newSearchEngine(): bool
    {
        return false; // flip to true when Meilisearch is ready
    }
}
```

**Usage in routing and controllers:**

```php
// Route-level — serve different controller based on flag
Route::get('/blog', function () {
    if (Feature::active('blog-migrated')) {
        return app(NewBlogController::class)->index();
    }
    return app(LegacyDispatchController::class)->handle();
});

// Blade — show different UI based on flag
@feature('new-publications-ui')
    <livewire:publication-search />
@else
    @include('publications::legacy-search')
@endfeature

// Middleware — gate entire route groups
Route::middleware('feature:new-search-engine')
    ->get('/search', [ScoutSearchController::class, 'index']);
```

**Filament admin page for flag management:**

```php
class FeatureFlagResource extends Resource
{
    // Hub admins can:
    // - Toggle flags globally
    // - Enable flags per-tenant
    // - Enable flags for specific users (beta testers)
    // - See which flags are active and for whom
}
```

**Beyond migration — permanent feature flags:**

```php
// Gate experimental features per-tenant
Feature::define('tool-sessions-v2', function (?User $user) {
    return app(TenantManager::class)->current() === 'dev.example.com';
});

// A/B testing a new group landing page
Feature::define('group-landing-redesign', function (?User $user) {
    return $user && $user->id % 2 === 0; // 50% of users
});
```

**Pennant stores flag state in the database** (`features` table) so flags
persist across deployments, survive Octane worker restarts, and can be
toggled from the Filament admin without code changes or redeployment.

---

## 27. Bot Protection & Abuse Prevention

Academic hubs are frequent targets for automated abuse — spam account registration,
scraping of publications and datasets, brute-force login attempts, and form spam.
HubZero 3.0 addresses this with a layered defense that escalates friction only when
needed, so legitimate users rarely encounter any barrier.

### Defense Layers

```
Layer 0: Firewall / CDN (Cloudflare, AWS WAF — external, optional)
Layer 1: Rate Limiting (already in §12 — throttle middleware)
Layer 2: Honeypot Fields (invisible, zero friction)
Layer 3: Bot Score Middleware (behavioral analysis)
Layer 4: CAPTCHA Challenge (Turnstile — only when suspicious)
Layer 5: IP Reputation & Blocking (admin-managed)
```

Layers 0–2 are invisible to real users. Layer 3 is invisible unless the score
triggers Layer 4. Layer 5 is reactive and admin-controlled.

### Layer 2: Honeypot Fields

`spatie/laravel-honeypot` adds invisible form fields that humans never fill in
but bots do. One Blade component on any form:

```blade
<form method="POST" action="{{ route('register') }}">
    @csrf
    <x-honeypot />
    {{-- real form fields --}}
</form>
```

```php
// Validation — applies automatically via package middleware,
// or explicitly in form requests:
use Spatie\Honeypot\ProtectAgainstSpam;

Route::post('/register', RegisterController::class)
    ->middleware(ProtectAgainstSpam::class);
```

Honeypot fields protect registration, contact forms, support tickets, wiki edits,
and any other user-submitted content. Zero friction for humans, catches the majority
of unsophisticated bots.

### Layer 3: Bot Score Middleware

A scoring middleware runs on every request and accumulates suspicion signals.
When the score crosses a threshold, the request is flagged for CAPTCHA challenge
or outright blocked.

```php
class BotScoreMiddleware
{
    protected array $signals = [
        'missing_accept_header'   => 15,  // browsers always send Accept
        'missing_accept_language' => 10,  // browsers always send this
        'no_js_cookie'            => 20,  // cookie set by a tiny JS snippet
        'known_bot_ua'            => 40,  // curl, python-requests, scrapy, etc.
        'request_cadence'         => 25,  // >N requests in M seconds (below rate limit)
        'no_referer_on_post'      => 10,  // form POST without referer
        'sequential_path_scan'    => 30,  // /page/1, /page/2, /page/3...
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $score = $this->calculateScore($request);

        // Store for analytics (§22) — track bot traffic patterns
        $request->attributes->set('bot_score', $score);

        if ($score >= 80) {
            // Almost certainly a bot — block immediately
            abort(429, 'Suspicious request blocked.');
        }

        if ($score >= 50 && $this->requiresCaptcha($request)) {
            // Suspicious — redirect to CAPTCHA challenge
            session()->put('captcha_redirect', $request->fullUrl());
            return redirect()->route('captcha.challenge');
        }

        return $next($request);
    }

    protected function requiresCaptcha(Request $request): bool
    {
        // Only challenge on state-changing or expensive operations
        return $request->isMethod('POST')
            || $request->routeIs('search.*')
            || $request->routeIs('api.*');
    }

    protected function calculateScore(Request $request): int
    {
        $score = 0;

        if (!$request->hasHeader('Accept') || $request->header('Accept') === '*/*') {
            $score += $this->signals['missing_accept_header'];
        }

        if (!$request->hasHeader('Accept-Language')) {
            $score += $this->signals['missing_accept_language'];
        }

        if (!$request->cookie('_js')) {
            $score += $this->signals['no_js_cookie'];
        }

        if ($this->isKnownBotUserAgent($request->userAgent())) {
            $score += $this->signals['known_bot_ua'];
        }

        if ($this->hasHighCadence($request)) {
            $score += $this->signals['request_cadence'];
        }

        return $score;
    }

    protected function hasHighCadence(Request $request): bool
    {
        $key = 'bot_cadence:' . $request->ip();
        $hits = Cache::increment($key);
        if ($hits === 1) {
            Cache::put($key, 1, now()->addSeconds(10));
        }
        return $hits > 20; // >20 requests in 10 seconds
    }
}
```

**JS cookie verification:** The base layout includes a tiny inline script that
sets a cookie on first visit. Real browsers execute it; headless scrapers
typically don't. This is not a security boundary — it's one signal among many.

```blade
{{-- In base Blade layout, inside <head> --}}
<script>document.cookie='_js=1;path=/;SameSite=Lax';</script>
```

### Layer 4: CAPTCHA Challenge (Cloudflare Turnstile)

When the bot score triggers a challenge, the user sees a Turnstile widget.
Turnstile is free, privacy-friendly, and usually invisible — most legitimate
users see a brief spinner and are passed through automatically. It only presents
a visual challenge when it detects anomalies.

```php
// config/services.php
'turnstile' => [
    'site_key'   => env('TURNSTILE_SITE_KEY'),
    'secret_key' => env('TURNSTILE_SECRET_KEY'),
],
```

```php
// Validation rule — usable in any form request
class TurnstileRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $response = Http::asForm()->post(
            'https://challenges.cloudflare.com/turnstile/v0/siteverify',
            [
                'secret'   => config('services.turnstile.secret_key'),
                'response' => $value,
                'remoteip' => request()->ip(),
            ]
        );

        if (!$response->json('success')) {
            $fail('CAPTCHA verification failed.');
        }
    }
}
```

```blade
{{-- Challenge page (resources/views/captcha/challenge.blade.php) --}}
<div class="turnstile-container">
    <p>Please verify you are human to continue.</p>
    <form method="POST" action="{{ route('captcha.verify') }}">
        @csrf
        <div class="cf-turnstile"
             data-sitekey="{{ config('services.turnstile.site_key') }}"
             data-callback="onSuccess">
        </div>
    </form>
</div>
<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
```

**Where Turnstile is always required** (regardless of bot score):
- User registration
- Password reset requests
- Contact / support ticket forms

**Where Turnstile is conditionally required** (only when bot score is elevated):
- Search queries
- API authentication
- Form submissions on wiki, publications, etc.

### Layer 5: IP Reputation & Blocking

Admin-managed blocklist with Filament UI. Supports individual IPs, CIDR ranges,
and optional expiry for temporary blocks.

```php
// Migration
Schema::create('blocked_ips', function (Blueprint $table) {
    $table->id();
    $table->string('ip_address', 45);       // IPv4 or IPv6
    $table->string('cidr_range', 49)->nullable(); // e.g., 192.168.0.0/16
    $table->string('reason')->nullable();
    $table->unsignedBigInteger('blocked_by'); // admin user ID
    $table->timestamp('expires_at')->nullable(); // null = permanent
    $table->timestamps();

    $table->index('ip_address');
});
```

```php
class BlockedIpMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Cache the blocklist in Redis — rebuild on admin changes
        $blocked = Cache::remember('blocked_ips', 3600, function () {
            return BlockedIp::active()->pluck('ip_address', 'cidr_range')->all();
        });

        if ($this->isBlocked($request->ip(), $blocked)) {
            abort(403, 'Access denied.');
        }

        return $next($request);
    }
}
```

**Auto-blocking:** When the bot score middleware blocks a request (score ≥ 80)
more than N times from the same IP within an hour, a queue job automatically
creates a temporary block (24-hour expiry) and notifies admins via the
notification system (§23).

```php
// Dispatched from BotScoreMiddleware when score >= 80
AutoBlockJob::dispatchIf(
    $this->exceedsAutoBlockThreshold($request->ip()),
    $request->ip()
);
```

### Integration with Analytics (§22)

The `bot_score` attribute set by `BotScoreMiddleware` is recorded in the
`analytics_hits` table. This enables reporting on:

- Bot traffic volume vs. legitimate traffic over time
- Which paths are most targeted by scrapers
- Geographic distribution of bot traffic
- Effectiveness of each defense layer (how many blocked at each stage)

The `AnalyticsMiddleware` already runs after `BotScoreMiddleware` in the
middleware stack and captures the score:

```php
// In RecordAnalyticsHit job
'bot_score' => $request->attributes->get('bot_score', 0),
```

### Middleware Stack Order

Bot protection middleware runs early in the stack, before expensive operations
like database queries or tenant resolution:

```php
// bootstrap/app.php or Kernel
->withMiddleware(function (Middleware $middleware) {
    $middleware->prepend([
        BlockedIpMiddleware::class,     // Layer 5 — cheapest check first
        BotScoreMiddleware::class,      // Layer 3 — behavioral scoring
    ]);

    // Honeypot (Layer 2) and Turnstile (Layer 4) are applied
    // per-route or per-form, not globally
})
```

### Configuration

All thresholds are configurable per-deployment, since bot pressure varies
significantly between a small lab hub and a large public-facing instance:

```php
// config/hubzero/bot_protection.php
return [
    'enabled'               => env('BOT_PROTECTION_ENABLED', true),
    'score_challenge'       => env('BOT_SCORE_CHALLENGE', 50),
    'score_block'           => env('BOT_SCORE_BLOCK', 80),
    'cadence_window'        => env('BOT_CADENCE_WINDOW', 10),    // seconds
    'cadence_threshold'     => env('BOT_CADENCE_THRESHOLD', 20), // requests
    'auto_block_threshold'  => env('BOT_AUTO_BLOCK_COUNT', 10),  // blocks/hour
    'auto_block_duration'   => env('BOT_AUTO_BLOCK_HOURS', 24),  // hours
    'turnstile_always_on'   => ['register', 'password.request', 'contact'],
    'honeypot_enabled'      => env('HONEYPOT_ENABLED', true),
];
```

### Friendly Bot Allowlist

Legitimate crawlers (Googlebot, Bingbot, academic indexers) should not be
blocked or challenged. The bot score middleware skips scoring for verified
crawlers:

```php
protected array $allowedBots = [
    'Googlebot'   => 'dns:crawl-*.googlebot.com',
    'Bingbot'     => 'dns:*.search.msn.com',
    'DOAJbot'     => 'ip:194.0.117.0/24',
    'CrossRef'    => 'ua:Crossref',
];

protected function isVerifiedBot(Request $request): bool
{
    $ua = $request->userAgent();

    foreach ($this->allowedBots as $name => $verification) {
        if (!str_contains($ua, $name)) continue;

        // Verify via reverse DNS or IP range — don't trust UA alone
        return match (Str::before($verification, ':')) {
            'dns' => $this->verifyReverseDns($request->ip(), Str::after($verification, ':')),
            'ip'  => $this->verifyIpRange($request->ip(), Str::after($verification, ':')),
            'ua'  => true, // trusted user-agent (low-risk indexers)
            default => false,
        };
    }

    return false;
}
```

This is important for academic hubs — DOI indexers, DOAJ, CrossRef, and Google
Scholar all need to crawl publications. Blocking them damages discoverability.

---

## Appendix: Current → Laravel Quick Reference

```
Hubzero\Base\Application          →  Illuminate\Foundation\Application
Hubzero\Container\Container       →  Illuminate\Container\Container
Hubzero\Base\ServiceProvider      →  Illuminate\Support\ServiceProvider
Hubzero\Base\Middleware           →  Illuminate\Http\Middleware (separate class)
Hubzero\Facades\Facade            →  Illuminate\Support\Facades\Facade
Hubzero\Http\Request              →  Illuminate\Http\Request
Hubzero\Http\Response             →  Illuminate\Http\Response
Hubzero\Database\Relational       →  Illuminate\Database\Eloquent\Model
Hubzero\Database\Query            →  Illuminate\Database\Query\Builder
Hubzero\Database\Rows             →  Illuminate\Database\Eloquent\Collection
Hubzero\Config\Repository         →  Illuminate\Config\Repository
Hubzero\Routing\Router            →  Illuminate\Routing\Router
Hubzero\Events\Dispatcher         →  Custom (wraps Illuminate\Events\Dispatcher)
Hubzero\View\View                 →  Illuminate\View\View (or preserved for legacy)
Hubzero\Plugin\Plugin             →  Preserved (Hubzero\Plugin\Plugin)
Hubzero\Module\Module             →  Preserved (Hubzero\Module\Module)
Hubzero\Component\SiteController  →  Illuminate\Routing\Controller
Admin template infrastructure     →  Filament
Ad-hoc email sending              →  Laravel Notifications (mail + database + broadcast)
(none)                            →  Laravel Reverb (WebSocket server) + Echo (JS client)
Custom Solr integration           →  Laravel Scout + Meilisearch
(none)                            →  Laravel Pennant (feature flags)
Hubzero\Access ACL                →  Laravel Policies & Gates
(none)                            →  Rate limiting middleware (throttle:api, etc.)
(none)                            →  spatie/laravel-backup (scheduled automated backups)
(none)                            →  Maintenance mode (php artisan down --secret)
(none)                            →  BotScoreMiddleware (behavioral bot detection)
(none)                            →  Cloudflare Turnstile (CAPTCHA challenge)
(none)                            →  spatie/laravel-honeypot (invisible form protection)
Apache log processing scripts     →  AnalyticsMiddleware + RecordAnalyticsHit (queued)
Offline monthly usage tables      →  analytics_hits + analytics_monthly (rollup)
Supergroup system plugin          →  SupergroupMiddleware + GroupPackageLoader
Joomla/HubZero layered CSS        →  CSS custom property tokens + @layer architecture
Template from scratch (complex)   →  Blade layout + token overrides (afternoon)
Per-site branding (manual CSS)    →  TenantBrandingServiceProvider (admin UI)
Raw HTML in components            →  Blade component library (x-hub-card, x-hub-form, etc.)
Supergroup Template (eval)        →  Standard Blade layout (TemplateServiceProvider)
Supergroup custom components      →  Group-scoped Laravel packages
#__xgroups_modules                →  #__modules (scope='group', scope_id=gidNumber)
/site/groups/{gid}/               →  storage/groups/{gid}/
```

## Appendix: Extension Package Quick Reference

```
packages/{vendor}/framework/               → {vendor}/framework
packages/{vendor}/components/{name}/       → {vendor}/component-{name}
packages/{vendor}/plugins/{group}/{name}/  → {vendor}/plugin-{group}-{name}
packages/{vendor}/modules/{name}/          → {vendor}/module-{name}
packages/{vendor}/templates/{name}/        → {vendor}/template-{name}
packages/{vendor}/languages/{code}/        → {vendor}/lang-{code}
```

Override priority: group packages → tenant packages → shared packages → Laravel core

## Appendix: Getting Started — MVP Roadmap

The migration begins inside the existing repository. Laravel is added at the repo
root alongside `core/`, which remains untouched and runnable throughout the process.

### Repository Layout

```
hubzero-cms/                       ← existing repo root (legacy document root)
├── index.php                      ← original HubZero entry point (unchanged)
├── public/                        ← Laravel document root
│   └── index.php                  ← Laravel entry point
├── core/                          ← existing HubZero codebase (unchanged)
│   ├── vendor/                    ← existing Composer dependencies
│   └── ...
├── app/                           ← Laravel application
├── config/                        ← Laravel configuration
├── routes/                        ← Laravel route definitions
├── packages/                      ← migrated components as Laravel packages
├── resources/                     ← Blade views, Vite assets
├── vendor/                        ← Laravel Composer dependencies
├── composer.json                  ← new root-level Composer
└── artisan
```

The existing `index.php` is left untouched. Laravel uses the conventional
`public/index.php` with `public/` as its document root. Both stacks run
simultaneously during development for side-by-side comparison.

The `core/` directory keeps its own `vendor/` and `composer.json` — the two
dependency trees are independent. Old HubZero remains fully runnable on
its existing port.

### MVP Milestones

**MVP 0 — Laravel boots (done)**
Add `public/index.php` as Laravel 12's entry point with `public/` as its own
document root. A `/hello` route proves Laravel is running. No legacy code is
touched.

**MVP 1 — First real route + dual-view engine (done)**
A `/status` health-check page rendered two ways: Blade layout with Tailwind CSS
(`/status`) and the existing HubZero PHP template (`/status/legacy`).
`LegacyTemplateRenderer` processes jdoc:include tags and provides stub facades
(`Html`, `Lang`, `Config`, `User`, `Request`, `App`, `Component`, `Route`) so
existing templates render without touching `core/`. Module positions are stubbed
(return empty). Database connection reads from the existing HubZero MariaDB
instance. On real hub deployments, templates get a mechanical facade-rename
cleanup pass — structure and HTML output stay identical.

**MVP 2 — First component (weeks)**
`com_blog` (or another simple component) fully packaged under `packages/`. Eloquent
models reading from existing blog tables, Blade views rendering entries. The legacy
catch-all still handles everything else. This is where the Eloquent shim for
`Relational`, the view bridge, and the package structure get built and proven.

**MVP 3 — Admin panel (days after MVP 2)**
Filament installed with a blog resource. CRUD for blog entries in the new admin
panel, managing real data alongside the old admin.

**MVP 4 — Two rendering paths coexist (week)**
Blog served by Blade, everything else by the legacy Document pipeline. Both share
the same template chrome. A visitor cannot tell which engine served a given page.
Design tokens and Blade component library (§10) in use for the new views.
Bot protection middleware (§27) active on all routes.

**MVP 5 — Multi-tenancy (week)**
TenantManager resolving tenants from hostname. Two tenants hitting the same codebase,
each seeing their own data.

**MVP 6 — Patterns solidify (ongoing)**
A second, more complex component migrated (e.g., `com_publications`). The migration
template and checklist get battle-tested. After this milestone, remaining components
follow an established pattern.

All time estimates assume LLM-assisted development with human review and steering.
Multiply by roughly 5–10× for people-only time. The real bottleneck is not code
generation — it is the per-component decisions about schema preservation, undocumented
side effects, and legacy behavior that require human judgement.
