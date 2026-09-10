<!--
status: rewritten
reviewed-against: 2.4-main @ ab49f763b0
reviewed: 2026-09-09
screenshots: none
source: https://help.hubzero.org/documentation/240/webdevs/supergroups
source-id: 3519
modified: 2014-09-10
-->
# Super Groups

A super group is a hub group with a directory of its own on the server. The
directory holds a template, optional PHP pages, optional components, macros,
migrations and a database configuration, and the group renders through that
template instead of the site's. This section is about writing the code that
goes in that directory.

A group's type is a database value only an administrator can set. Nothing a
group manager does from the site turns an ordinary group into a super group,
and nothing in the group's own web space can be edited from the site either:
the group file browser reaches the group's `uploads` folder and nothing else,
for every group, super or not. You edit a super group's template on the
server, or through the repository workflow in
[Super Groups with GitLab](../14-supergroups-gitlab/README.md).

For what the status gives a group and how an administrator creates one, see
[Super Groups](../../managers/06-users/08-supergroups.md) in the managers
book.

## The group directory

Group files live under the **Upload Path** option of `com_groups`,
`/site/groups` by default, resolved against `PATH_APP` — so
`app/site/groups/` on a stock install. Each group gets a directory named
after its numeric `gidNumber`, not its alias:

```
app/site/groups/1051/
├── components/          super group components
├── config/
│   └── db.php           credentials for the group's own database
├── language/
│   └── en-GB/           string overrides
├── macros/              custom and overridden wiki macros
├── migrations/          schema changes for the group's database
├── pages/               standalone PHP pages
├── template/
│   ├── index.php        the template, the only required file
│   ├── error.php        error page (see the note below)
│   ├── includes/
│   │   ├── header.php
│   │   └── footer.php
│   └── assets/
│       ├── css/
│       └── js/
└── uploads/             everything the group's file browser can reach
```

Everything except `pages/` is created when the group is saved as a super
group, from the skeleton in
[`core/components/com_groups/super/default`](../../../core/components/com_groups/super/default).
Existing files are never overwritten, so re-saving a group is safe. Create
`pages/` yourself when you need it.

Two directories are excluded from the group's git repository by
[`gitlab_setup.sh`](../../../core/components/com_groups/admin/assets/scripts/gitlab_setup.sh):
`uploads/*` and `config/db.php`. Keep generated files and credentials out of
the repository.

> **Note:** If the hub's active site template ships a `super/` directory, it
> is copied over `template/` first and the skeleton fills in whatever it did
> not provide. No template in this repository ships one, so in practice every
> new super group starts from the skeleton.

## What a super group can do

| Capability | Chapter |
|---|---|
| Render the group through its own template | [Templating system](01-templating_system.md) |
| Give individual pages their own layout | [Page templates](02-page_templates.md) |
| Add or replace `[[Macro()]]` handlers | [Custom macros](03-custom_macros.md) |
| Serve a plain PHP file at a group URL | [PHP pages](04-php_pages.md) |
| Read and write a private database | [Databases](05-databases.md) |
| Version that database's schema | [Migrations](06-migrations.md) |
| Ship a full MVC component | [Components](07-components.md) |

A super group also overrides the strings of any group plugin: every
`core/plugins/groups/*` plugin looks in the group directory's `language/`
folder before its own, so
`language/en-GB/en-GB.plg_groups_blog.ini` renames or rewords anything the
Blog tab says for that group alone.

## Code in pages and modules

Group page and module content is run through HTML Purifier before it is
stored. For an ordinary group, PHP tags and `<script>` elements are stripped.
For a super group — and for an ordinary group whose **Trusted content** page
setting is on — they survive, handled by the `Php` and `ExternalScripts`
filters in
[`core/components/com_groups/helpers/filters/`](../../../core/components/com_groups/helpers/filters).

Content that contains `<?`, `<?php` or `<script` is saved unapproved and
mailed to the usernames in the **Page Approvers** option. Visitors see a
placeholder until an approver approves it; approval then notifies the group's
managers. The approval screens are described in
[Super Groups](../../managers/06-users/08-supergroups.md#approval-of-pages-and-modules).

The `<group:include>` tags described in the next chapters survive
purification for every group, because the `GroupInclude` filter is always
applied. They only *do* anything where the renderer runs them.

> **Note:** Escaping the approval queue is what the `pages/` directory is
> for. A `.php` file placed there by someone with server access is executed
> as written and is never queued for approval.
