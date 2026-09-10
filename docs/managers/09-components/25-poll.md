<!--
status: rewritten
reviewed-against: 2.4-main @ be0bd4c772
reviewed: 2026-09-10
screenshots: none
-->
# Poll

Poll runs single-question, multiple-choice polls: one question, up to twelve
answers, one vote per browser per poll. It is the oldest component in the
hub — it predates most of the rest and has been carried forward largely
untouched — and while it does work, several of the routes into it are broken.
Read [What works and what does not](#what-works-and-what-does-not) before you
plan anything around it.

## What works and what does not

Polls are reachable. There is an administrator screen under **Components >
Poll**, three menu item types, two site modules, and a `/poll` page. What
fails is the plumbing that connects them:

| Route in | State |
|---|---|
| **Components > Poll** in the administrator interface | Works. |
| `/poll` — the list of published polls, each with its voting form | Works. Voting from it works. |
| A **Poll** menu item, layout *View latest poll* | Works. Shows the newest open, published poll. |
| A **Poll** menu item, layout *Poll Layout* (the results page) | Renders, but the poll it should show cannot be chosen — see below. |
| The **Poll** module (`mod_poll`) | Renders the newest open, published poll. It cannot be pointed at a particular poll — see below. |
| The **Take the latest poll** button and the **Results…** links on `/poll` | Dead. Both build a `view=` parameter that the controller ignores, so both land back on `/poll`. |
| `/feedback` → **Take a Poll** | Works: it renders the **Poll** module, so it shows whatever that module shows. See [Feedback](16-feedback.md). |

The reason two of those rows say "cannot be chosen" is that both the menu item
type and the module declare a form field of type `poll` for picking one, and
no such field type exists anywhere in the source tree. The field simply does
not render. Whatever the module or the results menu item was supposed to point
at, it ends up showing the newest open, published poll instead.

The practical consequence: **you can run one poll at a time.** Publish it,
mark it open, and it becomes the poll every module and menu item shows. To
change polls, close the old one and publish a new one.

## The Polls list

Open it under **Components > Poll**. The list shows every poll with its
**Title**, **Published** and **Open** states, the number of **Votes**, how
many **Options** it has, and its **Lag**. All are sortable except the option
count.

Above the list, a search box matching the title — type and press **Go** — and
a state filter offering published and unpublished.

The toolbar offers **Options**, **Publish**, **Unpublish**, **Delete**,
**Edit**, **New** and **Help**, each shown only if you hold the matching
permission. **Delete** asks for confirmation and removes the poll, its
options and its recorded votes.

**Published** and **Open** are two separate switches and both matter:

- **Published** decides whether the poll exists as far as the site is
  concerned. An unpublished poll is invisible and cannot be voted in.
- **Open** decides whether it is still taking votes. A published, closed poll
  still appears, but shows its results instead of a voting form.

## Creating or editing a poll

| Field | Notes |
|---|---|
| Title | Required. |
| Alias | The URL segment. Generated from the title if you leave it blank. |
| Lag | Required, and must be greater than zero. Seconds before the same browser may vote again — see [How voting is counted](#how-voting-is-counted). The form offers 86400, one day. |
| Published | **Unpublished**, **Published** or **Trashed**. |
| Access Level | Which viewing level may see the poll. |
| Open | Yes or No. |
| Option 1 … 12 | The answers. Blank boxes are ignored on the site; only options with text are shown or counted. |

**Preview** opens the poll as a visitor would see it. **Save** and **Apply**
save; **Close** returns to the list and checks the record back in.

> **Warning:** Do not save an existing poll unless you mean to. Saving writes
> every option box back, including the blank ones, using the position of the
> box as a database row number. Those numbers are not confined to this poll,
> so saving a poll that has fewer than twelve options can overwrite options
> belonging to *other* polls with empty text, silently removing answers from
> them. Check your other polls after editing one. There is no fix for this in
> 2.4; the safe course is to get a poll right before you publish it, and to
> keep only one poll in the system.

## How voting is counted

A vote increments the chosen option's hit count, increments the poll's voter
count, and stores a timestamped row so the results page can report when the
first and last votes were cast.

Voting is **not** restricted to logged-in members, and it is not tracked per
account. What stops repeat voting is a cookie, named from the poll's id and
set to expire after **Lag** seconds. Clearing cookies, using a different
browser, or using a private window lets the same person vote again. Treat the
numbers as indicative, never as a count of distinct people.

## The results page

The results layout lists each option with its vote count, its share of the
total as a percentage, and a bar; below it, the number of voters and the dates
of the first and last vote, and a drop-down for jumping to another published
poll.

Because the poll it shows cannot be set from the menu item, the only reliable
way to reach a specific poll's results is by URL, using the poll's id and
alias: `/poll/12:staff-survey`.

## Modules

Two site modules use the component, both installed by default and both
unpublished until you place them:

- **Poll** (`mod_poll`) — the voting form for the newest open, published poll.
  Its **Poll** parameter does not render, as described above; its **Module
  Class Suffix** and caching parameters do.
- **Poll title** (`mod_polltitle`) — a one-line invitation, **Tell us what you
  think.** by default, meant to sit above the poll module.

Place them under **Extensions > Modules**; see
[Modules](../10-extensions/01-modules.md).

## Options

The component has no settings. Its configuration screen has only a
**Permissions** tab, setting per user group who may configure it, reach its
screens, and create, delete, edit or change the state of a poll. Access
Administration Interface is what admits you to the screens at all.

## Whether to use it

If you want a quick opinion poll on the front page and one at a time is
enough, it works. If you want several polls at once, per-member voting, or
anything you intend to report on, this component will not give it to you — use
a survey tool outside the hub and link to it.
