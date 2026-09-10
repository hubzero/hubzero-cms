<!--
status: rewritten
reviewed-against: 2.4-main @ 1924c22171
reviewed: 2026-09-09
screenshots: none
source: https://help.hubzero.org/documentation/240/users/usage
source-id: 3332
modified: 2016-08-30
imported: 2026-09-09
-->
# Usage

The **Usage** tab of a member area summarises what that member has
contributed to the hub and how much use those contributions have had. It is
supplied by the Members - Usage plugin, and it appears on every member's
profile, not only your own.

Read the warning at the end before you rely on any of the numbers. Half of
this page reports figures the CMS does not collect.

## Finding it

1. Log in and go to your member area at `/members/myaccount`.
2. Select **Usage** in the tab list down the left-hand side.

If the tab is not there, the hub has not enabled the plugin.

A line at the top of the tab reads *Usage is calculated on the last day of
every month*, with a link to the hub-wide [`/usage`](#the-hub-wide-usage-page)
page.

## What the tab shows

### Table 1: Overview

| Row | What it counts |
|---|---|
| **Contributions** | Published, publicly accessible resources you are credited on, excluding ones where your only role is submitter. |
| **Rank by Contributions** | Your position when every contributor on the hub is ordered by that count. |
| **First Contribution** | The publication date of your earliest. |
| **Last Contribution** | The publication date of your most recent. |
| **Citations on Contributions** | Distinct citations recorded against resources you are credited on. |
| **Usage in Courses/Classrooms** | How many users, classes, and schools have used your contributions in a course. Only shown when there is data. |

### Table 2: Simulation Tool Usage

One row per simulation tool you are credited on: **Users served in last 12
months**, **Simulation Runs in last 12 months**, **Total users served**,
**Total Simulation Runs**, **Citations**, and the date it was **Published**,
with totals underneath.

### Table 3: "and more" Usage

The same shape for your other contributed resources — anything that is not a
tool — with users served over twelve months and in total.

## What actually works

The two halves of this tab have different sources, and only one of them is
filled in by the CMS.

**Computed live, and reliable:** contributions, rank, first and last
contribution, and citations. These are counted from the hub's own resource,
authorship, and citation tables every time you open the tab.

**Read from tables nothing in the CMS writes:** every "users served" and
"simulation runs" figure, and the courses and classrooms row. Those come from
`#__author_stats`, `#__resource_stats_tools`, and
`#__metrics_author_cluster`. The tables are created when the hub is installed,
but no code in the CMS ever puts a row in them. They are filled by the hub's
metrics tooling, which is separate software installed alongside the hub.

> **Warning:** On a hub that has not installed and run that metrics tooling,
> tables 2 and 3 list your tools and resources with zeros in every usage
> column, and the classroom row does not appear. That is not a sign that
> nobody used your work; it means nothing is collecting the figures. Ask your
> hub's support staff whether metrics collection is running before drawing any
> conclusion from a zero.

If the statistics tables are missing entirely, the whole tab is replaced by
*Required database table not found.*

## <a id="the-hub-wide-usage-page"></a>The hub-wide usage page

`/usage` is a different page: the hub's own statistics, not yours. Its tabs
cover visits, downloads, simulation users and simulation jobs over a choice of
time periods, and where a hub enables them, breakdowns by country,
organisation type, domain, and partner, plus a map.

It has the same prerequisite, and more of it — `/usage` reads a whole separate
statistics database that nothing in this repository writes, and most of its
maps use a Google Maps API retired in 2013. A hub without metrics collection
shows missing-table errors there rather than an empty report. The terms it
uses are defined in
[Simulation usage definitions](25-simusagedefinitions.md); the administrator's
view of the same problem is in
[Usage](../managers/09-components/37-usage.md) in the Hub managers book.
