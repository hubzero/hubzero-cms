<!--
status: rewritten
reviewed-against: 2.4-main @ ab49f763b0
reviewed: 2026-09-09
screenshots: stale
source: https://help.hubzero.org/documentation/240/webdevs/supergroups/templating_system
source-id: 3520
modified: 2014-09-10
-->
# Templating system

A super group renders through a template of its own instead of the site
template. The template is ordinary PHP with a handful of `<group:include>`
tags in it, and it lives in the group's `template/` directory.

## The one required file

```
app/site/groups/<gidNumber>/template/index.php
```

That is the only file a super group template needs. Everything else — the
error page, the includes, the stylesheets — is convention.

## How the file is chosen

[`Components\Groups\Helpers\Template::_fetch()`](../../../core/components/com_groups/helpers/template.php)
builds a list of candidates and uses the first that exists. When a group page
is being rendered the order is:

1. `<page's template>.php` — the file behind the page's **Template** setting
2. `page-<alias>.php`
3. `page-<id>.php`
4. `page.php`
5. `default.php`
6. `index.php`

When there is no active page — a plugin tab such as Wiki or Calendar, for
instance — only `default.php` and `index.php` are considered, in that order.
If neither exists the group aborts with *Missing "Super Group" template
file.*

The per-page files are the subject of [Page templates](02-page_templates.md).

## What the template can see

The template is `require`d by the `Template` helper, so `$this` inside it is
that helper. Four properties are set before it runs:

| Property | What it holds |
|---|---|
| `$this->group` | The `Hubzero\User\Group` being displayed |
| `$this->page` | The active group page, or `null` |
| `$this->tab` | The active tab, from the `active` URL segment |
| `$this->content` | The rendered body of the active tab |

The file's output is then scanned for `<group:include>` tags, those are
replaced with rendered content, and the result is `eval`ed — so PHP written
inside a group page or module runs at that point too.

## Include tags

A template may use these tags. They are parsed by
[`Components\Groups\Helpers\Document`](../../../core/components/com_groups/helpers/document.php)
and each one is handled by a class in `helpers/document/renderer/`.

| Tag | What it renders |
|---|---|
| `<group:include type="content" />` | The body of the active tab |
| `<group:include type="content" scope="before" />` | Content group plugins contribute above the page |
| `<group:include type="menu" />` | The group's tab bar and page menu |
| `<group:include type="toolbar" />` | The member and manager toolbar |
| `<group:include type="modules" position="{position}" />` | Every published module in that position |
| `<group:include type="module" title="{title}" />` | One module, by title |
| `<group:include type="googleanalytics" account="{account}" />` | A Google Analytics snippet |
| `<group:include type="script" base="" source="{path}" />` | Adds a script to the document |
| `<group:include type="stylesheet" base="" source="{path}" />` | Adds a stylesheet to the document |

Tags must be self-closing, exactly as written. Anything else — a misspelled
`type`, or a tag used where it is not allowed — renders as an HTML comment
saying so rather than failing.

> **Warning:** A single module is `type="module"` with a `title`; a position
> is `type="modules"` with a `position`. Older documentation showed
> `type="modules" title="..."`, which matches no renderer, and spelled the
> attribute `postion`. Both are wrong.

### Script and stylesheet paths

`base` selects where `source` is resolved from:

- `base="template"` prepends `template/assets/js` or `template/assets/css`.
- Any other value is used as a path segment under the group directory.
- Omitting `base` looks in the group's `uploads` directory.

The file is served through the group's own download route, so a group
stylesheet arrives as `/groups/<alias>/File:template/assets/css/main.css`.

### Modules and approval

The module renderers list only published modules. Unapproved modules are
included only when the template is rendered with `allMods` set, which is what
the module preview screens do; a visitor never sees one.

## The default template

Saving a group as a super group copies
[`core/components/com_groups/super/default/template`](../../../core/components/com_groups/super/default/template)
into the group directory. It is a working starting point rather than a
finished design: a header with the group title and menu, the content, and a
footer.

<!--include: core/components/com_groups/super/default/template/index.php:1-30-->

Note the `no_html` check. When a request carries `no_html=1` the template
emits only the content, which is what AJAX requests from group plugins rely
on. Keep that behaviour in any template you write from scratch.

![An example of a finished super group template](../media/templating-system-template.png)

> **Note:** That screenshot shows a custom template built on a hub, not the
> skeleton the hub gives you. The skeleton is unstyled by comparison.

## Error template

The skeleton also ships `template/error.php` and `assets/css/error.css`.

![The super group error page the template was written for](../media/templating-system-error.png)

> **Warning:** This does not work in 2.4. `View::attachCustomErrorHandler()`
> in
> [`core/components/com_groups/helpers/view.php`](../../../core/components/com_groups/helpers/view.php)
> still checks that the group has an `error.php`, but the
> `set_exception_handler()` call below the check is commented out. A super
> group gets the site's error pages, so the screenshot above shows a page the
> software no longer produces. The file is still copied into every new group
> directory.

## File layout

![A super group directory in a file browser](../media/templating-system-filesystem.png)

> **Note:** That picture predates the `macros/` and `migrations/`
> directories, which the skeleton now creates as well. See
> [the section overview](README.md#the-group-directory) for the current
> layout.
