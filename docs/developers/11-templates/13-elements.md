<!--
status: rewritten
reviewed-against: 2.4-main @ ab49f763b0
reviewed: 2026-09-09
screenshots: none
source: https://help.hubzero.org/documentation/240/webdevs/templates/elements
source-id: 3516
modified: 2013-08-02
-->
# Elements and typography

The classes shared between the templates and the components. Component views
emit this markup and expect your template to style it, so these are the ones
you cannot rename.

The sources are in [`core/assets/less`](../../../core/assets/less), with compiled
equivalents in [`core/assets/css`](../../../core/assets/css) for anything not
written in LESS:

| Element | LESS | Compiled CSS |
|---|---|---|
| Grid | `grid.less` | `columns.css` |
| Buttons | `buttons.less` | `buttons.css` |
| Notifications | `notifications.less` | `notifications.css` |
| Pagination | `pagination.less` | `pagination.css` |
| Tabs | `tabs.less` | `tabs.css` |
| Layout | `layout.less` | `layout.css` |

`core/assets/less/site.less` imports the whole set in the right order and is
the shortest way to pick them all up.

## The grid

Twelve fluid columns. A `.grid` wrapper, `.col` on each child, and a `.span{n}`
saying how wide it is:

```html
<div class="grid">
	<div class="col span3">…</div>
	<div class="col span3">…</div>
	<div class="col span3">…</div>
	<div class="col span3 omega">…</div>
</div>
```

- Every column needs `.col`. That is what floats it and gives it the gutter.
- The last column in a row needs `.omega`, which removes the trailing gutter.
  Without it the row overflows.
- Columns are percentages of the container, so a grid nests inside a column of
  another grid with no extra work.
- No clearing element is needed; `.grid` clears itself.

`.span1` through `.span12` exist, and `.offset1` through `.offset12` push a
column to the right by that many columns:

```html
<div class="grid">
	<div class="col span3 offset3">…</div>
	<div class="col span3">…</div>
	<div class="col span3 omega">…</div>
</div>
```

### Fraction classes

Aliases for the common widths, easier to read than counting:

| Class | Equivalent | Offset alias |
|---|---|---|
| `.span-whole` | `.span12` | `.offset-whole` |
| `.span-three-quarters` | `.span9` | `.offset-three-quarters` |
| `.span-two-thirds` | `.span8` | `.offset-two-thirds` |
| `.span-half` | `.span6` | `.offset-half` |
| `.span-third` | `.span4` | `.offset-third` |
| `.span-quarter` | `.span3` | `.offset-quarter` |

### Collapsing

`grid.less` rearranges rows as the viewport narrows. The rules count the
columns in the row with `:nth-last-child`, so what happens depends on how many
children the `.grid` has, not on their `.span` classes:

| Breakpoint | A row of six | A row of four | A row of three |
|---|---|---|---|
| `@break6` — 1023px | Three across, two rows | — | — |
| `@break4` — 1000px | — | Two across, two rows | — |
| `@break3` — 900px | Two across, three rows | Two across | Full width |
| `@break2` — 500px | Full width | Full width | Full width |

Two escape hatches, both on the `.grid`:

- `.nobreak` — never rearrange. Every rule above is written
  `.grid:not(.nobreak)`.
- `.break6`, `.break4`, `.break3` — go straight to full-width columns at that
  breakpoint instead of rearranging.

The breakpoints are variables in
[`core/assets/less/variables.less`](../../../core/assets/less/variables.less), so
a LESS template can move them.

## Sections and asides

Most component pages are a main column with a narrower one beside it. The
markup is:

```html
<section class="section">
	<div class="section-inner">
		<div class="aside">
			Secondary navigation, metadata, related items …
		</div>
		<div class="subject">
			The main content …
		</div>
	</div>
</section>
```

`.aside` must come **before** `.subject` in the source. The rules that size
them are written `.aside + .subject`, so reversing the order leaves the layout
broken. If that is a semantic problem for your page, use the grid instead.

Unlike the grid, `.aside` has a fixed width and `.subject` takes what is left.

> **Important:** `.section`, `.section-inner`, `.aside` and `.subject` are
> **not** in `core/assets/less`. They are styled by each template —
> `kimera/less/_sections.less` is the reference — and `core/assets/less/layout.less`
> only carries a few responsive rules for them. A new template must supply
> these itself or every component page loses its sidebar. Copy
> `_sections.less` when you copy the template.

## Notifications

Five classes, all styled the same way and differing only in colour and the
icon in the `:before`:

| Class | Meaning |
|---|---|
| `.passed` | Success |
| `.info` | Information |
| `.help` | Help |
| `.warning` | Warning |
| `.error` | Error |

```html
<p class="passed">Your changes have been saved.</p>
<p class="error">That file is too large.</p>
```

The messages the application queues render through
`<jdoc:include type="message" />` as a definition list inside
`#system-message`, and its `dd` elements pick up the same styling. Its `dt`
elements are hidden by the stylesheet; they carry the message type for
assistive technology, so do not remove them from the layout.

`Hubzero.renderMessages()` in `core.js` writes the same structure from
JavaScript. See [JavaScript](08-javascript.md).

## Buttons

`.btn` on a link, a `<button>` or an `<input type="submit">`:

```html
<a class="btn" href="#">Link</a>
<button class="btn">Button</button>
<input type="submit" class="btn" value="Submit" />
```

### States

| Class | Effect |
|---|---|
| `.active` | Pressed. `:active` gets the same styling |
| `.disabled` | 65% opacity, `cursor: not-allowed`, `pointer-events: none`. The `disabled` attribute does the same |

### Variants

| Class | Effect |
|---|---|
| `.btn-primary` | The emphasised action. One per form |
| `.btn-secondary` | **Smaller**, not a colour change — this is the size modifier |
| `.btn-success` | Green |
| `.btn-info` | Blue |
| `.btn-warning` | Orange |
| `.btn-danger` / `.btn-error` | Red. The two are identical |

### Icons

Add any `.icon-*` class from [Fontcons](12-fontcons.md) and the glyph is
rendered in a tinted block at the left edge of the button, with the padding
adjusted for it:

```html
<a class="btn btn-danger icon-danger" href="#">Delete</a>
<a class="btn icon-prev" href="#">Previous</a>
```

Add `.opposite` to move the glyph to the right edge instead — what you want for
a "next" control:

```html
<a class="btn icon-next opposite" href="#">Next</a>
```

### Groups

`.btn-group` joins buttons into one control, squaring off the inner corners:

```html
<div class="btn-group">
	<a class="btn icon-prev" href="#">Previous</a>
	<a class="btn" href="#">All</a>
	<a class="btn icon-next opposite" href="#">Next</a>
</div>
```

Add `.dropdown` and a toggle for a menu:

```html
<div class="btn-group dropdown">
	<a class="btn" href="#">Actions</a>
	<span class="btn dropdown-toggle"></span>
	<ul class="dropdown-menu">
		<li><a href="#">Edit</a></li>
		<li><a href="#">Duplicate</a></li>
		<li class="divider"></li>
		<li><a href="#">Delete</a></li>
	</ul>
</div>
```

- `.dropup` instead of `.dropdown` opens the menu upwards.
- `.btn-group.btn-secondary` makes the whole group the smaller size.
- `.divider` on an empty `<li>` draws a rule between groups of items.
- The menu opens on hover as well as on click — `.btn-group:hover .dropdown-menu`
  — so it needs no script.
