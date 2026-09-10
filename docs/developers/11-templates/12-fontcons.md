<!--
status: rewritten
reviewed-against: 2.4-main @ ab49f763b0
reviewed: 2026-09-09
screenshots: none
source: https://help.hubzero.org/documentation/240/webdevs/templates/fontcons
source-id: 3515
modified: 2012-10-19
-->
# Fontcons

Fontcons is the CMS's general-purpose icon font: pencils, folders, arrows,
warning triangles and so on. Roughly 295 glyphs, originally derived from Font
Awesome and heavily extended. Components, modules and the admin templates all
use it, so a site template that does not carry it renders a lot of blank
squares.

## What ships

| File | What it is |
|---|---|
| [`core/assets/less/fontcons.less`](../../../core/assets/less/fontcons.less) | The `@font-face` rule, plus 273 named `@icon…` variables — one per glyph |
| [`core/assets/less/icons.less`](../../../core/assets/less/icons.less) | 292 `.icon-*` classes built from those variables |
| [`core/assets/css/fontcons.css`](../../../core/assets/css/fontcons.css) | The `@font-face` rule alone, for stylesheets not written in LESS |
| `core/assets/css/fonts/fontcons-webfont.{eot,svg,ttf,woff}` | The font itself |

> **Warning:** `fontcons.css` is 17 lines and contains *only* the `@font-face`
> rule. Linking it gives you the family, not the classes. The `.icon-*` classes
> exist only in `icons.less`. Older documentation implied otherwise.

## Adding it to a template

If your template is written in LESS — as `kimera`, `kameleon` and `lucent` all
are — import the two files near the top, in this order:

```less
@import "../../../../core/assets/less/fontcons.less"; // Defines the available icons
@import "../../../../core/assets/less/icons.less";    // Depends on fontcons.less
```

`icons.less` refers to variables that `fontcons.less` defines, so importing it
alone fails to compile. That is what the `!!` in `kimera`'s comment means.

`fontcons.less` builds its URLs from a `@pathSystemCss` variable, so define it
in your `_variables.less` before the import. The shipped values are:

| Template | `@pathSystemCss` |
|---|---|
| `core/assets/less/variables.less` | `"/core/assets/css"` — an absolute path |
| `kimera/less/_variables.less` | `"../../../../core/assets/css"` — relative, from `css/` |

Use the relative form if your compiled CSS sits in the template's own `css/`
directory, the absolute form otherwise.

If you are not using LESS, link the plain stylesheet and write your own classes:

```html
<link rel="stylesheet" href="/core/assets/css/fontcons.css" />
```

> **Note:** Do not `@import` it from inside another stylesheet. Imported
> stylesheets are fetched only after the importing one has been parsed, which
> delays the font and can show a flash of unstyled content.

> **Note:** Older copies of this page pointed at `/media/system/css/`. That
> path has not existed since the `core`/`app` split.

## Using it

`icons.less` sets the family on anything whose class begins `icon-`, and each
class supplies its own glyph:

```less
*[class^="icon-"]:before,
*[class*=" icon-"]:before {
	font-family: "Fontcons";
	margin-right: 0.2em;
	speak: none;
}

.icon-edit:before   { content: "\@{iconPencil}"; }
.icon-delete:before { content: "\@{iconTrash}"; }
```

So all you write is the class:

```html
<a class="icon-edit" href="…">Edit</a>
```

Keep the visible text. The glyph is in a pseudo-element, which assistive
technology does not read, so the class alone leaves the control unlabelled.

To use a glyph outside the `icon-` naming, three mixins in
[`core/assets/less/mixins.less`](../../../core/assets/less/mixins.less) do the
work:

```less
.my-thing:before {
	.fontcons(@iconWrench);
}
```

`.ie7-fontcons-before()` and `.ie7-fontcons-after()` are the other two. They
emit a `zoom: expression(…)` hack for Internet Explorer 7, which cannot render
`:before`. They are still compiled into the shipped CSS. There is no reason to
use them in new work.

## The icons

Named variables in `fontcons.less` and matching classes in `icons.less`, in
these groups:

| Group | Examples |
|---|---|
| Voting | `.icon-heart`, `.icon-star`, `.icon-star-half`, `.icon-thumbs-up` |
| Notifications | `.icon-help`, `.icon-info`, `.icon-success`, `.icon-error`, `.icon-warning` |
| Security | `.icon-lock`, `.icon-unlock`, `.icon-flag`, `.icon-shield` |
| Config | `.icon-tools`, `.icon-settings`, `.icon-dashboard`, `.icon-key`, `.icon-wrench` |
| Mail | `.icon-envelope`, `.icon-inbox` |
| User, Zoom, Tags | `.icon-user`, `.icon-zoom-in`, `.icon-tag` |
| Travel, Download/Upload, Sound, Video, Media | `.icon-plane`, `.icon-download`, `.icon-volume-up`, `.icon-play` |
| Text edit | `.icon-bold`, `.icon-align-left`, `.icon-list` |
| Actions, Directional | `.icon-edit`, `.icon-delete`, `.icon-prev`, `.icon-next` |
| Folders, Files, Date/time | `.icon-folder`, `.icon-file`, `.icon-calendar`, `.icon-alarm-clock` |
| Commerce, Comments, Sign in/out | `.icon-shopping-cart`, `.icon-comment`, `.icon-signin` |
| Charts, States, Quotes, Awards | `.icon-line-graph`, `.icon-quote-open`, `.icon-trophy` |
| Computers, Gauge, Misc | `.icon-laptop`, `.icon-gauge-high` |

The last group in `icons.less` is aliases: `.icon-danger` is `.icon-error`,
`.icon-add` is `.icon-plus-sign`, `.icon-edit` is `.icon-pencil`,
`.icon-delete` is `.icon-trash`, and so on. Use whichever name reads better.

For the complete list, read the two files. They are the only authority — the
old page's bare list of codepoints has no names attached and cannot be checked
against anything.

## The alternative: SVG icons

The CMS also ships 325 SVG icons in
[`core/assets/icons`](../../../core/assets/icons), and this is the newer of the
two systems:

```php
<?php echo Html::asset('icon', 'edit'); ?>
```

That inlines the file wrapped in `<span class="icn icn-edit" aria-hidden="true"
focusable="false">`. The `.icn` rules in
[`core/assets/less/utilities.less`](../../../core/assets/less/utilities.less) size
the SVG to `1em` and fill it with `currentColor`, so it inherits the
surrounding text's size and colour exactly as a font glyph would.

| | Fontcons | SVG icons |
|---|---|---|
| Added by | A stylesheet import | `Html::asset('icon', …)` in a layout |
| Overridable per template | Only by restyling the class | `html/icons/{symbol}.svg` replaces the file |
| Cost | One font download, then free | Markup weight on every use |
| Colour | One per glyph | One per glyph, from `currentColor` |
| Multi-colour | No | Possible |

Both are current. Fontcons is what the existing component and template
stylesheets use, and you need it for those to look right. Reach for
`Html::asset('icon', …)` in new markup, where a template can override the
symbol without touching your stylesheet.
