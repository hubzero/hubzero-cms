# Blade Page Shell + Dual Template Support

## Context

The site currently wraps all component output with `LegacyTemplateRenderer`, which
executes the legacy PHP template (`app/templates/hubzero/index.php`) and replaces
`<jdoc:include>` tags. As components get modernized, they should be able to opt
into a Blade page shell instead — same daisyUI look from the UX docs, but using
Laravel's Blade engine with `@yield`, `@section`, `@stack`, etc.

The opt-in is at the **view level**, not the component level. This means a single
component can have some views using legacy markup and others using the Blade
shell — enabling one-view-at-a-time migration within a component.

Two rendering paths coexist:

1. **Legacy** (default): Component echoes HTML → `LegacyTemplateRenderer` wraps it
   with `app/templates/hubzero/index.php` (jdoc tags, legacy facades)
2. **Blade** (opt-in per view): Any code running during the component lifecycle
   calls `app()->instance('hubzero.template.engine', 'blade')` → dispatcher
   wraps output with the Blade layout instead.

A third path already works with no changes: pure Laravel controllers with their
own routes return Blade views that `@extends('layouts.hubzero')` directly,
bypassing the fallback dispatch entirely.

## Plan

### 1. Create Blade page shell layout

**New file**: `resources/views/layouts/hubzero.blade.php`

Full page shell matching `docs/ux/README.md`:
- `<html lang="en" data-theme="hubzero">`
- FOUC-prevention inline script in `<head>`
- Skip navigation link
- `<body class="{{ $option ?? '' }} bg-base-100 ...">` for component CSS scoping
- `<header>` navbar with main menu and theme toggle
- Breadcrumb nav from Pathway service
- `<main id="main-content">` with `{!! $content !!}` or `@yield('content')`
- Conditional sidebar via module positions (`left`, `right`)
- Flash messages (Laravel session + HubZero Notify)
- `<footer>`
- `@stack('styles')` and `@stack('scripts')` for component assets

Key differences from legacy template:
- No `<jdoc:include>` tags — uses Blade directives
- Module positions rendered via `{!! app('hubzero.module')->position('left') !!}`
- Head assets from Document service rendered via a `@include('partials.head')`
- Standard Blade `@yield` / `@section` / `@stack` for extensibility

### 2. Create head partial

**New file**: `resources/views/partials/head.blade.php`

Renders stylesheets, scripts, and meta tags collected by the Document singleton,
so legacy components that call `Document::addStyleSheet()` etc. still work.

### 3. Create messages partial

**New file**: `resources/views/partials/messages.blade.php`

Renders both Laravel session flash messages and HubZero Notify messages as
daisyUI `.alert` components with appropriate ARIA roles.

### 4. Register template engine context singleton

**File**: `packages/hubzero/framework/src/FrameworkServiceProvider.php`

Register `hubzero.template.engine` defaulting to `'legacy'`:

```php
$this->app->instance('hubzero.template.engine', 'legacy');
```

Using `instance()` (not `singleton()`) so views can swap it with another
`instance()` call during execution.

### 5. Check template engine after render in dispatcher

**File**: `packages/hubzero/framework/src/Http/ComponentDispatchController.php`

In `dispatch()`, after `$loader->render()`, check `app('hubzero.template.engine')`:

```php
$componentHtml = $loader->render($option);

if (app('hubzero.template.engine') === 'blade') {
    $title = $document->getTitle() ?: ucfirst($component);
    return new Response(view('layouts.hubzero', [
        'content' => $componentHtml,
        'option'  => $option,
        'title'   => $title,
    ])->render());
}

// Existing legacy path unchanged...
```

No changes to Loader needed — the view/controller sets the engine during
execution, the dispatcher reads it after.

Same pattern can be added to `adminDispatch()` later when an admin Blade layout
is needed — not in scope here.

### 6. Expose via legacy App API

**File**: `packages/hubzero/framework/src/Facades/Services/AppService.php`

So legacy code can also query and set the engine:

```php
// Query:
case 'template.engine':
    return app('hubzero.template.engine');

// Set (via App::set()):
case 'template.engine':
    app()->instance('hubzero.template.engine', $value);
    break;
```

### 7. Update existing Blade layout

**File**: `resources/views/layouts/app.blade.php`

Update to use daisyUI classes for consistency. This layout stays as a minimal
variant for non-component pages (pure Laravel routes like `/status`).

### 8. Document the dual-template system

**File**: `docs/ux/README.md`

Add a "Template Engines" section explaining:
- Legacy PHP templates (default, `<jdoc:include>` tags)
- Blade page shell (opt-in per view)
- Pure Laravel controllers (own routes, `@extends('layouts.hubzero')`)

## Files to create/modify

| Action | File |
|--------|------|
| Create | `resources/views/layouts/hubzero.blade.php` |
| Create | `resources/views/partials/head.blade.php` |
| Create | `resources/views/partials/messages.blade.php` |
| Modify | `packages/hubzero/framework/src/FrameworkServiceProvider.php` |
| Modify | `packages/hubzero/framework/src/Http/ComponentDispatchController.php` |
| Modify | `packages/hubzero/framework/src/Facades/Services/AppService.php` |
| Modify | `resources/views/layouts/app.blade.php` |
| Modify | `docs/ux/README.md` |

## How a view opts in

Any code running during the component lifecycle — a controller action, a view
template, a helper — can switch to the Blade page shell:

```php
// In a controller action:
public function displayTask()
{
    // This view is ready for the new template
    app()->instance('hubzero.template.engine', 'blade');

    $view = $this->view('default', 'entries');
    $view->entries = Entry::all();
    $view->display();
}

// Or in a view template file:
<?php app()->instance('hubzero.template.engine', 'blade'); ?>
<div class="list bg-base-100 rounded-box shadow-sm">
  ...daisyUI markup...
</div>
```

Legacy equivalent via App facade:

```php
App::set('template.engine', 'blade');
```

Other views in the same component that don't set the flag continue using the
legacy template — migration happens one view at a time.

Plugins can also check the active engine to decide which markup to output:

```php
$engine = app('hubzero.template.engine'); // 'legacy' or 'blade'
if ($engine === 'blade') {
    return '<div class="alert alert-info" role="status">' . $msg . '</div>';
} else {
    return '<p class="info">' . $msg . '</p>';
}
```

No changes to any existing component — everything continues using the legacy
template by default.

## Verification

```bash
# Existing site pages still work (legacy template)
curl -sk https://localhost:9443/login 2>/dev/null | grep '<title>'
curl -sk https://localhost:9443/about 2>/dev/null | grep '<title>'

# A view that calls app()->instance('hubzero.template.engine', 'blade')
# renders via Blade layout (test with com_test)
curl -sk https://localhost:9443/test 2>/dev/null | grep 'data-theme'

# Admin still works (unchanged, uses kameleon)
curl -sk https://localhost:9443/admin 2>/dev/null | grep '<title>'

# Check for errors
cat storage/logs/laravel.log
```
