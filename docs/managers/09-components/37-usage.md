<!--
status: rewritten
reviewed-against: 2.4-main @ 009ec973b7
reviewed: 2026-09-10
screenshots: none
source: https://help.hubzero.org/documentation/240/managers/components/usage
source-id: 3401
modified: 2016-08-24
imported: 2026-09-09
-->
# Usage

The Usage component publishes the hub's statistics page at `/usage`. It is a
reporting front end and nothing else: it collects no data, computes no
totals, and stores nothing. Every figure it draws comes from tables filled in
by the hub's metrics tooling, which is not part of this repository.

Read this chapter before promising anyone a usage page. On a hub installed
from this repository and nothing else, most of `/usage` has nothing to show.

This is the chapter to have open when somebody — a principal investigator, a
funder's annual report, a departmental review — asks for the hub's numbers.
The honest answer for a plain installation is that the hub is not collecting
them. That is not a fault you can fix from the administrator interface, and
the page will not tell you so: it will show zeros, empty tables, or a missing
table error, all three of which look like a hub nobody uses rather than a hub
nobody is measuring.

> **Important:** A zero on `/usage` is not evidence of no activity. It is
> evidence of no collection. Do not report these figures to anyone until you
> have established that the metrics tooling is installed and writing to the
> database the component is pointed at.

## What it is not

Usage is not the hub's analytics, and it is not the per-member statistics a
member sees on their own profile. It is a public page of institution-level
totals — visits, downloads, simulation sessions, top tools, top countries —
intended for a hub's own reporting. Two things people ask for are elsewhere:

- Traffic analytics for the site as a whole are not in this repository at
  all; hubs use an external analytics service for that.
- A member's own figures come from the **Members - Usage** plugin, covered at
  the end of this chapter.

## The administrator screen

**Components → Usage** has one screen and it says so:

> There are currently no administrative capabilities for this component.
> Configuration may be done via the **options** button in the toolbar.

That is the whole screen. **Options** is the only control on it. Reaching the
screen at all needs `core.manage` on `com_usage`.

## Options

The options fall into three groups. The complete list is in the
[generated parameter reference](../../reference/configuration/components/usage.md).

**The statistics database.** **Stats DB Driver**, **Host**, **Port**,
**Username**, **Password**, **Database**, **Prefix**, and **SSL CA Path**
point the component at the database holding the collected metrics.

> **Important:** When **Username**, **Password**, and **Database** are all
> empty, the component silently falls back to the hub's own database. The
> tables it wants are not there, so every screen that needs them reports
> *Missing table usage `tops`* or similar. An empty configuration does not
> disable the page; it produces a broken one.

**Maps API Key.** A Google Maps API key. See the warning under
[Maps](#maps).

**Paths.** **Path** (`/site/usage`), **Maps** (`/site/usage/maps`), **Plots**
(`/site/usage/plots`), and **Charts** (`/site/usage/charts`) say where the
pre-rendered map and chart images live. The metrics tooling writes them; the
component only serves them.

## What is on `/usage`

The page's tabs are supplied by the plugins in the `usage` group, one tab per
enabled plugin. Manage them under **Extensions → Plugins**, filtering on
`usage`.

| Plugin | Tab | Enabled on a new hub |
|---|---|---|
| Usage - Overview | Overview | yes |
| Usage - Maps | Maps | yes |
| Usage - Tools | Tools | no |
| Usage - Domain Class | Org. Type | no |
| Usage - Domains | Domains | no |
| Usage - Region | Country | no |
| Usage - Partners | Partners | no |

The first enabled plugin's tab is the default view.

### Overview

Charts and tables covering the whole hub, over one of five periods —
**Prior 12 Months**, **By Month**, **By Quarter**, **By Calendar Year**, and
**By Fiscal Year**.

The tab has two halves:

- **Users** — **Visits** and **Downloads**, each with a chart over time and a
  breakdown **Identified by Residence** and **Identified by Organization**.
- **Simulation** — **Simulation Users** and **Simulation Jobs**, each as a
  chart over time.

Its plugin settings are **End Year**, **End Month** (both blank meaning
"now"), and a **Message** shown to readers.

All of this reads `summary_user_vals`, `summary_simusage` and
`summary_simusage_vals` in the statistics database.

### Tools

A ranked table, chosen from a **Show data for** drop-down and a time period.
The nine rankings that ship with the hub are:

- Top Tools by Ranking
- Top Tools by Simulation Users
- Top Tools by Interactive Sessions
- Top Tools by Simulation Sessions
- Top Tools by Simulation Runs
- Top Tools by Simulation Wall Time
- Top Tools by Simulation CPU Time
- Top Tools by Simulation Interaction Time
- Top Tools by Citations

Unlike the other tabs, this one reads the hub's own database —
`#__stats_tops` for the list of rankings and `#__stats_topvals` for the
figures. `#__stats_tops` is seeded at install; `#__stats_topvals` is not, and
no code in this repository writes to it. Until the metrics tooling fills it,
the tab renders its drop-down and an empty table.

### Org. Type, Domains, Country, and Partners

Four "top list" tabs, each a table of the leading entries for a period. They
read `tops`, `topvals`, `classes`, `classvals`, `regions`, `regionvals` and
`totalvals` in the statistics database, and each reports a missing table by
name when it is not there.

### Maps

The Maps tab embeds a pre-rendered map in an iframe. The `type` parameter
picks which: `online`, `us-maps`, `tools`, `web_all`, `web_gradient`,
`animation`, and `whoisonline`. The default is `online`, a live map of
sessions in `#__xsession` with users, guests and bots counted per location.
A location comes from the `ipLATITUDE` and `ipLONGITUDE` columns on
`#__xsession`, not from the user's profile — so a user who fills in an
address does not appear on the map, and no code in this repository writes
that table at all.

> **Warning:** Every map template except `online` loads Google Maps
> JavaScript API **v2**, which Google retired in 2013. Those maps do not
> render, whatever key you supply. `online` uses the current Maps JavaScript
> API, which needs a key with billing enabled on the Google account. Treat
> the Maps tab as unmaintained.

## Is any of it wired up?

Short answer: not by anything in this repository.

- Nothing in this repository writes `summary_user_vals`,
  `summary_simusage`, `summary_simusage_vals`, `tops`, `topvals`, `classes`,
  `classvals`, `regions`, `regionvals`, `totalvals`, `ipmap`, or `location`.
  They belong to a separate statistics database maintained by the hub's
  metrics tooling.
- Nothing in this repository writes `#__stats_topvals` or `#__xsession`
  either, even though both tables are part of the hub's own schema.
- The pre-rendered maps, plots and charts under the configured paths are
  produced outside the hub as well.

So a hub gets a working `/usage` only when the metrics collection is
installed and running alongside it, and the component's options point at the
database it fills. Without that, publish no menu item to `/usage`: the page
is public, and a visitor reaching it sees missing-table errors rather than an
empty report.

Per-user figures are a different feature, served by the **Members - Usage**
plugin on a member's profile and described in the
[Usage chapter](../../users/28-usage.md) of the Hub users book. That plugin
reads `#__resource_stats_tools`, `#__author_stats` and
`#__metrics_author_cluster` in the hub's own database — tables the hub reads but
does not fill, so it has the same prerequisite.
