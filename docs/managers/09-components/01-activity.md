<!--
status: rewritten
reviewed-against: 2.4-main @ be0bd4c772
reviewed: 2026-09-10
screenshots: none
-->
# Activity

The activity log is the hub's record of who did what. When a member posts a
blog entry, answers a question, joins a group, uploads a file to a project or
starts a tool session, the component responsible writes a row to the log and
names the people who should hear about it. Those rows are what fill the
**Activity** tab on a member's profile and a group's activity feed.

The Activity component itself is thin. It has no site pages of its own and one
administrator screen, which draws a graph. Everything a manager actually
controls — whether a member sees an activity feed, whether digests go out,
who receives what — lives in the plugins described below, not in this
component.

## The screen

Open it under **Components > Activity**. There is one view: a line chart
labelled **Recent**, plotting the number of log entries recorded on each day
of the last four weeks. Hovering a point gives the date and the count.

That is the whole screen. There is no list of entries, no search, no filter,
and no way to open, edit or delete an entry from here. The chart is useful for
one thing: seeing at a glance whether the hub is still recording activity, and
roughly how much.

The toolbar has **Options**, shown only if you may configure the component,
and **Help**.

> **Note:** The **Help** button opens the wrong text. `admin/help/en-GB/`
> holds a copy of the Blogs component's help screens, describing blog entries
> and their columns. Ignore it.

The chart is built by counting entries one day at a time, so the screen runs
around thirty queries against `#__activity_logs` each time it loads. On a
large, long-lived hub it is slow.

## Options

The component's configuration has only a **Permissions** tab. There are no
settings. `config/access.xml` declares the usual seven actions — Configure,
Access Administration Interface, Create, Delete, Edit, Edit State and Edit Own
— but only two matter: Access Administration Interface admits you to the
screen above, and Configure shows the **Options** button. Nothing in the
component creates, edits or deletes an entry, so the other five have no
effect.

## How entries are recorded

A component that wants to record something fires a system event:

```php
Event::trigger('system.logActivity', [
    'activity' => [
        'action'      => 'created',
        'scope'       => 'blog.entry',
        'scope_id'    => $entry->get('id'),
        'description' => Lang::txt('...'),
        'details'     => [...]
    ],
    'recipients' => [
        ['group', $group->get('gidNumber')],
        $user->get('id')
    ]
]);
```

The **System - Activity** plugin
([`core/plugins/system/activity`](../../../core/plugins/system/activity/activity.php))
answers it. It ignores anything fired from the administrator interface and
otherwise hands the array to
[`Hubzero\Activity\Log`](../../../core/libraries/Hubzero/Activity/Log.php),
which writes one row to `#__activity_logs` and then *broadcasts* it: one row in
`#__activity_recipients` per person or group named in `recipients`, plus one
for everyone with a matching subscription in `#__activity_subscriptions`.

Two consequences are worth knowing:

- **If that plugin is disabled, nothing is logged at all.** Every activity
  feed on the hub goes quiet, silently. It is a system plugin, so it does not
  appear anywhere obvious; check **Extensions > Plugins** filtered to the
  `system` folder.
- The log row and the delivery rows are separate. Removing an entry from your
  own feed marks your recipient row unpublished; the log row, and everyone
  else's copy, stay.

Around forty places in the tree fire the event — the Answers, Blogs,
Collections, Forum, Groups, Knowledge base, Projects, Publications, Resources,
Support, Tools, Wiki and Wishlist components, plus their group and member
plugins. The scope string always names the component and the kind of thing,
such as `forum.thread`, `project.file` or `groups.member`.

## Where the log is read

| Where | Plugin |
|---|---|
| The **Activity** tab on a member's own profile | `plg_members_activity` |
| A group's activity feed | `plg_groups_activity` |
| A project's activity feed | `com_projects`, reading the recipient rows directly |
| `/api/activity` | See the [API reference](../../reference/api/activity.md) |

The member tab is visible only to the member themselves; a manager cannot use
it to read someone else's feed. It offers a keyword search, a **Category**
filter built from the scopes actually present in the log, and a filter for
entries the member did or did not create. Members can star entries and remove
them from their own view.

The **Members - Activity** plugin has two parameters: **Display Tab**, which
decides whether the tab appears in the profile's menu, and **Email digests**,
which is the switch for everything in the next section.

## Digests

With **Email digests** set to Yes on the **Members - Activity** plugin, the
profile tab grows a **Settings** action where a member chooses how often to be
emailed a summary of their feed: never, daily, weekly or monthly. The choice
is stored in `#__activity_digests`.

Nothing is sent until the **Cron - Activity** plugin's job is scheduled. Add a
job under [Cron](cron.md) using the event **Email member activity digest**.
It behaves like this:

- Daily digests go every run; weekly digests only on a Monday; monthly
  digests only on the first of the month.
- Each member's digest covers the recipient rows created since their last
  digest was sent.
- The job has no queue and no per-member scheduling of its own.

> **Warning:** Do not schedule this job more than once a day. It re-reads the
> same window each time it runs and will send members the same digest
> repeatedly.

The plugin also has an **Email transport mechanism** parameter, matching the
one on the other cron mail jobs.

## Retention

There is none. Nothing in the tree prunes `#__activity_logs`, nothing expires
old rows, and the administrator screen offers no way to delete anything. The
table grows for the life of the hub, and on a busy hub it becomes one of the
largest tables in the database.

The only deletions that happen are cascades: deleting a log entry through the
model removes its recipient rows and its child entries, which is what happens
when the component that owns the subject deletes it. If you need to trim the
log, it is a database job, and there is no supported procedure for it.
