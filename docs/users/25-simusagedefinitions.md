<!--
status: rewritten
reviewed-against: 2.4-main @ 42a7a5b5c7
reviewed: 2026-09-10
screenshots: none
source: https://help.hubzero.org/documentation/240/users/simusagedefinitions
source-id: 3329
modified: 2016-03-22
imported: 2026-09-09
-->
# Simulation usage definitions

The **Simulation** half of the hub's [`/usage`](28-usage.md) page reports ten
figures about the tools members have run. This chapter says what each one
means.

It is a reference, not a task. Read it when a figure on `/usage` is about to
go into a report and you need to be sure what it counts — *simulation users*
and *simulation runs* in particular are easy to misread, and the difference
between CPU time and wall time changes what a total means. If you are not
quoting one of these numbers, you do not need this page.

Two of them — **Simulation Users** and **Simulation Runs** — are drawn as
charts over the period you selected. The other eight are shown underneath as
single values for one point in time; selecting a point on either chart moves
them to that month.

> **Warning:** These figures come from a statistics database that the CMS
> reads but never writes. Nothing in this repository computes any of them.
> They are produced by the hub's metrics tooling, which is separate software.
> On a hub where that tooling is not installed and running, the simulation
> section of `/usage` reports a missing table rather than zeroes. See
> [Usage](../managers/09-components/37-usage.md) in the Hub managers book.

## The vocabulary

A **simulation run** — the page also calls it a job — is one unit of work
submitted by a tool: a program, its inputs, and the instructions needed to run
it. Starting a tool session is not a run; asking that tool to compute
something is.

A **simulation user** is a registered account, identified by its own login,
that submitted at least one run in the period.

## The ten figures

| Figure | Meaning |
|---|---|
| **Simulation Users** | Registered users who ran one or more simulation runs in the period. |
| **Simulation Runs** | Simulation runs submitted in the period. |
| **Total CPU Time** | Processor time spent executing those runs. A run using several cores at once accumulates CPU time on each of them, so this exceeds the elapsed time. It excludes anything the run was not computing during — queue waits, file I/O. |
| **Total Wall Time** | Elapsed time from a run starting to it finishing, as a stopwatch would measure it. Anything else the machine was busy with counts against it. |
| **Total Interaction Time** | Time users spent working inside tool sessions, as distinct from the compute time above. |
| **Users with > 10 mins of CPU Time** | Users whose runs consumed more than ten minutes of CPU time — a rough separation of real work from a first look. |
| **Avg. Number of Simulation Runs/User** | Simulation runs divided by simulation users, for the period. |
| **Avg. Time between First and Last Simulation** | Mean gap, per user, between their earliest and their latest run. A measure of how long people keep coming back. |
| **Repeat Users with > 10 Simulation Jobs** | Users who have submitted more than ten runs. |
| **Repeat Users with > 3 Months** | Users who came back after a gap of more than three months. |

> **Note:** Two of those labels are stored with a placeholder in them —
> `Simulation Users {5}` and `Repeat Users with > 3 Months {9}` — and nothing
> substitutes the placeholder, so the braces are shown to readers as they
> stand. It is a fault in the seeded label rather than in the figure beside
> it.

The labels themselves are rows in the statistics database, so a hub that
maintains its own metrics tooling can rename them or report different things
under the same names. If a label on your hub does not match this list, trust
the hub.
