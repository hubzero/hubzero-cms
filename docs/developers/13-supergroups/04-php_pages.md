<!--
status: rewritten
reviewed-against: 2.4-main @ 91d03d0a23
reviewed: 2026-09-10
screenshots: none
source: https://help.hubzero.org/documentation/240/webdevs/supergroups/php_pages
source-id: 3523
modified: 2014-09-10
-->
# PHP pages

A PHP page is a plain `.php` file in the group's `pages/` directory that the
group serves at a URL. It is the way to write a page in code instead of in the
page editor, and the way out of the approval queue: a file put there by
someone with server access runs as written and is never queued.

The Coastal Resilience Center's status board is one of these. It reads the
latest reading for every tide gauge out of the group's own database and prints
a table. There is nothing to administer, no records to edit, and one URL. A
component would be four directories of scaffolding for that; a group page
written in the editor would go through the approval queue on every save.

Reach for a PHP page when all of the following hold — otherwise see
[Components](07-components.md):

- one page, or a small handful, and no URL structure below them;
- no records the group edits through the hub;
- the people who maintain it can deploy files.

```
app/site/groups/<gidNumber>/pages/
```

The skeleton does not create `pages/`. Make it yourself the first time you
need it.

## The smallest thing that works

```php
<?php // app/site/groups/1051/pages/status.php ?>
<h2>Tide gauge status</h2>
<p>Group: <?php echo htmlspecialchars($group->get('description')); ?></p>
```

Reachable at `/groups/coastal/status`. No registration, no manifest, no
restart.

## URL mapping

The file path under `pages/` is the URL path under the group, with `.php`
added:

| URL | File |
|---|---|
| `/groups/coastal/status` | `pages/status.php` |
| `/groups/coastal/status/pier-7` | `pages/status/pier-7.php` |
| `/groups/coastal` | `pages/overview.php` |

The last row is not a special case: an empty path is treated as `overview`, so
`pages/overview.php` replaces the group's home page.

Note what the second row means: there is no path *parameter*. `status/pier-7`
is a second file, not an argument to the first. A page that has to vary by
identifier belongs in a component, which gets a router.

## When a PHP page is reached

[`Components\Groups\Helpers\View::superGroupPhpPages()`](../../../core/components/com_groups/helpers/view.php)
is consulted only while the group is showing its **Overview** tab, and only
after two other things have had their turn. The order in
[`site/controllers/groups.php`](../../../core/components/com_groups/site/controllers/groups.php)
is:

1. the group login page, for `/groups/<cn>/login`
2. a super group component, if the **Super Group Components** option is on
3. a PHP page
4. a group page from the page manager

So a PHP page shadows a group page with the same alias, and a super group
component shadows a PHP page with the same name.

Two sets of names never reach step 3 at all:

- **Group plugin names.** If the first segment after the group alias matches
  an enabled group plugin — `wiki`, `blog`, `forum`, `calendar`,
  `announcements`, `members`, `resources`, and so on — that plugin's tab is
  the active tab and the overview path is not run. `pages/wiki.php` is
  unreachable.
- **Segments the group router claims.** `edit`, `delete`, `customize`,
  `invite`, `accept`, `cancel`, `join`, `request`, `pages`, `modules`,
  `categories` and `media` are routed to com_groups controllers by
  [`site/router.php`](../../../core/components/com_groups/site/router.php).

Nothing warns you about the collision. The page simply never appears — you get
the wiki, or the page manager, or a group page you had forgotten about, and no
error anywhere says why. Check the name against both lists before you check
your file.

## What the file can do

The file is `include`d, so it runs in the scope of the helper method that
included it. `$group` is in scope and is the `Hubzero\User\Group` being
displayed. Echo your markup; anything the file prints becomes the page body.

That method is **static**, so there is no `$this`. Copying a line out of a view
template — `$this->escape($x)` is the usual one — fatals with *Using $this when
not in object context* and takes the whole group page with it. Escape with
`htmlspecialchars()` here.

After the file runs, its output goes through two more steps:

1. `<group:include>` tags in the output are rendered. The tags allowed are the
   same four allowed in page content — `modules`, `module`, `script` and
   `stylesheet` — described in
   [Page templates](02-page_templates.md#include-tags-inside-a-page-s-content).
2. The result is passed through `eval()`.

> **Warning:** That second `eval()` means PHP tags in the *output* are
> executed as well. If your page echoes text that a user supplied, and that
> text contains `<?php`, it runs. Escape anything you did not write.

The page body is then wrapped by
[`site/views/pages/tmpl/_view_php.php`](../../../core/components/com_groups/site/views/pages/tmpl/_view_php.php),
which is simply:

```php
<div class="group-php-page">
	<?php echo $this->content; ?>
</div>
```

To change that wrapper, put a file called `_view_php.php` in the group's
`template/` directory — not in `template/pages/`, which is where the wrapper
for editor-written pages is overridden.

## Reading the group's database

The status board's whole point is the data behind it, and getting the
connection right is the one thing that bites:

```php
<?php
$db = \Hubzero\User\Group\Helper::getDbo();

$db->setQuery("SELECT `name`, `reading` FROM `gauges` ORDER BY `name`");

echo '<ul class="gauges">';
foreach ($db->loadObjectList() as $gauge)
{
	echo '<li>' . htmlspecialchars($gauge->name) . ': '
	   . htmlspecialchars($gauge->reading) . '</li>';
}
echo '</ul>';
```

`getDbo()` returns the **hub's** connection rather than failing when anything
is wrong — see [Databases](05-databases.md#opening-it). A `CREATE TABLE` in a
PHP page that has silently landed on the hub connection is how a group's table
ends up in the hub schema.

## Rendering inside the group template

A PHP page is content, not a template. It is dropped into the group's normal
template, so the header, menu and footer come from `template/index.php` as
usual. There is no per-file template selection for PHP pages the way there is
for group pages; if a PHP page needs a different frame, branch on
`Request::path()` inside `index.php`, or move the work into a
[super group component](07-components.md), which gets a router of its own.

## Approval

PHP pages are not filtered, not scanned and not queued. The approval workflow
in [Super Groups](../../managers/06-users/08-supergroups.md#approval-of-pages-and-modules)
applies to content saved through the page and module managers on the site.
A file in `pages/` bypasses all of it, which is the point of the directory and
also the reason it can only be filled by someone with access to the server or
to the group's repository.

That cuts both ways. The code runs with the hub's privileges and nobody
reviews it on the way in, so the group's repository — not the group's page
editor — is where the review has to happen.
