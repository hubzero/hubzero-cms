<!--
status: rewritten
reviewed-against: 2.4-main @ 009ec973b7
reviewed: 2026-09-10
screenshots: none
-->
# Poll

Poll runs single-question, multiple-choice polls: one question, up to twelve
answers, one vote per browser per poll. Two things about it decide whether it
is any use to you, and you need both before you touch anything else.

**A poll cannot be selected.** The module and the results menu item both ask
for a poll with a form field of type `poll`, and no such field type exists
anywhere in the source tree. The field does not render, so nothing can be
pointed at a particular poll, and everything falls back to the newest open,
published poll. In practice **your hub runs one poll at a time.**

**Saving a poll can corrupt other polls.** Saving writes every option box
back — including the blank ones — keyed by database row number rather than by
position within the poll, so blank boxes are written as updates to option
rows belonging to *other* polls. The full warning is under
[Creating or editing a poll](#creating-or-editing-a-poll); read it before you
edit anything.

Between them, those two facts mean the same thing: keep one poll in the
system, get it right before you publish it, and do not go back and edit it.

## Whether your hub needs it

A poll is a way of asking your members one question in passing, on a page
they were visiting anyway, and getting a rough answer without anyone filling
in a form. Which of three dates suits people for a workshop; whether anyone
still uses the old version of a tool. It costs a visitor one click and it
costs you nothing to run.

The typical case: you are scheduling a training session and want a rough
sense of which of three weeks people prefer. Three options, a fortnight on
the front page, and a number good enough to book a room by.

**What it is not:** it is not a survey, and it is not a vote. There is one
question, the answers are anonymous and not tied to accounts, and repeat
voting is stopped by nothing stronger than a cookie. Anything you would
report to a funder, or anything where it matters that each person votes once,
belongs in a survey tool outside the hub. See
[Whether to use it](#whether-to-use-it) at the foot of this page.

It is the oldest component in the hub — it predates most of the rest and has
been carried forward largely untouched — and while it does work, several of
the routes into it are broken. Read
[What works and what does not](#what-works-and-what-does-not) next.

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

## Running one poll, safely

Given both faults, this is the only sequence that avoids them. Following the
training-session example.

1. Check the Polls list first. If a poll is already there, this one will
   replace it as the poll every module and menu item shows, and — because of
   the save fault — creating a new poll is safer than editing the old one.
   Set the old poll's **Open** to No and unpublish it.
2. Press **New**. Write the **Title** as the question a visitor will read.
3. Fill in the option boxes from the top, with no gaps. Get all three
   options right now: this is the last save that is safe to make.
4. Set **Lag** to something above zero — the form offers 86400, one day,
   which is a sensible value for a poll you intend to leave up for a
   fortnight.
5. Leave **Published** at Unpublished and press **Apply**. Use **Preview** to
   see the poll as a visitor would.
6. When it reads correctly, set **Published** to Published and **Open** to
   Yes, and **Save**.
7. If the poll is to appear on the front page, go to
   **Extensions > Module Manager**, create a **Poll** module, give it a
   template position and publish it — and a **Poll title** module above it if
   you want the invitation. No module instance exists until you make one, so
   there is nothing to switch on. There is nothing to configure either: the
   module shows the newest open, published poll, which is now this one, and
   its **Poll** parameter does not render.

When the fortnight is up, set **Open** to No. The poll stays visible and
shows its results instead of a voting form, and no further votes are taken.
That is safer than deleting it, and it leaves the numbers where you can read
them.

> **Warning:** Step 6 is the last time you should open this poll's edit form.
> Every later save re-writes the blank option boxes and may blank options in
> other polls. If the question turns out to be wrong, it is safer to
> unpublish this poll and create a replacement than to correct it.

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

Create instances of them under **Extensions > Module Manager**; see
[Modules](../10-extensions/01-modules.md).

## Options

The component has no settings — there is nothing here to get wrong and
nothing to tune. Its configuration screen has only a
**Permissions** tab, setting per user group who may configure it, reach its
screens, and create, delete, edit or change the state of a poll. Access
Administration Interface is what admits you to the screens at all.

## Whether to use it

If you want a quick opinion poll on the front page and one at a time is
enough, it works. If you want several polls at once, per-member voting, or
anything you intend to report on, this component will not give it to you — use
a survey tool outside the hub and link to it.
