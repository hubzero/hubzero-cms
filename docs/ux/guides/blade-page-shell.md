# Blade Page Shell

Blade layout template for components that have opted into the new daisyUI design
system. Provides the same page structure as the
[UX docs index](../README.md) but using Laravel Blade syntax with
`@yield`, `@section`, `@stack`, and `@include` directives.

## When to Use

The opt-in is currently done at the **controller/component level** by declaring
preferred view engines and CSS frameworks on the site controller. This is the
pattern used by the Blade-enabled site components already in the repository.

```php
class Events extends SiteController
{
    protected $viewEngines = ['blade', 'php'];
    protected $cssFrameworks = ['daisyui', 'classic'];
}
```

For components with multiple site controllers, use a shared base controller:

```php
class ComponentController extends \Hubzero\Component\SiteController
{
    protected $viewEngines = ['blade', 'php'];
    protected $cssFrameworks = ['daisyui', 'classic'];
}
```

That allows Blade and legacy `.php` templates to coexist while the framework
prefers Blade when both exist. The older `template.engine` switching examples
do not reflect the current migration path and should not be used as the
primary guidance.

## Template File

`app/templates/hubzero/index.blade.php`

The Blade page shell lives alongside the legacy `index.php` in the CMS template
directory. For Blade-enabled requests, the dispatcher renders this file instead
of the legacy `index.php`.

```blade
<!DOCTYPE html>
<html dir="{{ $direction }}" lang="{{ $lang }}" data-theme="hubzero"
      data-css-framework="daisyui" data-view-engine="blade">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>{{ $pageTitle }}</title>
  <script>/* theme FOUC prevention */
    (function(){var s=localStorage.getItem('hubzero-theme');if(!s)s=matchMedia('(prefers-color-scheme:dark)').matches?'hubzero-dark':'hubzero';document.documentElement.setAttribute('data-theme',s)})();
  </script>
  <link rel="stylesheet" href="/app/templates/hubzero/css/site.css" />
  @include('hubzero::partials.head')
  @stack('styles')
</head>
<body class="{{ $option }}{{ $isHome ? ' home' : '' }} bg-base-300 text-base-content min-h-screen flex flex-col">

  <a href="#main-content" class="skip-link">
    {{ Lang::txt('TPL_HUBZERO_SKIP_TO_CONTENT', 'Skip to main content') }}
  </a>

  @include('hubzero::partials.navbar')
  @include('hubzero::partials.drawer')
  @include('hubzero::partials.breadcrumb')
  @include('hubzero::partials.help-drawer')
  @include('hubzero::partials.messages')

  {{-- Content area: optional left aside, component content, optional right aside --}}
  <div class="max-w-7xl mx-auto w-full px-4 pt-[9px] pb-8 flex-1 {{ $cols }}">
    @if($hasLeft)
      <aside class="space-y-6"
             aria-label="{{ Lang::txt('TPL_HUBZERO_SIDEBAR', 'Sidebar') }}">
        {!! $mod->position('left') !!}
      </aside>
    @endif

    <main id="main-content" tabindex="-1" class="min-w-0 bg-base-200 pt-3 px-6 pb-6">
      @hasSection('content') @yield('content') @else {!! $content ?? '' !!} @endif
    </main>

    @if($hasRight)
      <aside class="space-y-6"
             aria-label="{{ Lang::txt('TPL_HUBZERO_SIDEBAR_RIGHT', 'Complementary') }}">
        {!! $mod->position('right') !!}
      </aside>
    @endif
  </div>

  @include('hubzero::partials.footer')

  <script src="/app/templates/hubzero/js/site.js"></script>
  @stack('scripts')
</body>
</html>
```

## Current Implementation Notes

- The site shell currently includes an inline theme bootstrap `<script>` for
  flash-of-unstyled-theme prevention.
- A strict site-wide CSP is still a target state, not something the current
  shell fully satisfies today.
- New site Blade work should still avoid adding new inline scripts or styles so
  the remaining CSP cleanup stays bounded.

### Three-Tier Background System

The template uses three levels of background color to create visual depth:

| Tier | Token | Purpose |
|------|-------|---------|
| Body | `bg-base-300` | Darkest — visible as page margin/gutter |
| Main content | `bg-base-200` | Mid — the content reading area |
| Cards/lists | `bg-base-100` | Lightest — elevated items (list rows, cards) |

This provides clear visual separation between the page chrome, the content
area, and interactive elements without relying on borders or shadows.

### Template Directory Structure

The Blade page shell adds files alongside the existing legacy template:

```
app/templates/hubzero/
├── index.php               ← Legacy template (jdoc tags, loads index.css)
├── index.blade.php         ← Blade page shell (daisyUI, loads site.css)
├── component.php           ← Component-only layout (pop-ups)
├── error.php               ← Error page
├── offline.php             ← Maintenance page
├── templateDetails.xml     ← Template metadata & positions
├── partials/
│   ├── navbar.blade.php       ← Site header, nav, search, theme toggle, user menu
│   ├── theme-toggle.blade.php ← Sun/moon swap (included by navbar)
│   ├── user-menu.blade.php    ← Avatar dropdown (included by navbar)
│   ├── drawer.blade.php       ← Mobile navigation drawer
│   ├── breadcrumb.blade.php   ← Breadcrumb trail from Pathway service
│   ├── head.blade.php         ← Document service assets (meta, CSS, JS)
│   ├── messages.blade.php     ← Flash & Notify alerts
│   ├── help-drawer.blade.php ← Help/trouble report drawer
│   └── footer.blade.php      ← Footer links + copyright
├── css/
│   ├── index.css           ← Legacy stylesheet (CSS custom properties)
│   ├── site.css           ← Tailwind + daisyUI compiled output
│   └── site.src.css       ← Tailwind source (input for build)
├── js/
│   ├── hub.js              ← Legacy template JS
│   └── site.js             ← Behaviors, theme toggle, drawer, dropdown ARIA
├── html/                   ← Module/component overrides
├── language/
│   └── en-GB/
│       └── en-GB.tpl_hubzero.ini
└── images/
```

The two stylesheets are completely independent:

- **`index.css`** — Hand-written CSS custom properties (`:root { --color-primary: ... }`)
  used by the legacy `index.php` template and existing component markup.
- **`site.css`** — Compiled Tailwind + daisyUI output. Built from `site.src.css`
  (see [Tailwind + daisyUI Theming](tailwind-theming.md) for build setup). Contains
  all daisyUI component classes, Tailwind utilities, and the custom theme tokens.
  No overlap with the legacy custom properties.

Legacy component CSS (`Document::addStyleSheet()`) is still injected via the
head partial, so existing per-component stylesheets continue to load.

The dispatcher registers `app/templates/hubzero/` as a Blade view namespace
(`hubzero::`) so partials resolve via `@include('hubzero::partials.head')`.

## Partials

### navbar.blade.php

Site header with main menu, search, theme toggle, and user dropdown.
Uses daisyUI `navbar`, `menu`, `swap`, `dropdown`, and `avatar` components.

```blade
<header class="navbar bg-base-100 text-base-content sticky top-0 z-30 min-h-0 pt-2 pb-[6px] px-4">
  <div class="navbar-start gap-0">
    <a class="btn btn-ghost text-2xl font-bold normal-case text-primary hover:bg-primary/5 px-3 -mt-1.5"
       href="/">
      {{ $sitename }}
    </a>
    @if($mod && $mod->count('user3'))
      <nav class="hidden lg:flex" aria-label="Main menu">
        <ul class="menu menu-horizontal menu-sm px-0 uppercase text-xs font-semibold tracking-wider"
            role="menubar">
          {!! $mod->position('user3') !!}
        </ul>
      </nav>
    @endif
  </div>

  <div class="navbar-end gap-2">
    {{-- Search toggle + expandable search form --}}
    {{-- User menu or sign-in --}}
    {{-- Mobile hamburger --}}
  </div>
</header>
```

Key differences from the daisyUI default `navbar`:

- `min-h-0 pt-2 pb-[6px]` — tighter vertical padding than default
- No `border-b` or `shadow-sm` — sits flush against the breadcrumb bar below
- Site name and menu are both in `navbar-start` (not center) for left-aligned layout
- `text-2xl -mt-1.5` on site name — larger logo text, vertically aligned with menu items
- Search uses a toggle button that expands a search form (not always-visible)
- Theme toggle is in the breadcrumb bar, not the navbar

The navbar delegates two sub-partials to keep icon SVG and menu markup out of
the main flow:

- **`theme-toggle.blade.php`** — the `swap` label with sun/moon SVG icons
- **`user-menu.blade.php`** — the dropdown with avatar, profile/dashboard
  links, and sign-out (uses WAI-ARIA Menu Button pattern: `<button
  aria-haspopup>` + `<ul role="menu">`)

### drawer.blade.php

Mobile slide-out navigation. Uses daisyUI `drawer` with `role="dialog"` and
`aria-modal="true"`. The hidden checkbox is `aria-hidden="true"`.

```blade
<div class="drawer drawer-end lg:hidden">
  <input id="mobile-drawer" type="checkbox" class="drawer-toggle" aria-hidden="true" />
  <div class="drawer-side z-40" id="mobile-drawer-panel"
       role="dialog" aria-modal="true"
       aria-label="{{ Lang::txt('TPL_HUBZERO_MOBILE_MENU', 'Mobile menu') }}">
    <label for="mobile-drawer" class="drawer-overlay"
           aria-label="{{ Lang::txt('TPL_HUBZERO_CLOSE_MENU', 'Close menu') }}"></label>
    <nav class="menu bg-base-200 text-base-content min-h-full w-80 p-4"
         aria-label="{{ Lang::txt('TPL_HUBZERO_MOBILE_MENU', 'Mobile menu') }}">
      {!! app('hubzero.module')->position('user3') !!}
    </nav>
  </div>
</div>
```

### breadcrumb.blade.php

Sub-masthead bar with dark teal background. Shows breadcrumb trail on
interior pages, tagline on the home page. Also contains the theme toggle
and help button.

```blade
<div class="sub-masthead bg-[#134e4a] text-white/70 text-base">
  <div class="max-w-7xl mx-auto px-4 pt-1 pb-px flex items-center justify-between">
    @if($isHome)
      <p>{{ Lang::txt('TPL_HUBZERO_TAGLINE', '') }}</p>
    @elseif(app()->bound('pathway') && count(app('pathway')->items()) > 0)
      <nav aria-label="Breadcrumb">
        <span class="breadcrumb-trail">
          <a href="/" class="text-white hover:underline">Home</a>
          @foreach(app('pathway')->items() as $crumb)
            <span class="text-white/40 px-1">/</span>
            @if($loop->last)
              <span aria-current="location">{{ $crumb->name }}</span>
            @else
              <a href="{{ $crumb->link }}" class="text-white hover:underline">{{ $crumb->name }}</a>
            @endif
          @endforeach
        </span>
      </nav>
    @endif
    <div class="flex items-center gap-3">
      @include('hubzero::partials.theme-toggle')
      {{-- Help button (if helppane module exists) --}}
    </div>
  </div>
</div>
```

Key design decisions:

- Uses plain `/` separators instead of daisyUI breadcrumb component (tighter fit)
- `text-base` (not `text-sm`) — matched legacy template bar height
- `pt-1 pb-px` — minimal padding for a compact bar
- Theme toggle lives here, not in the navbar
- Last breadcrumb item uses `aria-current="location"` per WCAG 2.4.8

### head.blade.php

Renders assets collected by the Document singleton so legacy
`Document::addStyleSheet()` / `Document::addScript()` calls still work.

```blade
@if(app()->bound('hubzero.document'))
  @php $doc = app('hubzero.document'); @endphp
  @foreach($doc->getMetaData() as $name => $content)
    <meta name="{{ e($name) }}" content="{{ e($content) }}" />
  @endforeach
  @foreach($doc->getStyleSheets() as $href => $attrs)
    <link rel="stylesheet" href="{{ $href }}"
      @if(!empty($attrs['media'])) media="{{ $attrs['media'] }}" @endif />
  @endforeach
  @foreach($doc->getStyleDeclarations() as $css)
    <style>{!! $css !!}</style>
  @endforeach
  @foreach($doc->getScripts() as $src => $attrs)
    <script src="{{ $src }}"
      @if(!empty($attrs['defer'])) defer @endif
      @if(!empty($attrs['async'])) async @endif></script>
  @endforeach
  @foreach($doc->getScriptDeclarations() as $js)
    <script>{!! $js !!}</script>
  @endforeach
@endif
```

### messages.blade.php

Renders Laravel session flash messages and HubZero Notify messages as daisyUI
alerts. Errors/warnings use `role="alert"`, info/success use `role="status"`.

```blade
@php
  $messages = [];
  foreach (['error', 'warning', 'info', 'success'] as $type) {
      if (session()->has($type)) {
          $messages[] = ['type' => $type, 'text' => session($type)];
      }
  }
  if (app()->bound('hubzero.notify')) {
      foreach (app('hubzero.notify')->messages() as $msg) {
          $messages[] = ['type' => $msg->type ?? 'info', 'text' => $msg->message];
      }
  }
  $cls  = ['error'=>'alert-error','warning'=>'alert-warning','info'=>'alert-info','success'=>'alert-success'];
  $role = ['error'=>'alert','warning'=>'alert','info'=>'status','success'=>'status'];
@endphp
@if(count($messages))
  <div class="max-w-7xl mx-auto px-4 pt-4 space-y-2" id="system-message-container">
    @foreach($messages as $msg)
      <div class="alert {{ $cls[$msg['type']] ?? 'alert-info' }}"
           role="{{ $role[$msg['type']] ?? 'status' }}">{{ $msg['text'] }}</div>
    @endforeach
  </div>
@endif
```

### footer.blade.php

Site footer with dark teal background. The `footer` module position renders
above the copyright bar (typically a `mod_custom` with the "Helpful Links"
grid). The copyright bar uses `bg-black/20` for subtle contrast and sits
flush at the bottom via `-mb-8`.

```blade
@if($mod && $mod->count('footer'))
  <section class="bg-[#0c4a44] border-t-[3px] border-primary text-[#cbd5e1]"
           aria-label="Additional information">
    <div class="max-w-7xl mx-auto px-4 py-8">
      {!! $mod->position('footer') !!}
    </div>
  </section>
@endif

<footer class="bg-[#0c4a44] text-[#cbd5e1]">
  <div class="max-w-7xl mx-auto px-4 py-8">
    <div class="... bg-black/20 -mx-4 px-4 py-4 mt-4 -mb-8">
      <p>&copy; {{ date('Y') }} Purdue University. All Rights Reserved.</p>
      <p>Powered by <a href="https://hubzero.org">Hubzero</a>&reg;, a Purdue project</p>
    </div>
  </div>
</footer>
```

#### Footer Module Content (mod_custom with BLADE separator)

The footer links module (`mod_custom`) stores both legacy and blade markup
in the database, separated by `<!-- BLADE -->`. The blade version uses
semantic markup styled by the template CSS:

```html
<!-- BLADE -->
<nav class="footer-links" aria-label="Helpful Links">
  <section>
    <h3>Get Help</h3>
    <ul>
      <li><a href="/feedback">Feedback</a></li>
      ...
    </ul>
  </section>
  <!-- more sections -->
</nav>
```

The `.footer-links` class is styled in `site.src.css` with a responsive
grid, teal links, and uppercase headings. The module outputs clean semantic
HTML — the template controls the visual appearance.

### site.js

Site-facing counterpart to admin.js. Provides theme persistence, mobile drawer
management, dropdown ARIA sync, auto-submit forms, and behavior initialization.
See [Client-Side Framework Detection](client-side-framework-detection.md) for
the behavior system and plugin CSS loading patterns.

Key features:
- **Theme toggle** — dark/light persistence via `localStorage`
- **Mobile drawer** — Escape key closes and returns focus to trigger
- **Navbar menus** — close others on open, outside click dismisses
- **Auto-submit** — `data-submit-on-change` on `<form>` elements
- **Dropdown ARIA** — `aria-expanded` sync on focus/blur
- **Behaviors** — tooltip, modal, caption, keepalive, highlight, math
  (all driven by `<meta name="behavior-*">` tags)
- **jQuery alias** — `window.jq` for legacy plugin compatibility

## Engine Preference (3-Tier)

The view engine preference controls whether `.blade.php` templates are used
for view layouts, module layouts, and the page shell. It defaults to `'legacy'`
(PHP `include`) and can be set to `'blade'` at three levels:

```
Global config  →  Component controller  →  Individual view
   (lowest)           (middle)              (highest priority)
```

Works on both the legacy system (:8443) and the Laravel system (:9443).
The standalone `Hubzero\View\Blade` renderer handles compilation in both
environments.

### Global (site-wide)

Set in `app/config/app.php`:

```php
'view_engine' => 'blade',  // 'legacy' (default) or 'blade'
```

When set to `'blade'`, all components, modules, and the page shell prefer
`.blade.php` templates where they exist, falling back to `.php` otherwise.

### Component (controller-level)

**Preferred: ComponentController base class.** Create a `ComponentController`
extending `SiteController` that declares both `$viewEngines` and
`$cssFrameworks`. All controllers in the component extend this instead of
`SiteController` directly:

```php
// site/controllers/componentcontroller.php
namespace Components\Citations\Site\Controllers;

class ComponentController extends \Hubzero\Component\SiteController
{
    protected $viewEngines = ['blade', 'php'];
    protected $cssFrameworks = ['daisyui', 'classic'];
}

// site/controllers/citations.php
class Citations extends ComponentController
{
    // All views automatically negotiate blade + daisyUI
}
```

The `$viewEngines` and `$cssFrameworks` properties are read by the
[View Engine & CSS Framework Negotiation](../../view-engine-css-framework.md)
system during `execute()`. The template's preferences are matched against
what the controller accepts — no per-method calls needed.

Components using this pattern: `com_citations`, `com_cart`.

**Alternative: per-execute override** (useful when only some controllers
in a component support blade):

```php
class Entries extends SiteController
{
    public function execute()
    {
        $this->setViewEngine('blade');  // All views in this controller
        parent::execute();
    }
}
```

### View (individual)

Override for a single view only:

```php
public function displayTask()
{
    $this->view->setEngine('blade');  // Just this view
    $this->view->set('rows', Entry::all())->display();
}

public function editTask()
{
    // Uses controller or global setting — no override here
    $this->view->set('row', $row)->display();
}
```

### Plugin Awareness

Plugins can check the effective engine to output matching markup:

```php
$engine = \Hubzero\Facades\Config::get('view_engine', 'legacy');

if ($engine === 'blade') {
    return '<div class="alert alert-info" role="status">' . $msg . '</div>';
} else {
    return '<p class="info">' . $msg . '</p>';
}
```

### From a Pure Laravel Controller

Pure Laravel controllers bypass the legacy dispatcher and extend the CMS
template layout directly via the registered `hubzero::` view namespace:

```blade
{{-- View for a pure Laravel route --}}
@extends('hubzero::index')

@section('content')
<section class="py-8">
  <div class="max-w-7xl mx-auto px-4">
    <h2 class="text-2xl font-bold mb-6">System Status</h2>
  </div>
</section>
@endsection
```

## Blade vs Legacy Comparison

| Feature | Legacy PHP Template | Blade Layout |
|---------|-------------------|--------------|
| File | `app/templates/hubzero/index.php` | `app/templates/hubzero/index.blade.php` |
| Module positions | `<jdoc:include type="modules" name="left" />` | `{!! app('hubzero.module')->position('left') !!}` |
| Head assets | `<jdoc:include type="head" />` | `@include('hubzero::partials.head')` |
| Messages | `<jdoc:include type="message" />` | `@include('hubzero::partials.messages')` |
| Component output | `<jdoc:include type="component" />` | `{!! $content !!}` or `@yield('content')` |
| Stylesheet | `css/index.css` (CSS custom properties) | `css/site.css` (Tailwind + daisyUI) |
| Extra styles | `Document::addStyleSheet()` | `@push('styles')` or `Document::addStyleSheet()` |
| Extra scripts | `Document::addScript()` | `@push('scripts')` or `Document::addScript()` |
| Template scope | `$this->` (renderer instance) | Blade variables, `app()` helpers |
| Body class | `<?php echo $this->option; ?>` | `{{ $option }}` |
| Breadcrumbs | `<jdoc:include type="modules" name="breadcrumbs" />` | `app('hubzero.pathway')->items()` |

## Page Header Convention

Every blade component view should output a `.page-header` as the first element.
The template CSS (`site.src.css`) controls the visual appearance — components
don't embed colors, padding, or border-radius.

```blade
<header class="page-header">
  <h1>{{ Lang::txt('COM_BLOG') }}</h1>
  <div class="page-header-actions">
    <a class="btn" href="{{ $feedUrl }}">{{ Lang::txt('COM_BLOG_FEED') }}</a>
  </div>
</header>
```

### Heading hierarchy

The `<h1>` lives in the page header. The site name in the navbar is **not** an
`<h1>`. This follows WCAG 2.4.6 (Headings and Labels):

```
h1  — Page title (inside .page-header)
├── h2  — Section headings, sidebar widget headings
│   └── h3  — Individual item titles
```

### Template-controlled styling

The `.page-header` class is styled in `site.src.css`. The default Hubzero
template renders it as a teal bar with rounded top corners (connecting to the
content area below):

```css
.page-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  height: 3.35rem;
  padding: 0 1.5em;
  background: #0d6d66;
  color: var(--color-primary-content);
  border-radius: 0.75rem 0.75rem 0 0;
  margin-bottom: 0;
}
```

Buttons inside the page header use `min-height: 0; height: auto` to override
daisyUI's default button height, keeping them compact within the fixed-height
bar.

A different template could override this with a flat header, different colors,
or no background at all. The component doesn't care — it just outputs the
semantic `.page-header` markup.

## CMS Component View Lifecycle

Understanding how HubZero components render views is essential for migrating
them to the Blade shell. This is the existing system that produces the HTML
the Blade layout wraps.

### Request Flow

```
URL → Router → ComponentDispatchController::dispatch()
  → Loader::render($option)
    → Component::start()
      → Controller::execute()
        → {task}Task()
          → View::display()
            → include tmpl/{layout}.php
              → echo HTML (captured by ob_start)
  → Template wraps output (legacy or Blade if opted in)
→ HTTP Response
```

### Component Directory Structure

```
core/components/com_blog/
├── site/
│   ├── Blog.php              ← Component entry point
│   ├── controllers/
│   │   └── entries.php        ← Controller (task methods)
│   └── views/
│       └── entries/
│           └── tmpl/
│               ├── default.php   ← List layout
│               ├── entry.php     ← Single item layout
│               ├── edit.php      ← Form layout
│               └── _comment.php  ← Partial (sub-view)
├── models/
│   └── entry.php
└── assets/
    ├── css/
    └── js/
```

### Task Routing

Controllers auto-discover tasks via reflection. Any public method ending in
`Task` is a routable task:

```php
class Entries extends SiteController
{
    public function execute()
    {
        $this->registerTask('new', 'edit');    // /blog/new → editTask()
        $this->registerTask('archive', 'display');
        parent::execute();
    }

    public function displayTask() { ... }  // /blog (default)
    public function entryTask() { ... }    // /blog/entry?alias=my-post
    public function editTask() { ... }     // /blog/edit?id=42
    public function saveTask() { ... }     // POST /blog/save
    public function deleteTask() { ... }   // POST /blog/delete
}
```

### View Data Passing

Controllers create a View and populate it with `set()`. Templates access data
as `$this->propertyName`:

```php
// Controller
public function displayTask()
{
    $filters = [
        'state'  => 1,
        'access' => User::getAuthorisedViewLevels(),
        'search' => Request::getString('search', ''),
    ];

    $this->view
        ->set('filters', $filters)
        ->set('config', $this->config)
        ->set('archive', $this->model)
        ->display();
}
```

```php
// views/entries/tmpl/default.php
<?php
$rows = $this->archive->entries($this->filters)
    ->ordered()
    ->paginated()
    ->rows();
?>
<?php foreach ($rows as $row) : ?>
  <h3><?php echo $this->escape($row->get('title')); ?></h3>
<?php endforeach; ?>
<?php echo $rows->pagination; ?>
```

### Automatic View Properties

Every view template has these set by the controller:

| Property | Value |
|----------|-------|
| `$this->option` | Component name (`com_blog`) |
| `$this->task` | Current task (`entry`) |
| `$this->controller` | Controller name (`entries`) |

### View Methods

| Method | Purpose |
|--------|---------|
| `$this->set($key, $value)` | Pass data to the template |
| `$this->display($tpl)` | Render and output the template |
| `$this->loadTemplate($tpl)` | Render and return as string |
| `$this->setLayout($layout)` | Switch layout (`'edit'` → `edit.php`) |
| `$this->escape($value)` | HTML-escape for safe output |
| `$this->css()` | Auto-load component CSS |
| `$this->js()` | Auto-load component JS |
| `$this->view('_partial')` | Create sub-view (partial) |

### Template Path Resolution

For controller `Entries` with task `entry`, the view system checks for Blade
templates first, then falls back to legacy PHP:

1. `{component}/site/views/entries/tmpl/entry.blade.php`
2. `{component}/site/views/entries/tmpl/entry.php`
3. `app/templates/hubzero/html/com_xxx/entries/entry.blade.php` (override)
4. `app/templates/hubzero/html/com_xxx/entries/entry.php` (override)
5. Falls back to `default.blade.php` or `default.php` if layout not found

**No opt-in required.** Drop a `.blade.php` file next to (or instead of) the
`.php` file and it will be used automatically. The same priority applies to
module layouts and plugin views.

### Common Task Patterns

**List/Browse**:
```php
public function displayTask()
{
    $filters = [/* from request */];
    $this->view->set('rows', Model::all($filters))->display();
}
```

**Single Item**:
```php
public function entryTask()
{
    $row = Model::oneOrFail(Request::getInt('id', 0));
    $this->view->set('row', $row)->setLayout('entry')->display();
}
```

**Edit Form**:
```php
public function editTask($row = null)
{
    if (!is_object($row)) {
        $row = Model::oneOrNew(Request::getInt('id', 0));
    }
    $this->view->set('row', $row)->setLayout('edit')->display();
}
```

**Save (POST)**:
```php
public function saveTask()
{
    Request::checkToken();
    $data = Request::getArray('entry', []);
    $row = Model::oneOrNew($data['id'])->set($data);

    if (!$row->save()) {
        $this->setError($row->getError());
        return $this->editTask($row);  // Re-render with errors
    }

    App::redirect(Route::url('index.php?option=' . $this->_option));
}
```

### Migrating a View to Blade

There are two independent things you can migrate, separately or together:

1. **View template** — the markup file (`default.php` → `default.blade.php`)
2. **Page shell** — the outer wrapper (`index.php` → `index.blade.php`)

#### Step 1: Convert the view template to Blade

Create a `.blade.php` file alongside (or instead of) the `.php` file. The view
system will pick it up automatically — no controller changes needed.

**Legacy** (`views/entries/tmpl/default.php`):
```php
<?php defined('_HZEXEC_') or die(); ?>
<h2><?php echo Lang::txt('COM_BLOG'); ?></h2>
<ul>
  <?php foreach ($this->rows as $row) : ?>
    <li>
      <a href="<?php echo Route::url($row->link()); ?>">
        <?php echo $this->escape($row->get('title')); ?>
      </a>
    </li>
  <?php endforeach; ?>
</ul>
```

**Blade** (`views/entries/tmpl/default.blade.php`):
```blade
<h2>{{ Lang::txt('COM_BLOG') }}</h2>
<ul>
  @foreach($rows as $row)
    <li>
      <a href="{{ Route::url($row->link()) }}">
        {{ e($row->get('title')) }}
      </a>
    </li>
  @endforeach
</ul>
```

Key differences in Blade templates:
- View properties become local variables: `$this->rows` → `$rows`
- `$this->escape()` → `{{ }}` (auto-escaped) or `e()`
- `$this->loadTemplate('item')` → `$__view->loadTemplate('item')`
- No `defined('_HZEXEC_') or die();` needed
- All Hubzero facades (`Lang`, `Route`, `User`, etc.) work normally

#### Step 2 (optional): Opt into the Blade page shell

To use the daisyUI page shell, enable Blade/daisyUI preferences on the site
controller:

```php
protected $viewEngines = ['blade', 'php'];
protected $cssFrameworks = ['daisyui', 'classic'];
```

That is the path used by the current site migrations. Older one-off
`template.engine` switching examples are historical and should not be used as
the default pattern.

#### Step 3: Update markup to daisyUI (optional)

If using the Blade page shell, update the view markup to use daisyUI classes:

```blade
{{-- views/entries/tmpl/default.blade.php --}}
<header class="flex items-center justify-between py-4 border-b border-base-300 mb-6">
  <h2 class="text-2xl font-bold">{{ Lang::txt('COM_BLOG') }}</h2>
  <a class="btn btn-primary" href="...">New Entry</a>
</header>

<div class="list bg-base-100 rounded-box shadow-sm">
  @foreach($rows as $row)
    <div class="list-row">
      <div class="list-col-grow">
        <a class="link link-hover font-medium"
           href="{{ Route::url($row->link()) }}">
          {{ e($row->get('title')) }}
        </a>
        <time class="text-xs text-base-content/50"
              datetime="{{ $row->published() }}">
          {{ $row->published('date') }}
        </time>
      </div>
      <span class="badge badge-success badge-sm">Published</span>
    </div>
  @endforeach
</div>
```

Each step is independent. Migrate one view at a time — other views in the
same component continue working unchanged.

## WCAG 2.2 AA Compliance

The Blade page shell is designed to satisfy WCAG 2.2 Level AA. This section
documents how each criterion is met in the template itself and what
`site.css` must include to complete compliance. See the full
[Accessibility Guide](accessibility.md) for component-level requirements.

### Landmarks (WCAG 1.3.1)

The template provides all required landmark regions. Screen readers use these
to navigate between page sections:

| Landmark | Element | `aria-label` |
|----------|---------|-------------|
| `banner` | `<header class="navbar">` | (implicit, one per page) |
| `navigation` | `<nav>` (main menu) | "Main menu" |
| `navigation` | `<nav>` (breadcrumb) | "Breadcrumb" |
| `navigation` | `<nav>` (mobile menu) | "Mobile menu" |
| `navigation` | `<nav>` (footer links) | "Footer links" |
| `search` | `<search>` | "Site search" |
| `complementary` | `<aside>` (sidebar) | "Sidebar" |
| `main` | `<main id="main-content">` | (implicit, one per page) |
| `contentinfo` | `<footer class="footer">` | (implicit, one per page) |

Every `<nav>` has a unique `aria-label` so screen readers can distinguish them
in the landmarks list. All labels use `Lang::txt()` for translation.

### Skip Navigation (WCAG 2.4.1)

The first focusable element is a skip link targeting `#main-content`. It is
visually hidden via `sr-only` and revealed on focus via `focus:not-sr-only`.
The `<main>` element has `tabindex="-1"` so it receives programmatic focus
when the skip link is activated.

### Page Title (WCAG 2.4.2)

`<title>` receives the document title from the component or falls back to the
site name. Format: "Page Title — Hubzero" (set by the component via the
Document service).

### Language (WCAG 3.1.1)

`<html lang="...">` is set from `App::get('language')`, defaulting to `en`.

### Focus Visibility (WCAG 2.4.7, 2.4.11, 2.4.13)

`site.css` must include a global focus ring that is never overridden:

```css
/* Global focus indicator — WCAG 2.4.7 */
:focus-visible {
  outline: 2px solid oklch(var(--p));  /* daisyUI primary */
  outline-offset: 2px;
}

/* Prevent focus from hiding behind sticky header — WCAG 2.4.11 */
:target,
[tabindex]:focus-visible {
  scroll-margin-top: 5rem;
}

/* Focus appearance: area ≥ 2px perimeter — WCAG 2.4.13 */
/* The 2px outline + 2px offset satisfies this automatically */
```

### Keyboard Navigation (WCAG 2.1.1, 2.1.2)

All interactive elements are operable via keyboard:

| Element | Keyboard Interaction |
|---------|---------------------|
| Skip link | `Tab` to focus, `Enter` to jump to main |
| Main menu links | `Tab` / `Shift+Tab` between items |
| Theme toggle | `Tab` to focus, `Space` to toggle |
| User dropdown | `Tab` / `Enter` to open, `Escape` to close |
| Mobile drawer | Button opens, `Escape` closes + returns focus to trigger |
| Breadcrumb links | `Tab` between items |
| Footer links | `Tab` between items |

The template includes JavaScript for:
- `Escape` key closes the mobile drawer and returns focus to the trigger
  button (WCAG 2.4.3: Focus Order)
- `aria-expanded` is synced on the user dropdown and mobile menu toggle

**No keyboard traps** (WCAG 2.1.2): every interactive element can be tabbed
away from. The mobile drawer overlay is clickable to close, and `Escape`
provides a keyboard equivalent.

### Target Size (WCAG 2.5.8)

All interactive targets in the shell meet the 24x24px minimum. The navbar
buttons use `btn-circle` (44x44px). `site.css` should enforce the global
minimum:

```css
/* WCAG 2.5.8: Minimum target size */
.btn, .menu a, .tab, .link,
input[type="checkbox"], input[type="radio"] {
  min-height: 2.75rem;  /* 44px — exceeds 24px minimum */
  min-width: 2.75rem;
}
```

### Color and Contrast (WCAG 1.4.3, 1.4.11)

The daisyUI theme defines contrast-tested color pairs (see
[Tailwind + daisyUI Theming](tailwind-theming.md)). The template enforces:

- `bg-base-100` + `text-base-content` for body (14.7:1 on white)
- `bg-base-200` + `text-base-content` for footer and dropdowns
- `btn-primary` uses `primary-content` for text (theme-tested 4.5:1+)
- `ring-primary` on avatar provides 3:1 non-text contrast (WCAG 1.4.11)

Both the `hubzero` (light) and `hubzero-dark` themes must pass all contrast
ratios. Verify with [WebAIM Contrast Checker](https://webaim.org/resources/contrastchecker/).

### Non-text Content (WCAG 1.1.1)

- Avatar `<img>` uses `alt=""` (decorative — user name is in adjacent text)
- SVG icons use `aria-hidden="true"` (decorative — purpose conveyed by
  `aria-label` on the parent control)
- Hamburger menu icon uses `aria-hidden="true"` with `aria-label="Open menu"`
  on the button

### Status Messages (WCAG 4.1.3)

The messages partial applies:
- `role="alert"` for error and warning messages (assertive — interrupts)
- `role="status"` for info and success messages (polite — announced at pause)

### Text Resize (WCAG 1.4.4)

The viewport meta allows user zoom (`initial-scale=1`, no `maximum-scale`).
All layout uses relative units (`rem`, Tailwind spacing scale). The sidebar
grid collapses to single column at narrow widths via `lg:grid`.

### Reduced Motion (WCAG 2.3.3)

`site.css` must include the reduced-motion media query:

```css
@media (prefers-reduced-motion: reduce) {
  *, *::before, *::after {
    animation-duration: 0.01ms !important;
    animation-iteration-count: 1 !important;
    transition-duration: 0.01ms !important;
    scroll-behavior: auto !important;
  }
}
```

The mobile drawer slide-in and dropdown animations are suppressed for users
who prefer reduced motion. Tailwind's `motion-safe:` / `motion-reduce:`
variants can be used in component views.

### Content on Hover or Focus (WCAG 1.4.13)

The user dropdown and mobile drawer are dismissible:
- **Escape key** closes both (via JS in the template)
- **Click outside** closes both (drawer overlay, dropdown blur)
- Content remains visible while the trigger has focus (daisyUI `dropdown`
  behavior)

### User Dropdown ARIA Pattern

The user dropdown follows the
[WAI-ARIA Menu Button pattern](https://www.w3.org/WAI/ARIA/apd/patterns/menubutton/):

```html
<button aria-haspopup="true" aria-expanded="false" aria-label="User menu">
<ul role="menu" aria-label="User menu">
  <li role="none"><a role="menuitem">...</a></li>
  <li role="separator" aria-hidden="true"></li>
</ul>
```

- Trigger is a `<button>` (not `<div tabindex="0">`) for native keyboard
  support
- `aria-haspopup="true"` tells AT a menu will appear
- `aria-expanded` is toggled by JavaScript on focus/blur
- Menu items use `role="menuitem"` with `role="none"` on the `<li>` wrapper
- The divider uses `role="separator"` and `aria-hidden="true"`

### Mobile Drawer ARIA Pattern

The drawer follows the
[WAI-ARIA Dialog (Modal) pattern](https://www.w3.org/WAI/ARIA/apd/patterns/dialog-modal/):

```html
<button aria-expanded="false" aria-controls="mobile-drawer-panel">
<div role="dialog" aria-modal="true" aria-label="Mobile menu">
```

- Trigger button has `aria-expanded` and `aria-controls`
- Panel has `role="dialog"` and `aria-modal="true"`
- `Escape` closes and returns focus to the trigger (WCAG 2.4.3)
- The checkbox input driving the drawer is `aria-hidden="true"` (it's a
  CSS-only mechanism, not a user-facing control)

### Breadcrumb Current Location (WCAG 2.4.8)

The last breadcrumb item uses `aria-current="location"` to indicate the
current page. It renders as plain text (not a link) since you're already there.

### Internationalization

All visible text in the template shell uses `Lang::txt()` with English
defaults. The language keys (e.g., `TPL_HUBZERO_SKIP_TO_CONTENT`,
`TPL_HUBZERO_MAIN_MENU`) are defined in the template's language file at
`app/templates/hubzero/language/en-GB/en-GB.tpl_hubzero.ini`.

### site.css Required Styles

These styles must be included in `site.css` (in addition to the Tailwind +
daisyUI output) for full WCAG compliance:

```css
/* WCAG 2.4.1: Skip link — hidden until focused */
.skip-link {
  position: absolute;
  top: -100%;
  left: 1rem;
  z-index: 50;
  padding: 0.5rem 1rem;
  background: oklch(var(--b1));
  color: oklch(var(--bc));
  border-radius: var(--rounded-btn, 0.5rem);
  box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1);
  outline: 2px solid oklch(var(--p));
  outline-offset: 2px;
}
.skip-link:focus {
  top: 1rem;
}

/* WCAG 2.4.7: Focus visibility */
:focus-visible {
  outline: 2px solid oklch(var(--p));
  outline-offset: 2px;
}

/* WCAG 2.4.11: Focus not obscured by sticky elements */
:target,
[tabindex]:focus-visible {
  scroll-margin-top: 5rem;
}

/* WCAG 2.5.8: Minimum target size */
.btn, .menu a, .tab, .link,
input[type="checkbox"], input[type="radio"] {
  min-height: 2.75rem;
  min-width: 2.75rem;
}

/* WCAG 2.3.3: Respect reduced motion preference */
@media (prefers-reduced-motion: reduce) {
  *, *::before, *::after {
    animation-duration: 0.01ms !important;
    animation-iteration-count: 1 !important;
    transition-duration: 0.01ms !important;
    scroll-behavior: auto !important;
  }
}
```

### WCAG Compliance Summary

| Criterion | Requirement | How the Template Satisfies It |
|-----------|-------------|-------------------------------|
| 1.1.1 | Non-text content | `alt=""` on decorative images, `aria-hidden` on icons |
| 1.3.1 | Info and relationships | Semantic landmarks, headings, ARIA roles |
| 1.3.5 | Input purpose | `autocomplete` on search (module responsibility) |
| 1.4.1 | Color not sole indicator | Badges use text + color (component responsibility) |
| 1.4.3 | Contrast ≥ 4.5:1 | daisyUI theme colors, verified pairs |
| 1.4.4 | Text resizable | Relative units, no max-scale, responsive grid |
| 1.4.11 | Non-text contrast ≥ 3:1 | Ring on avatar, focus outline, button borders |
| 1.4.13 | Content on hover/focus | Escape dismisses dropdown/drawer |
| 2.1.1 | Keyboard operable | All controls keyboard accessible |
| 2.1.2 | No keyboard trap | Every element can be tabbed away from |
| 2.3.3 | Reduced motion | `prefers-reduced-motion` in site.css |
| 2.4.1 | Skip navigation | Skip link → `#main-content` |
| 2.4.2 | Page titled | `<title>` from Document service |
| 2.4.3 | Focus order | Logical DOM order, focus return on close |
| 2.4.7 | Focus visible | `:focus-visible` outline in site.css |
| 2.4.8 | Location | `aria-current="location"` on breadcrumb |
| 2.4.11 | Focus not obscured | `scroll-margin-top` in site.css |
| 2.5.8 | Target size ≥ 24×24px | `min-height: 2.75rem` on interactive elements |
| 3.1.1 | Page language | `<html lang="...">` |
| 4.1.2 | Name, role, value | ARIA attributes on dropdown, drawer, toggle |
| 4.1.3 | Status messages | `role="alert"` / `role="status"` on flash messages |
