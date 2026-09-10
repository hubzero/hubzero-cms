<!--
status: rewritten
reviewed-against: 2.4-main @ 91d03d0a23
reviewed: 2026-09-10
screenshots: none
source: https://help.hubzero.org/documentation/240/webdevs/templates/socicons
source-id: 3514
modified: 2013-08-02
-->
# Socicons

Socicons is a small webfont of logos for social and web services — Twitter,
Facebook, LinkedIn, Google, Dropbox and a few dozen more. It ships with the
CMS as font files only.

## When you need it

Almost never, and the honest answer is [at the bottom of this
page](#consider-not-using-it). Reach for Socicons in one case: you are styling
a control whose icon must be a **third-party brand mark**, and you want it to
match the share buttons the shipped plugins already draw. That is what the font
is for and it is the only thing it is good at.

For anything else — an edit pencil, a folder, an arrow — use the
[SVG icons](12-fontcons.md#the-alternative-svg-icons). They are overridable per
template and need no codepoint table.

The reason this page is longer than it should be is that Socicons ships with no
stylesheet, so every consumer reimplements the same thirty lines. If you use
it, you are writing those thirty lines too.

## What ships

Five files, and nothing else:

```
core/assets/css/fonts/socicons-webfont.eot
core/assets/css/fonts/socicons-webfont.otf
core/assets/css/fonts/socicons-webfont.svg
core/assets/css/fonts/socicons-webfont.ttf
core/assets/css/fonts/socicons-webfont.woff
```

> **Important:** There is no `socicons.css` and no `socicons.less`. Unlike
> [Fontcons](12-fontcons.md), Socicons has no shared stylesheet, no named
> codepoint variables and no `.socicon-*` classes. Every stylesheet that uses
> it declares its own `@font-face` and picks the codepoints out by hand.

Three do, all of them share buttons:

- [`core/plugins/resources/share/assets/css/share.css`](../../../core/plugins/resources/share/assets/css/share.css)
- [`core/plugins/publications/share/assets/css/share.css`](../../../core/plugins/publications/share/assets/css/share.css)
- [`core/plugins/projects/files/assets/css/files.css`](../../../core/plugins/projects/files/assets/css/files.css)

Between them they use seven glyphs. The rest of the font is unused.

## Using it

Declare the face, then set the family on the elements that need it. The paths
are absolute from the web root:

```css
@font-face {
	font-family: 'Socicons';
	font-display: block;
	src: url('/core/assets/css/fonts/socicons-webfont.eot');
	src: url('/core/assets/css/fonts/socicons-webfont.eot?#iefix') format('embedded-opentype'),
	     url('/core/assets/css/fonts/socicons-webfont.woff') format('woff'),
	     url('/core/assets/css/fonts/socicons-webfont.ttf') format('truetype'),
	     url('/core/assets/css/fonts/socicons-webfont.svg#SociconsRegular') format('svg');
	font-weight: normal;
	font-style: normal;
}
```

> **Note:** Older copies of this page pointed at `/media/system/css/fonts/`.
> That path has not existed since the `core`/`app` split. Use
> `/core/assets/css/fonts/`.

Put the glyph in a pseudo-element rather than in the markup, so the icon is a
styling decision and screen readers do not read it. This is what the share
plugins do:

```css
[class^="share_"]:before,
[class*=" share_"]:before {
	font-family: 'Socicons';
	font-style: normal;
	font-weight: normal;
	speak: none;
	display: inline-block;
	text-align: center;
	line-height: 1em;
	width: 16px;
	height: 16px;
	font-size: 16px;
}

.share_twitter:before   { content: "\f010"; color: #0d6eac; }
.share_facebook:before  { content: "\f012"; color: #344e86; }
.share_google:before    { content: "\f014"; color: #000; }
.share_linkedin:before  { content: "\f016"; color: #0b5394; }
.share_pinterest:before { content: "\f01a"; color: #a01020; }
```

The markup keeps the label as real text, with the icon class on a wrapper:

```html
<a href="…" title="Share on Twitter" class="popup" rel="external">
	<span class="share_twitter"><span>Twitter</span></span>
</a>
```

Keep the text. A pseudo-element is not read by a screen reader, and the link
has to say something when the font fails to load.

## What is in the font

The SVG font defines glyphs at these codepoints:

`f001`–`f006`, `f010`–`f01f`, `f021`–`f02e`, `f054`, `f201`–`f204`, plus a
handful of ordinary characters (`©`, `®`, `Æ`, `∞`, `™`).

The documented names, from the original release:

| Icon | Main | Alt |
|---|---|---|
| Hub | `\f002` | `\f001` |
| Purdue | `\f006` | `\f005` |
| Twitter | `\f011` | `\f010` |
| Facebook | `\f013` | `\f012` |
| Google+ | `\f015` | `\f014` |
| LinkedIn | `\f017` | `\f016` |
| Github | `\f019` | `\f018` |
| Pinterest | `\f01b` | `\f01a` |
| Vimeo | `\f01d` | `\f01c` |
| YouTube | `\f01f` | `\f01e` |
| Skype | `\f022` | `\f021` |
| PayPal | `\f024` | `\f023` |
| Dropbox | `\f026` | `\f025` |
| Dribbble | `\f028` | `\f027` |
| eBay | `\f02a` | `\f029` |
| Google | `\f02c` | `\f02b` |

> **Note:** The imported page listed `\f01e` twice, as both *Vimeo alt* and
> *YouTube alt*, and never mentioned `\f01c`. The table above assigns `\f01c`
> to Vimeo alt to fit the odd/even pairing every other entry follows. The
> codepoints the shipped stylesheets actually use — `f010`, `f012`, `f014`,
> `f016`, `f01a`, `f026`, `f02c` — are confirmed; treat the rest as a guide and
> check the glyph before you ship it.

The remaining codepoints (`f003`, `f004`, `f02d`, `f02e`, `f054`, `f201`–`f204`)
are in the font but were never named anywhere in this repository.

## Consider not using it

For anything that is not a third-party logo, prefer the SVG icons: 325 of them
in [`core/assets/icons`](../../../core/assets/icons), inlined by
`Html::asset('icon', 'feed')`, overridable per template at
`html/icons/{symbol}.svg`, and coloured with `currentColor`. No font to load
and no codepoint to look up. See [Fontcons](12-fontcons.md) for how the two
compare.
