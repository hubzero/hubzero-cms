<!--
status: rewritten
reviewed-against: 2.4-main @ 1924c22171
reviewed: 2026-09-09
screenshots: none
source: https://help.hubzero.org/documentation/240/webdevs/conventions/csscodingstyles
-->
# CSS Coding Style

Stylesheets live in two places: a template's `css/` directory
(`core/templates/kimera/css/`, `core/templates/kameleon/css/`) and an
extension's own asset directory
(`core/components/com_blog/site/assets/css/blog.css`). The rules below apply to
both, and to the LESS sources the templates compile from.

## Terminology

```css
selector {
	property: value;
}
```

## The file header

A stylesheet opens with the same docblock as a PHP file — `@package`,
`@copyright`, `@license` — in a `/** */` block:

<!--include: core/components/com_blog/site/assets/css/blog.css:1-5-->

## Indentation

One tab per level. 215 of the 252 stylesheets in core indent with tabs, as do
106 of the 135 LESS files.

Rules are indented one level under the comment that introduces their group, so
the comments read as headings down the left margin:

```css
/* Entries listing */
	.blog-entries article {
		position: relative;
	}

	.blog-entries dl.entry-meta {
		margin: 0.5em 0;
		color: #999;
	}
```

## Selectors

A selector sits on one line and ends in the opening brace. The closing brace
goes on its own line.

Where several selectors share a rule, put each on its own line with the comma
immediately after it, no space:

```css
#forum td.posts,
#forum td.topics,
#forum td.replies,
#forum td.pager {
}
```

Leave a blank line between groups of related rules, and comment each group.

## Properties

Each property is on its own line, one level deeper than the selector, with:

- no space before the colon
- one space after the colon
- a semicolon at the end, including on the last property

```css
#forum .description {
	color: #EFEFEF;
	font-size: 0.9em;
	margin: 0.5em;
}
```

Separate multiple values with a space after each comma:

```css
font-family: helvetica, sans-serif;
```

Reach for `!important` only to beat a rule you cannot edit. It appears in core
where a component stylesheet has to override the template.

## LESS

`core/templates/kameleon` and `core/templates/lucent` are written in LESS under
a `less/` directory and compiled into `css/`. The syntax adds three things to
the rules above:

- Variables are `@name`: `font-family: @sansFontFamily;`
- Mixins are called like a rule: `.border-radius(0.25em);`
- Nested blocks and `&` for the parent selector

```less
.input-text,
textarea {
	font-family: @sansFontFamily;
	background-color: #F0F0F0;
	.border-radius(0.25em);

	&:hover {
		border-color: #c9c9c9;
	}

	&:focus {
		background-color: #fff;
		border-color: #777;
	}
}
```

A nested block gets a blank line before it. Keep nesting shallow: every level
is a level of specificity a later rule has to beat.

The compiler is `splitbrain/lesserphp`, wrapped by `core/bin/lessc` for
command-line use and by
[`Hubzero\Document\Assets`](../../../core/libraries/Hubzero/Document/Assets.php)
at runtime. The CMS compiles a template's LESS on demand into
`app/cache/site.css`; delete that and `app/cache/site.less.cache` with:

```bash
php core/bin/muse cache:css clear
```

Edit the `.less` source, never the generated `.css` beside it.

## Colours

Hex, and short form where it exists: `#fff`, not `#ffffff`. Core is
inconsistent about case — both `#F0F0F0` and `#c9c9c9` appear — so follow the
file you are in. Use `rgba()` where transparency is wanted, with the flat hex
on the line above as the fallback:

```css
background-color: #F0F0F0;
background-color: rgba(0, 0, 0, 0.039);
```
