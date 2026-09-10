<!--
status: rewritten
reviewed-against: 2.4-main @ ab49f763b0
reviewed: 2026-09-09
screenshots: none
source: https://help.hubzero.org/documentation/240/webdevs/index/browsersupport
source-id: 3427
modified: 2012-04-09
-->
# Browser support

Nothing in this repository enforces a browser support policy. There is no
browserslist, no Babel, no PostCSS, and — with one exception — no build step
that targets a language level. What the CMS runs in is decided by the
libraries it ships and by the CSS and JavaScript its templates were written
in. This page says what those actually require.

The page this replaces carried a table of supported versions written in
2012: Internet Explorer 7 through 9, Firefox 3 to 5, Safari 5, Android 2
WebKit, Opera 10. None of it is meaningful now. The code in this tree cannot
run in any of those browsers, and no part of the repository checks.

## What the repository does not contain

Each of these was looked for and is not present anywhere outside
`core/vendor/` and vendored third-party libraries:

| Looked for | Result |
|---|---|
| `.browserslistrc` or a `browserslist` key | None |
| Babel configuration | None |
| PostCSS or Autoprefixer configuration | None |
| A JavaScript bundler for the CMS | None. The only root `package.json` dependency is `blade-formatter` |
| A build target | One, and only for the CKEditor 5 editor plugin — see below |
| A polyfill loaded on every page | None |

CSS is compiled from LESS by `core/bin/lessc` and by `muse cache:css`, and
LESS does no prefixing and no transformation for older engines. The CMS's own
JavaScript is shipped as written; nothing transpiles it.

The exception is
[`core/plugins/editors/ckeditor5/build/`](../../../core/plugins/editors/ckeditor5/build),
which bundles CKEditor 5 with esbuild. Its `build.mjs` sets
`target: ['es2020']`, and that is the only browser-facing language level
declared anywhere in the repository. The bundle it produces,
`core/plugins/editors/ckeditor5/assets/js/ckeditor.js`, is committed; the
build is not run as part of any workflow.

The CI workflows in `.github/workflows/` run PHP syntax and facade-import
lint and build the documentation. None of them starts a browser. The root
`README.md` says the project is tested with BrowserStack; that sentence is
the only mention of BrowserStack in the tree, and no configuration, device
list, or browser test suite goes with it.

## What the shipped code actually requires

The floor is set by what the framework loads, not by a policy:

| What ships | Where | What it needs |
|---|---|---|
| jQuery 3.3.1 | `core/assets/js/jquery.js` | jQuery 3 dropped Internet Explorer 6-8 |
| Bootstrap 5.3.3 | `core/assets/css/bootstrap/5.3.3/`, `core/assets/js/bootstrap/5.3.3/` | Bootstrap 5 dropped Internet Explorer entirely and is built on CSS custom properties, flexbox, and grid |
| htmx 2.0.4 | `core/assets/js/htmx/` | htmx 2 dropped Internet Explorer |
| Alpine.js 3.14.8 | `core/assets/js/alpine/` | Alpine 3 is built on the ES6 `Proxy`, which cannot be polyfilled |
| CKEditor 5 | `core/plugins/editors/ckeditor5/assets/js/ckeditor.js` | Built to ES2020 |

Bootstrap, htmx, and Alpine are opt-in: an extension asks for them through
[`Behavior`](../../../core/libraries/Hubzero/Html/Builder/Behavior.php) —
`Behavior::bootstrap()`, `Behavior::htmx()`, `Behavior::alpinejs()`,
`Behavior::htmxalpine()`. jQuery comes in with `Behavior::framework()` and is
on nearly every page.

The framework's own JavaScript is ES6 in places. `core/assets/js/htmx/hubzero-bootstrap.js`
uses `const` and `"use strict"` at the top level. The stylesheets use flexbox
in dozens of files across `core/templates/` and `core/components/`, and CSS
custom properties in a handful.

Taken together the practical floor is a current evergreen browser: Chrome,
Firefox, Safari, or Edge in a version from roughly the last few years. The
one explicit number in the tree, the editor bundle's ES2020, puts that floor
at about 2020 for any page with an editor on it. None of this is a policy
anyone wrote down; it is what the dependencies impose.

## The Internet Explorer leftovers

Three shipped templates still carry conditional comments that no browser has
honoured since Internet Explorer 10 dropped support for them:

- `core/templates/kimera/index.php` loads `js/html5.js` and
  `css/browser/ie8.css` for `lt IE 9`, and `css/browser/ie9.css` for `IE 9`.
- `core/templates/kameleon/index.php` loads `js/html5.js` for `lt IE 9` and
  `css/browser/ie9.css` for `IE 9`. It ships no `ie8.css`.
- `core/templates/lucent/component.php` loads `js/html5.js` for `lt IE 9`, and
  that file does not exist in the lucent template. The reference is dead, but
  it is inside a conditional comment, so nothing ever requests it.

`core/templates/system/email.php` has an `IEMobile 7` block that is commented
out in PHP as well.

Two other legacy shims are still in the tree and are not loaded by anything on
a normal page: `core/assets/js/excanvas/` (the `<canvas>` shim for old
Internet Explorer) and `core/components/com_dataviewer/site/html/modernizr.js`.

None of this does any harm. None of it does any good either. Treat it as dead
weight, not as evidence of a support target.

## Browser detection, and what it is used for

The framework has a user-agent parser,
[`Hubzero\Browser\Detector`](../../../core/libraries/Hubzero/Browser/Detector.php).
It recognises about forty browser names, including several — Palm, Avantgo,
Xiino, imode, HotJava — that have not existed for a long time.

In the templates it is cosmetic. `kimera`, `kameleon`, and `lucent` each build
a class list for the `<html>` element from `$browser->name()` and
`$browser->name() . $browser->major()`, alongside the text direction and, in
the first two, a no-script flag:

<!--include: core/templates/kimera/index.php:32-42-->

Nothing keys off those classes to withhold a feature. The no-script flag is
the one piece of progressive enhancement the templates actually implement,
and each spells it differently: kimera sets `no-js` and removes it in
`js/hub.js`, kameleon sets `nojs` and removes it in `js/index.js`, and
lucent sets neither.

> **Note:** The `Edge` pattern in `Detector` matches `Edge/`, the legacy
> EdgeHTML user agent. Current Edge sends `Edg/` and falls through to the
> `Chrome/` pattern, so it is reported as Chrome. The `<html>` class on an
> Edge page therefore reads `chrome`.

## The one real gate

`core/plugins/tools/novnc/novnc.php` is the only place in the tree that
refuses to render for a browser. Its `canRender()` method reads a **Minimum
OS/Browsers** parameter — one `OS, BROWSER MAJOR.MINOR` line each — and a
list of user-agent regular expressions to reject. Its shipped default is from
around 2014:

```text
*, safari 5.1
*, chrome 27.0
*, iceweasel 38.0
*, firefox 30.0
*, opera 23.0
*, mozilla 5.0
iOS, safari 1.0
Windows, msie 10.0
Windows, ie 10.0
```

These are minimums, so any current version of the browsers named passes. But
a browser whose name does not appear in the list at all fails the check, and
the plugin does not render. Vivaldi and Yandex are recognised by `Detector`
under their own names and are not in the default list. See
It is recorded with the project.
## Writing front-end code

Since nothing enforces a target, the burden is on what you write:

- Assume an evergreen browser. Everything the CMS already loads does.
- Build on working HTML. kimera and kameleon put a no-script class on
  `<html>` and remove it once script runs; use it if a feature needs
  JavaScript to be usable, and check which name your template uses.
- Do not add conditional comments, `excanvas`, `html5shiv`, or Modernizr. The
  copies already in the tree are historic.
- If you need a version floor for a specific feature, feature-detect it in
  JavaScript or use an `@supports` rule in CSS. Do not use `Detector`; it
  parses a string the browser is free to lie about, and it is not maintained
  against current user agents.
- Progressive enhancement — a usable page first, richness layered on — is
  still the right approach, and is the one idea worth keeping from the
  "graded browser support" material this page used to reproduce. That
  material was Yahoo!'s, written in the mid-2000s, and described a testing
  programme Hubzero does not run.

## Next

[Development environment](06-devenvironment.md) covers getting a hub to develop
against.
