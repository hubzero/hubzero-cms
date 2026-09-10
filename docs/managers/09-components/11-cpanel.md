<!--
status: rewritten
reviewed-against: 2.4-main @ be0bd4c772
reviewed: 2026-09-10
screenshots: none
-->
# Control Panel

The Control Panel is the first screen after you sign in to `/administrator`.
The component behind it, `com_cpanel`, holds no data and has no settings: it
draws a stack of collapsible panels, one for each administrator module
published in the **cpanel** position, and nothing else. What your dashboard
shows is therefore a question about modules, not about this component.

[Daily maintenance](../03-maintenance/README.md) describes the panels
themselves — what each one counts and where its numbers link to. This chapter
covers the component and how you change what appears.

## What it renders

The component has one screen and five files. Its layout,
[`views/cpanel/tmpl/default.php`](../../../core/components/com_cpanel/admin/views/cpanel/tmpl/default.php),
asks the module loader for everything published in the `cpanel` position,
renders each one, and wraps it in a slider panel — the loop is quoted in
[Daily maintenance](../03-maintenance/README.md). The open or closed state of
each panel is remembered in a cookie, per administrator.

The Control Panel is also the fallback screen for the whole administrator
interface. A request to `/administrator` with no `option` lands here, and
every component's **Cancel** returns here when it has nowhere better to go.

## Changing what appears

The panels are ordinary administrator modules, so they are managed from
**Extensions → Module Manager**:

1. Open **Extensions → Module Manager**.
2. Set the **Client** filter to **Administrator**. Administrator
   modules are invisible while the filter is on **Site**, and so are the
   positions they can occupy.
3. To add a panel, press **New**, choose the module type, and set its
   **Position** to `cpanel`. To remove one, either unpublish it or move it to
   another position.
4. **Ordering** decides the order of the panels down the page.
5. **Access** decides which administrators see it. A panel is only as visible
   as the viewing level on its module.

[Modules](../10-extensions/01-modules.md) covers the Module Manager itself.

A module published in `cpanel` needs nothing special: any administrator module
works there, including a Custom HTML module, which is the simplest way to put
a hub's own notice or link list on the dashboard.

### Panel headings

The heading on a panel is the module's **Title** as you typed it in the Module
Manager — unless the module's **Automatic Title** option is set, in which case
the module supplies its own heading. Both modules a fresh install publishes
here have that option set, so their headings read *Popular Articles* and
*Recently Created Articles* whatever you call the modules.

## Permissions and options

There are none. The component ships no `config.xml` and no `access.xml`, so it
has no **Options** button and no permission rules of its own, and its entry
point performs no access check — reaching the administrator interface at all
is the only gate. Who sees which panel is decided by each module's **Access**
setting.

> **Note:** The component has a second, undocumented task. Requesting
> `index.php?option=com_cpanel&task=module&module=<name>` renders that one
> administrator module on its own, ignoring whether it is published, what
> position it is assigned to, and what access level it carries. Nothing in the
> interface links to it. It is recorded with the project,
> which is not published to this site.
