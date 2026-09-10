<!--
status: rewritten
reviewed-against: 2.4-main @ 9924bea2ec
reviewed: 2026-09-10
screenshots: none
-->
# Accessibility

What the shipped templates and the framework's HTML helpers actually emit, so
you know which parts of a page you control and which parts arrive already
built. This chapter is for someone writing a template or a component view
layout. It is a description of the current state of this tree, not a checklist
and not a claim about any standard: nothing in this repository asserts
conformance with WCAG, Section 508 or anything else, and neither does this
page.

## What the shipped templates do

The three layouts worth reading are
[`kimera/index.php`](../../../core/templates/kimera/index.php),
[`lucent/index.php`](../../../core/templates/lucent/index.php) and
[`kameleon/index.php`](../../../core/templates/kameleon/index.php).

| | `kimera` | `lucent` | `kameleon` |
|---|---|---|---|
| `lang` and `dir` on `<html>` | yes | yes | yes |
| Skip link | **no** | yes, to `#maincontent` | **no** |
| `<main>` element | `<main id="content">` | `<main id="maincontent">` | `<main id="wrap">` |
| `<header>` | yes | yes | yes, with `role="banner"` |
| `<footer>` | yes | none in the layout | yes |
| Labelled `<nav>` | main menu only | primary, mobile, plus an unlabelled `<nav class="nav">` wrapper and `<nav class="subnav">` | main and component menus |
| `<aside>` for module positions | yes | yes | — |
| Menu button pattern | none | `aria-haspopup`, `aria-controls`, `role="menu"`/`menuitem`/`none` | none |
| Search dialog | none | `role="dialog"`, `aria-modal`, `aria-labelledby` | none |

Everything in that table is in the layout files themselves. The `role="banner"`
and `role="navigation"` attributes in `kameleon` duplicate what `<header>` and
`<nav>` already convey; they are harmless and you do not need to copy them.

`lucent` is the only shipped template with a skip link, and it is the first
thing in the body:

<!--include: core/templates/lucent/index.php:61-62-->

### The skip link never becomes visible

`.vh` is `lucent`'s visually-hidden utility, and it has no focused state:

<!--include: core/templates/lucent/less/core/_core.less:1-11-->

There is no `.vh:focus` or `.vh:focus-within` rule anywhere in
`core/templates/lucent/less`, so the link stays clipped to one pixel even while
it holds focus. A screen reader announces it; a sighted keyboard user tabs onto
something they cannot see. If you copy `lucent`, add a rule that restores the
link on focus, or give the skip link its own class.

`kimera` and `kameleon` have no skip link at all. `kimera`'s `<main>` carries
`id="content"`, so adding one is a single line.

### Headings

Both site templates put the page's only structural heading in the masthead.
`kimera` wraps the site name in `<h1>`; `kameleon` does the same in the
administrator. On every page. Component output therefore starts at `<h2>` or
lower, and no heading describes what the page is about.

`lucent` goes the other way: its logo is a `<div class="logo">` holding a plain
link, so the shell contributes no heading at all and a `lucent` page has an
`<h1>` only if the component supplies one.

Neither arrangement is fixed for you. Decide which one your template uses and
make it consistent, because component views are written against no assumption
either way.

### Untranslated strings in `lucent`

`lucent` hardcodes English in places where the accessible name is the only
thing a screen reader user gets: `Skip to main content`,
`aria-label="Primary navigation"`, `aria-label="Mobile navigation"`,
`aria-label="search popup"`, the `<h2 class="vh">Search</h2>` inside the search
dialog, `<span class="vh">Search</span>` on the search trigger, and
`<span>Menu</span>` on the mobile button. `error.php` also hardcodes
`close search`, where `index.php` uses `Lang::txt('TPL_SEARCH_CLOSE')` for the
same button.

The logo link's text is the literal word `Lucent`, not `Config::get('sitename')`,
so on a hub using this template the link home announces the template's name.

Run every one of these through `Lang::txt()` in your own template. See
[Languages](02-languages.md).

## What the framework emits for you

A template author does not control the markup below. It comes out of
[`Hubzero\Html`](../../../core/libraries/Hubzero/Html) and out of the document
renderers, and it sets the floor for anything built on top of it.

### Pagination

[`core/libraries/Hubzero/Pagination/Views/paginator.php`](../../../core/libraries/Hubzero/Pagination/Views/paginator.php)
is the best-behaved helper in the tree. It wraps the list in
`<nav class="pagination" aria-label="Pagination">`, marks the current page with
`<strong aria-current="page">`, hides the `…` separators with `aria-hidden`,
and gives the *Display #* select a real `<label for>`.

Two things to know:

- The **site** branch labels its arrows:

  <!--include: core/libraries/Hubzero/Pagination/Views/paginator.php:105-111-->

  The strings are passed to `Lang::txt()` as literal English — `'Previous page'`
  and `'Next page'` — and no such keys exist in any `en-GB.ini`, so `Lang::txt()`
  returns them unchanged and they cannot be translated. The *Go* button next to
  the limit select is not passed through `Lang::txt()` at all.
- The **administrator** branch emits `<a>` elements with `data-prefix` and
  `data-start` and **no `href`**. An anchor without `href` is not focusable and
  is not a link to assistive technology, so admin pagination cannot be reached
  by keyboard. The click handler is `Hubzero.paginate()` at the bottom of
  `core.js`. Admin pagination also has no `aria-label` on its previous/next
  controls.

The limit select in the administrator carries `onchange="this.form.submit()"`,
which submits the form on every value change — including the changes a keyboard
user makes while arrowing through the options.

### The grid helpers

[`Hubzero\Html\Builder\Grid`](../../../core/libraries/Hubzero/Html/Builder/Grid.php)
builds admin list tables. What it emits:

- `Grid::sort()` returns `<a href="#" data-order="…" class="grid-order sort">`
  with the column title as the link text and
  `title="Click to sort by this column"`. There is no `aria-sort` on the header
  cell and nothing tells a screen reader which column is sorted or in which
  direction — the current direction is carried in a CSS class.
- `Grid::id()` builds the row checkbox:

  <!--include: core/libraries/Hubzero/Html/Builder/Grid.php:104-112-->

  The `<label>` text is the **record's database id**, and the useful string
  ("Checkbox for row 3", from `JGRID_CHECKBOX_ROW_N`) is in the `title`
  attribute, which the label overrides as the accessible name. The label's
  classes are `sr-only visually-hidden`; `.sr-only` is defined in
  `core/templates/kameleon/css/index.css` and nowhere else, so if this helper is
  ever used on the site the record id renders as visible text.
- `Grid::boolean()`, `Grid::state()`, `Grid::published()`, `Grid::orderUp()`,
  `Grid::orderDown()` and `Grid::action()` all return `<a href="#toggle">` or
  `<a href="#">` carrying a `data-task`. They are focusable and they do carry
  real text in a `<span class="text">`, but they are links that behave as
  buttons: they have no `role="button"` and so do not respond to the space bar.
  When `$enabled` is false, `Grid::action()` drops the `href` and the
  `data-task` but keeps the `<a>`, the class and the `title` — so a disabled
  control leaves the tab order silently, with nothing saying it is disabled.

### The toolbar

[`Hubzero\Html\Toolbar`](../../../core/libraries/Hubzero/Html/Toolbar.php)
renders `<div class="toolbar-list"><ul>` and each button as
`<li class="button">`. Inside, every button type — `Standard`, `Confirm`,
`Link`, `Popup`, `Help` — is an `<a href="#">` (or a real URL, for `Link` and
`Popup`) whose text is inside a `<span>` carrying the Fontcons icon class.

So the toolbar's labels are real text, which is the important part. What is
missing is the same thing the grid helpers are missing: these are controls, not
links, and they carry no `role="button"`. `Confirm` uses a native `confirm()`
dialog; `Popup` uses `window.open`. Neither returns focus anywhere when it
closes.

### Form labels

[`Hubzero\Form\Field::getLabel()`](../../../core/libraries/Hubzero/Form/Field.php)
emits `<label id="{id}-lbl" for="{id}">`, correctly associated, with the label
text translated. A required field appends
`<span class="required star">Required</span>` **inside** the label, so the word
is part of the accessible name. That is the good news.

The field's `description` becomes a `title` attribute on the *label*, in the
form `Label::Description`, for the tooltip behaviour to split. It is never
attached to the control with `aria-describedby`.

And `required="true"` in a form XML file changes only the label. Look at
[`Fields/Text.php`](../../../core/libraries/Hubzero/Form/Fields/Text.php): the
attribute list it builds is `type`, `value`, `name`, `id`, `size`, `maxlength`,
`class`, `autocomplete`, `readonly`, `disabled`, `onchange`. No `required`, no
`aria-required`. If your view depends on a required field being announced as
required, add the attribute yourself.

### Tooltips

`Html::behavior('tooltip')` binds jQuery UI tooltips to `.hasTip` and rewrites
the `title` attribute on creation:

<!--include: core/libraries/Hubzero/Html/Builder/Behavior.php:568-578-->

After that runs, the element's `title` attribute contains a string of HTML tags.
`title` is what assistive technology falls back to for an element's description,
so the description it has is that markup. Where the behaviour is *not* loaded —
which is most of the site — the `Label::Description` string from `getLabel()`
stays in the `title` verbatim, double colon and all.

Prefer a visible `<p class="hint" id="…">` and an `aria-describedby` on the
control over a `.hasTip` tooltip in any view you write.

### Icons

`Html::asset('icon', 'edit')` inlines an SVG from
[`core/assets/icons`](../../../core/assets/icons) wrapped in
`<span class="icn icn-edit" aria-hidden="true" focusable="false">`. This one is
right by default — the glyph is hidden and you supply the text. It is the safest
of the three icon systems for that reason. See
[Fontcons](12-fontcons.md#the-alternative-svg-icons).

### The content sanitizer

[`Hubzero\Utility\Sanitize`](../../../core/libraries/Hubzero/Utility/Sanitize.php)
runs user-submitted HTML through HTML Purifier, and its whitelist explicitly
keeps ARIA:

<!--include: core/libraries/Hubzero/Utility/Sanitize.php:423-436-->

So `role`, `aria-label`, `aria-labelledby` and `aria-describedby` survive on
`a`, `div`, `span`, `img`, `table`, `ul`, `ol`, `li`, `nav`, `section` and
`button`. Nothing else does — `aria-hidden`, `aria-live`, `aria-expanded`,
`aria-current` and `tabindex` are all stripped from sanitized content.

## Messages and notifications

System messages reach the page two ways, and the two do not produce the same
markup.

On a normal request, `<jdoc:include type="message" />` calls
[`Hubzero\Document\Type\Html\Message`](../../../core/libraries/Hubzero/Document/Type/Html/Message.php):

<!--include: core/libraries/Hubzero/Document/Type/Html/Message.php:52-76-->

A `<div id="system-message-container">` holding a `<dl id="system-message">`,
one `<dt>` per message type with the type name translated, and the messages in a
`<ul>` inside the matching `<dd>`. There is no `role`, no `aria-live` and no
heading. The container is always emitted, even with nothing in it.

After the page has loaded, `Hubzero.renderMessages()` in
[`core-uncompressed.js`](../../../core/assets/js/core-uncompressed.js) builds the
same shape in JavaScript:

<!--include: core/assets/js/core-uncompressed.js:156-186-->

Two differences matter:

- The JavaScript version sets `role="alert"`. The server-rendered version does
  not. So a message that was on the page when it loaded is not in a live region,
  and a message inserted afterwards is — which is backwards from what is useful.
- The `<dl>` is created detached, filled, given `role="alert"`, and only then
  appended. A live region has to be in the document *before* its contents change
  for the change to be announced reliably; inserting an already-populated region
  is the pattern that most often stays silent.

The `<dt>` text also differs: the PHP renderer runs the type through the
language object, so it reads **Error** or **Warning**; the JavaScript renderer
calls `.html(type)` on the raw key, so it reads `error`.

`Hubzero.removeMessages()` empties `#system-message-container` completely, so
there is no persistent region left behind between updates.

None of the shipped templates wrap the message container in anything, and none
move focus to it. `kimera` and `lucent` render it only when
`$this->getBuffer('message')` is non-empty, which means on those templates the
container is not even in the DOM until a message exists.

## Where the gaps are

Stated plainly, from the code above:

- `kimera` and `kameleon` have no skip link. `lucent` has one that never becomes
  visible on focus.
- No shipped template moves focus after a dynamic update, and nothing in
  `core.js` does either. `renderMessages`, `submitform`, `filterClear`,
  `gridOrder` and `listItemTask` all change or replace content and leave focus
  where it was.
- The server-rendered message block is not a live region. The one built in
  JavaScript is, but is populated before insertion.
- Admin pagination links have no `href`, so they are not keyboard-reachable.
- Admin sortable column headers carry no `aria-sort`.
- Toolbar and grid controls are `<a href="#">` acting as buttons, without
  `role="button"`.
- Form field descriptions are `title` attributes, not `aria-describedby`, and
  the tooltip behaviour replaces those attributes with HTML.
- `required="true"` on a form field produces no `required` or `aria-required`
  attribute on the control.
- `lucent`'s accessible names are hardcoded English.
- Only one `aria-live` region exists in the whole of `core/`: the upload list in
  [`com_support`'s new-ticket view](../../../core/components/com_support/site/views/tickets/tmpl/new.php).

One convention does exist and is worth following.
[`Behavior.php`](../../../core/libraries/Hubzero/Html/Builder/Behavior.php)
marks its accessibility-motivated lines with an `[a11y]` comment, next to the
MathJax configuration that enables `AssistiveMML.js` and hides the visual glyph
spans. Follow that convention if you change something for this reason; it makes
the change findable.

## What you can do from a template

### Fix the framework's markup with an override

You cannot change what `Grid::sort()` returns, but you rarely have to call it.
An [output override](09-overrides.md) at
`app/templates/mytemplate/html/{extension}/{view}/{layout}.php` replaces the
whole layout, so you can write the header cell yourself:

```php
<th scope="col" aria-sort="<?php echo $sorted ? $dir : 'none'; ?>">
	<a href="<?php echo $sortUrl; ?>"><?php echo Lang::txt('COM_EXAMPLE_TITLE'); ?></a>
</th>
```

Pagination is harder, and the difference is worth knowing. There is no template
override path for it. `Paginator::render()` accepts an optional
`Hubzero\Pagination\View` — or an array of view config, which can carry a
`base_path` or `template_path` — and only builds the default view when it is
given neither. So replacing the markup means the *calling code* passes a view;
a template on its own cannot. Everything that calls `render()` with no argument
gets `Views/paginator.php` exactly as it ships.

Overriding is also how you deal with `.hasTip`. If a view emits one and you want
a described control instead, override the layout rather than trying to patch the
tooltip behaviour globally.

### Keep icon glyphs out of the accessibility tree

The three icon systems fail differently, and two of them fail on you.

**Fontcons** puts the glyph in a `:before` pseudo-element on an `.icon-*` class.
A pseudo-element is not read by a screen reader, so a control with only a class
and no text has no accessible name at all. Always keep the text:

```html
<a class="icon-edit" href="…">Edit</a>
```

If the design shows the icon alone, hide the text with your template's
visually-hidden class rather than deleting it — and make sure that class exists
in your stylesheet. `lucent` has `.vh`; `kameleon` has `.sr-only`; `kimera` has
neither. See [Fontcons](12-fontcons.md).

**Socicons** is worse, because it has no stylesheet at all: every consumer
declares its own `@font-face` and picks codepoints by hand. The three share
plugins that use it get the pattern right — the class goes on a wrapper and the
service name stays as text:

```html
<a href="…" title="Share on Twitter" class="popup" rel="external">
	<span class="share_twitter"><span>Twitter</span></span>
</a>
```

Do the same. A social logo with no text is a blank to a screen reader and a
blank on screen if the font fails to load. See [Socicons](11-socicons.md).

**The SVG icons** are already handled: `Html::asset('icon', …)` sets
`aria-hidden="true" focusable="false"` on the wrapper. `focusable="false"`
matters because older Internet Explorer put inline SVG in the tab order.

`lucent` inlines its search SVG directly in the layout, with neither attribute,
and pairs it with `<span class="vh">Search</span>`. The visible text is the
right half of that; the SVG should carry `aria-hidden="true"` as well.

### The data-attribute JavaScript and the keyboard

[JavaScript](08-javascript.md#the-data-attribute-hooks) explains the pattern
`core.js` uses: bind by class on `DOMContentLoaded`, read `data-` attributes off
the element. Keep using it — it is the right pattern, and it is what survives the
[`System - CSP`](../../../core/plugins/system/csp/csp.php) plugin. But it binds
`click` and nothing else, which has consequences:

- **Put the handler on an element that is already a control.** A `click` handler
  fires from the keyboard only on natively activatable elements. `<button>` is
  the right choice; `<a href="…">` works but only responds to Enter. A `<div>`
  or `<span>` with a `data-task` is unreachable by keyboard, full stop.
- **`href="#"` is not a destination.** Every framework helper uses it, so a
  keyboard user tabbing through an admin list hears "link" over and over for
  things that are buttons. In markup you write, use `<button type="button">`.
- **Nothing restores focus.** `Hubzero.filterClear` resets the filter fields and
  submits; `Hubzero.gridOrder` submits the form; `Hubzero.listItemTask` checks a
  row and submits. Each one replaces the page. If you write a handler that
  changes content in place instead, move focus to the changed region and say so
  in a live region — neither happens for you.
- **`Hubzero.saveOrder` calls `alert()`** when a row is checked out, and
  `toolbarAction` calls `alert()` and `confirm()`. Native dialogs are announced,
  but they discard the page's focus position when dismissed.

If you add your own live region, put the empty region in the page from
`index.php` and write into it, rather than creating a region at the moment you
have something to say.

### The card-link pattern

A clickable card built from a stretched `::after` on the link — so the whole
card is a hit target — does **not** appear in any shipped template. The only
place `.stretched-link` exists in `core/` is the vendored Bootstrap stylesheet at
`core/assets/css/bootstrap/5.3.3/bootstrap.css`, which no shipped template loads.

If you introduce it, know what it costs. The stretched pseudo-element makes the
card clickable but changes nothing about focus or reading order: the focus ring
still draws around the link text, not the card; any second link inside the card
is covered by the overlay and becomes unclickable; and text inside the card
cannot be selected with a mouse. The accessible name is still only the link's own
text, so if the card's heading is the link, everything else in the card —
the byline, the date, the summary — is invisible to a user navigating by link.

Either accept that and make the link text a complete label, or use a real
`<a>` around the card's heading and let the rest of the card be inert content.
