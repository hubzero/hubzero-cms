# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

HubZero is an open source PHP platform for building scientific collaboration websites ("hubs"). It's a component-based CMS with Joomla heritage, using modern patterns like dependency injection and an Eloquent-style ORM.

## Development Commands

```bash
# Install dependencies
cd core && composer install

# Run linting
vendor/bin/parallel-lint core/

# Run code style checks (PSR-12)
vendor/bin/phpcs core/ --standard=PSR12

# Run tests
vendor/bin/phpunit core/

# CLI executor for console commands and migrations
php core/bin/hzexec.php
```

## Architecture

### Directory Structure

```
/core/
├── bootstrap/              # Application bootstrapping and service providers
│   ├── Site/              # Public frontend client
│   ├── Administrator/     # Backend admin client
│   ├── Api/               # REST API client
│   └── Cli/               # CLI client
├── components/            # MVC components (com_content, com_users, etc.)
├── modules/               # Display modules (mod_menu, mod_search, etc.)
├── plugins/               # Event listener plugins by type (system, auth, etc.)
├── templates/             # Template themes
├── libraries/Hubzero/     # Core framework classes
└── migrations/            # Database migrations
/app/                      # Custom overrides (templates, config)
```

### Request Flow

1. `index.php` → `Hubzero\Base\Application` boot
2. Service providers register database, router, auth, etc.
3. Middleware pipeline processes request
4. Router dispatches to component based on `option` parameter
5. Component controller executes task method
6. View renders response

### Component Structure

Each component in `/core/components/com_{name}/` follows this pattern:

```
com_{name}/
├── site/                  # Frontend
│   ├── controllers/       # Task-based controllers
│   ├── views/{view}/tmpl/ # View templates
│   └── router.php         # SEF URL routing
├── admin/                 # Backend (same structure)
├── models/                # Relational ORM models
├── migrations/            # Component migrations
└── api/                   # REST endpoints
```

### Key Framework Classes

- **`Hubzero\Base\Application`** - Main app, extends DI container
- **`Hubzero\Component\SiteController`** - Base controller, task methods end in `Task()`
- **`Hubzero\Database\Relational`** - Modern ORM base class (preferred)
- **`Hubzero\Database\Table`** - Legacy Joomla-compatible ORM (deprecated)
- **`Hubzero\Plugin\Plugin`** - Plugin base class
- **`Hubzero\Module\Module`** - Module helper base class

### Controller Task Pattern

Controllers use task-based routing where `task` parameter maps to method:

```php
class Articles extends SiteController {
    public function displayTask() { /* task=display */ }
    public function editTask()    { /* task=edit */ }
    public function saveTask()    { /* task=save */ }
}
```

### ORM Usage

Models extend `Hubzero\Database\Relational`:

```php
namespace Components\Blog\Models;

class Entry extends \Hubzero\Database\Relational {
    protected $table = '#__blog_entries';

    public function comments() {
        return $this->hasMany(__NAMESPACE__ . '\Comment');
    }

    public function author() {
        return $this->belongsTo(\Hubzero\User\User::class, 'created_by');
    }
}
```

### Facades

Static facades proxy to container services:

```php
Route::url('index.php?option=com_content');  // $app['route']
User::get('id');                              // $app['user']
Lang::txt('TRANSLATION_KEY');                 // $app['lang']
App::get('db');                               // Database connection
```

### Path Constants

```php
PATH_ROOT   // Document root
PATH_CORE   // /core directory
PATH_APP    // /app overrides directory
```

### Events

Plugins hook into lifecycle events:

```php
class plgSystemExample extends \Hubzero\Plugin\Plugin {
    public function onAfterInitialise() { }
    public function onBeforeDispatch() { }
    public function onAfterDispatch() { }
}
```

## Namespace Conventions

- `Hubzero\*` - Framework core
- `Components\{Name}\*` - Component classes
- `Modules\{Name}\*` - Module classes
- `Plugins\{Type}\{Name}\*` - Plugin classes
- `Bootstrap\{Client}\Providers\*` - Service providers

## Model State Constants

```php
const STATE_UNPUBLISHED = 0;
const STATE_PUBLISHED   = 1;
const STATE_DELETED     = 2;
```

## Testing

Tests use PHPUnit with `Hubzero\Test\Database` base class:

```php
class EntryTest extends \Hubzero\Test\Database {
    public function setUp(): void {
        \Hubzero\Database\Relational::setDefaultConnection($this->getMockDriver());
    }
}
```

Test locations:
- Component tests: `/core/components/{name}/tests/`
- Library tests: `/core/libraries/Hubzero/{lib}/Tests/`
