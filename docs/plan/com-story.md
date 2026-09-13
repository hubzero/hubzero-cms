<!--
status: draft
reviewed-against: 2.4-main @ b2f01c4958
reviewed: 2026-09-13
summary: Design and delivery plan for com_story, a Slashdot-style news component for Hubzero, and for the karma and moderation libraries it is built on. Forks 2.4-main at b2f01c4958, taking nothing from 2.4-dev.
-->

# com_story

A plan for building `com_story`: a news component that reproduces the way
old-school Slashdot worked — editor-curated stories fed by a public
submissions queue, threaded comments that carry a score, comment moderation
handed out to ordinary readers a few points at a time, metamoderation that
judges the moderators, and a karma number behind all of it. The karma
machinery is deliberately built *outside* the component, as a framework
library any other part of the hub can use.

This plan is written against the Slashcode tree
(`git clone https://git.code.sf.net/p/slashcode/git`, last commit `cdca8b680`,
2009) and against Hubzero as it stands on `2.4-main`. It shares no tables and
no code with any existing component; where it needs a platform convention it
names the convention rather than a component to copy from. See
[Fork point and conventions](#fork-point-and-conventions).

## Scope

What is being built:

- `Hubzero\Karma` — a framework library holding per-user reputation on named
  scales, with an append-only ledger, decay, banded gates and events.
- `Hubzero\Moderation` — a framework library holding the moderation credit
  economy: credits, reasons, the moderator log, a pluggable grantor, and an
  optional metamoderation layer. Generic over the kind of item being moderated.
- `com_karma` — the administrative and member-facing face of both libraries:
  scales, rules, reasons, the ledger browser, the metamoderation queue.
- `com_story` — stories, topics, sections, the submissions queue,
  discussions, scored threaded comments, and the front page.
- A set of small integration extensions: cron jobs, search, activity, tags,
  member profile tabs, and sidebar modules.

What is not being built: see [Deliberate omissions](#deliberate-omissions).
Where Slashdot's design had to be changed rather than merely ported, see
[Fit with the platform](#fit-with-the-platform).

## Source survey: what Slashdot actually is

Slashcode is a Perl/mod_perl application, so none of it ports directly. What
ports is the data model and the algorithms. This section records what was
found, with file references, so the implementation does not have to rediscover
it.

> **Important:** this tree's last commit is from 2009. It is what Slashdot
> *was*, not what Slashdot is. Where the site later reversed course on
> something described here, that reversal is evidence about the design — the
> people who ran it at scale for another decade decided against it — and not a
> gap for this component to fill. Unauthenticated posting is the worked
> example; see [Deliberate omissions](#deliberate-omissions).

### Content model

| Slashcode table | Purpose |
|---|---|
| `stories` | One row per published story: `stoid`, `sid` slug, `uid` (editor), `submitter`, `dept`, `tid` primary topic, `primaryskid` section, `hits`, `commentcount`, `discussion`, `is_archived` |
| `story_text` | `title`, `introtext`, `bodytext`, `relatedtext`, `rendered` — split off so the story list query never touches the bodies |
| `story_param` | Arbitrary key/value extension of a story |
| `story_topics_chosen` / `story_topics_rendered` | Many-to-many story/topic with weights |
| `topics` | `keyword`, `textname`, `image`, `submittable`, `searchable`, `storypickable` |
| `topic_nexus`, `topic_parents` | Topics form a weighted DAG; a "nexus" topic is a section front page |
| `submissions` | The public queue: `subj`, `story`, `tid`, `uid`, `note`, `weight`, `del` state |
| `discussions` | One discussion per story, plus standalone ones. `type` open/recycle/archived, `commentstatus` gate |
| `pollquestions` / `pollanswers` / `pollvoters` | Polls, optionally attached to a story via `stories.qid` |

Schema: `sql/mysql/slashschema_create.sql`.

The `stories`/`story_text` split and the `discussions` indirection are both
worth keeping. The topic DAG is not — a simple topic tree plus many-to-many
tagging covers everything a hub needs, and Hubzero already has `com_tags`.

### The comment model

`comments` + `comment_text`, threaded by `pid`. The score columns are the
interesting part:

| Column | Meaning |
|---|---|
| `points` | Current stored score, clamped to `[comment_minscore, comment_maxscore]` = `[-1, 5]` |
| `pointsorig` | Score the comment was born with |
| `pointsmax` | Highest score it ever reached |
| `tweak`, `tweak_orig` | A display-only penalty for low-karma posters, kept separate so further downmods still register |
| `reason` | The most recent (or most common) moderation reason |
| `karma_bonus` | Whether the poster's karma earned this comment a `+1` at post time |
| `len` | Body length, used for reader length bonuses |
| `lastmod` | uid of the last moderator |

A comment is born at `users_comments.defaultpoints` (1 for a logged-in user,
0 for anonymous), minus penalties if the poster's karma is below 0 or below
`badkarma`, plus 1 if their karma is above `goodkarma`. See
`Slash/Utility/Comments/Comments.pm:1578`.

The score a *reader* sees is not the stored score. `getPoints()`
(`Comments.pm:645`)
starts from `pointsorig + tweak_orig`, applies the accumulated moderations,
then adds a stack of per-reader modifiers before clamping again:

- long-comment and short-comment bonuses (reader-set thresholds)
- an anonymous-poster bonus or penalty
- a new-user penalty, by uid percentile
- a per-reason adjustment (`reason_alter_Funny = -1` is the classic)
- relationship bonuses: friend, foe, fan, freak, friend-of-friend,
  foe-of-friend
- the poster's karma bonus
- a subscriber bonus

Then the reader's `threshold` hides everything below it. **This is the single
most important behaviour to reproduce.** It means rendered comment HTML cannot
be shared between logged-in readers. On this platform that costs nothing:
[`plugins/system/cache/cache.php:54`](../../core/plugins/system/cache/cache.php)
only page-caches when `User::isGuest()`, so logged-in readers were never
page-cached to begin with, and guests all get the same default preferences and
therefore one cacheable rendering. The plan computes the stored score in SQL
and applies the modifier stack in PHP at render time, from one query per
discussion.

Reader preferences live in `users_comments`: `mode` (flat / nested / threaded
/ nocomment), `threshold`, `highlightthresh`, `commentlimit`, `commentspill`,
`commentsort`, `noscores`, `reparent`, `nosigs`, `maxcommentsize`.

### Moderation (M1)

Tables `moderatorlog` and `modreasons`
(`plugins/Moderation/mysql_schema.sql`).
Each reason carries a score delta `val`, a karma delta `karma`, an `m2able`
flag and a `fairfrac`. The stock set:

| id | Name | val | karma | m2able |
|---|---|---|---|---|
| 0 | Normal | 0 | 0 | no |
| 1–4 | Offtopic, Flamebait, Troll, Redundant | −1 | −1 | yes |
| 5–8 | Insightful, Interesting, Informative, Funny | +1 | +1 | yes |
| 9 | Overrated | −1 | −1 | no |
| 10 | Underrated | +1 | +1 | no |

`moderateComment()`
(`Moderation.pm:121`)
is the whole act: refuse if the user has no points or already moderated this
comment; compute the new score; if it would fall outside `[-1, 5]`, still log
the moderation as `active = 0` so it can be metamoderated, but change nothing;
charge `getModPointsNeeded()` points (always 1 for a downmod, configurable per
target score for upmods); charge tokens for a non-M2-able reason; move the
comment's score; move the poster's karma, clamped to `[minkarma, maxkarma]`;
notify the poster. A moderator who comments in a discussion loses the ability
to moderate in it, and moderating then commenting undoes the moderations
(`undoModeration`, `checkDiscussionForUndoModeration`).

### The point economy

This is the part people forget. Moderation points are not granted directly;
they condense out of a token pool.

`process_moderation.pl` runs hourly
(`plugins/Moderation/process_moderation.pl`):

1. Count comments posted since the last run; mint
   `tokenspercomment` (6) tokens for each.
2. `stirPool()` — take back unspent points from anyone granted points more
   than `mod_stir_hours` (96) ago, charging `mod_stir_token_cost` (2) tokens
   per stirred point, and recycle `tokperpt × stirred` tokens back into the
   mint.
3. Build the eligible pool: users who read at least `m1_eligible_hitcount`
   (3) story or comment pages recently, are willing to moderate, and have
   karma at or above `mod_elig_minkarma`. Sort by click count, chop off the
   bottom and top (`m1_pointgrant_start` … `m1_pointgrant_end` = 0.8888).
4. `factorEligibleModerators()` — reweight the list by the user's M2
   fair/unfair ratios and their spent-versus-stirred ratio, each with a
   configurable strength (1.3 by default, "1 = irrelevant, 2 = top user twice
   as likely").
5. Scatter the tokens randomly over that list, at most `maxtokens_add` (3) per
   user per pass.
6. `convert_tokens_to_points()` — take the users with the most tokens
   (at least half of `tokensperpoint × maxpoints`) and trade
   `tokensperpoint × maxpoints` = 40 tokens for `maxpoints` = 5 points.

Tokens also decay: `decayTokens()`
(`Slash/DB/Static/MySQL/MySQL.pm:714`)
removes `mod_token_decay_perday` (1) per day from anyone who has not visited
in `mod_token_decay_days` (14), or whose karma has fallen below
`mod_elig_minkarma − 3`.

The effect is that moderation capacity scales with site activity, is
distributed to readers rather than to the most active posters, cannot be
hoarded, and is biased toward people whose past moderation was judged fair.

### Metamoderation (M2)

`metamodlog` plus counters on `users_info`
(`plugins/Metamod/mysql_schema.sql`).
A user is shown `m2_comments` (10) moderations, at most once every `m2_freq`
(86400) seconds, and votes each fair or unfair. When a moderation collects
`m2_consensus` (9) votes, `reconcile_m2()`
(`plugins/Metamod/process_metamod.pl`)
resolves it against the `m2_consequences` table, which maps a fairness
fraction to four numbers — tokens to the fair voters, tokens to the unfair
voters, tokens to the moderator, karma to the moderator:

```text
0.00 =  0,   +2,  -100, -1
0.15 = -2,   +1,   -40, -1
0.30 = -0.5, +0.5, -20,  0
0.35 =  0,    0,   -10,  0
0.49 =  0,    0,    -4,  0
0.60 =  0,    0,    +1,  0
0.70 =  0,    0,    +2,  0
0.80 = +0.01, -1,   +3,  0
0.90 = +0.02, -2,   +4,  0
1.00 = +0.05,  0,   +5, +0.5
```

A unanimously-unfair moderation costs its moderator 100 tokens and a karma
point; a unanimously-fair one earns 5 tokens and, half the time, a karma
point. Voting with the consensus earns a trickle of tokens; voting against it
costs. Multipliers reward metamoderating early, deep in a thread, or on
comments that started at an unusual score, and `m2_consequences_repeats`
penalises a moderator who keeps modding the same person.

### Karma

One integer, `users_info.karma`, clamped to `[minkarma, maxkarma]` =
`[-25, 50]`, moved by: comment moderation (±1 per reason's `karma` value, with
`mod_down_karmacoststyle` optionally scaling a downmod's cost by how far the
comment fell from its peak, and `comment_karma_limit` capping what one comment
can cost), M2 verdicts on your moderations, and accepted story submissions.

It is consumed in four places:

- posting: below 0 and below `badkarma` (−10) each cost the comment a point
- posting: above `goodkarma` (25) earns the comment a `+1` bonus
- rate limiting: `comments_perday_bykarma = -1=2|25=25|99999=50`
- moderation eligibility: `mod_elig_minkarma`

And it is optionally hidden behind adjectives —
`karma_adj = -10=Terrible|-1=Bad|0=Neutral|12=Positive|25=Good|99999=Excellent`
— because showing the number invites gaming it.

### FireHose

The last thing Slashdot built, and the only late addition worth taking. A
unified popularity-ranked stream over every content type — stories,
submissions, journals, bookmarks, feeds, comments — with the raw queue exposed
publicly so reader votes, not editors, do the first pass of sorting.

The full implementation is large: `FireHose.pm` is 4,968 lines, its seven
tagboxes another 1,330, and it runs on the Tags/Tagbox async framework
(`Tags.pm` 3,077 + `Tagbox.pm` 1,024) plus the `globjs` object registry and a
separate per-user Clout system. Roughly ten thousand lines of Perl and a
background daemon.

But the scoring itself is small.
`tagboxes/FHPopularity/FHPopularity.pm` computes:

```text
popularity = base_for_color_level(type, state) + extra + Σ (voter_clout × ±1)
```

Color level is a 1–7 band derived from what the object is and what state it is
in — submission 5, journal 5 or 6, bookmark 7, story 1–3 by nexus, comment 4–7
by its own score — mapped to a starting score through `firehose_slice_points`
(`290,240 220,200 185,175 155,138 102,93 30,25 0,-20 -60,-999999`), so a
submission enters at 102 and a mainpage story at 290. Nods and nixes are up
and down votes weighted by the voter's clout, with one wrinkle: an
administrator's nod accompanied by a "maybe" tag is held back until it is
known whether any administrator nixed.

That is sixty lines. The ten thousand are plumbing — the globj registry so
tags, votes and tagboxes can key on any object; the async daemon; and a clout
system that exists only because Slashdot had no general reputation library.
Two of those three are unnecessary here, which is why a reduced version is in
scope. See [The ranked queue](#the-ranked-queue).

### Everything else

Surveyed and scoped out or deferred: Achievements, Hall of Fame, Zoo
(friends/foes), Journal, Bookmark, Remarks, Tags with Clout, Subscribe and
Daypass, HumanConf, skins, and the template system. Their dispositions are in
[Deliberate omissions](#deliberate-omissions).

## What Hubzero already provides

The build should lean on these rather than reinvent them.

| Need | Use |
|---|---|
| ORM, table naming, relations | [`Hubzero\Database\Relational`](../../core/libraries/Hubzero/Database/Relational.php) — table defaults to `#__{namespace}_{plural model name}` |
| Threaded comments | [`Hubzero\Item\Comment`](../../core/libraries/Hubzero/Item/Comment.php) — a `parent` column and nothing more, which is what four components already do. See [Comment threading](#comment-threading) |
| Serving the same models in site, group and other contexts | The `models/adapters/{base,site,group}` pattern, in six components — [`com_blog`](../../core/components/com_blog/models/adapters) is the clearest |
| Per-row ACL | `asset_id` on the row, `config/access.xml` (45 components have one), `User::authorise()` |
| Transactions | `transactionStart()` / `transactionCommit()` / `transactionRollback()` on [`Driver`](../../core/libraries/Hubzero/Database/Driver.php), implemented in [`Driver/Pdo.php`](../../core/libraries/Hubzero/Database/Driver/Pdo.php) |
| JSON columns | A `transform*()` accessor wrapping the column in a `Registry`, as [`com_blog`'s `transformParams()`](../../core/components/com_blog/models/entry.php) does |
| Voting | [`Hubzero\Item\Vote`](../../core/libraries/Hubzero/Item/Vote.php) — polymorphic `item_type`/`item_id` |
| Following | [`Hubzero\Item\Watch`](../../core/libraries/Hubzero/Item/Watch.php) |
| Activity feeds and digests | [`Hubzero\Activity`](../../core/libraries/Hubzero/Activity) |
| Scheduled jobs | `plugins/cron/*` with `onCronEvents()` |
| Install and upgrade | [`Hubzero\Content\Migration\Base`](../../core/libraries/Hubzero/Content/Migration/Base.php) and its macros (`addComponentEntry`, `addPluginEntry`, `addModuleEntry`, `setAssetRules`) |
| Badges, virtual currency | [`Hubzero\Badges`](../../core/libraries/Hubzero/Badges), [`Hubzero\Bank`](../../core/libraries/Hubzero/Bank) |
| Polls | [`com_poll`](../../core/components/com_poll) |

Two traps.

[`Hubzero\User\Reputation`](../../core/libraries/Hubzero/User/Reputation.php)
sounds like karma but is only a spam counter used by the spam jail. Leave it
alone. Feed its `spam_count` into karma as a source; do not overload it.

Component `composer.json` files still claim `"php": "^5.4"` — for example
[`com_forum`'s](../../core/components/com_forum/composer.json). That is stale.
[`core/composer.json`](../../core/composer.json) pins the platform at
`8.2.30`, so write modern PHP and do not plan around 5.4.

## Fit with the platform

Slashdot in 2009 was a pseudonymous general-interest site with hundreds of
thousands of readers. Most of its design ports cleanly, but four pieces do
not, and they are changed here rather than ported and left to fail quietly.

Two framing notes before the four.

**The academic hub is a deployment profile, not the audience.** It is the
likely first one, and the defaults below are chosen for it — but Hubzero is a
CMS anyone can deploy, including someone who wants to run a Slashdot. Where
the reasoning below is specific to a research community it says so, and the
governance range is expressed through ACL rather than baked in; see
[ACL, and the governance model](#acl-and-the-governance-model). Nothing here
should require a fork to run the other way.

**Sites start small, which is not the same claim as "hubs are small."** The
scale arguments below were first written about hub size, and that was the
weaker version of the point. Slashdot's constants encode Slashdot's
*maturity*, not its category: a brand-new Slashdot clone has a dozen users on
its first day too, and would be just as poorly served by a mint calibrated for
hundreds of thousands. The scale-gating applies to every deployment, in its
first year.

### The token economy does not survive a young site

Slashdot's mint is driven by comment volume: `tokens_per_comment` (6) tokens
per new comment, and a grant costs `tokens_per_point × max_points` = 40
tokens, scattered at most 3 per user per hourly pass, against a decay that
removes one token a day from anyone idle for two weeks.

A site posting 20 comments a day mints 120 tokens a day, which is workable. A
site posting five comments a day mints 30, and the decay eats them faster than
anyone reaches 40. The economy is self-balancing at Slashdot's scale and
degenerate below some threshold — and it does not fail loudly. It simply never
grants anyone anything, and nobody can tell whether that is a bug or the
design working as intended. This is not a hub-versus-Slashdot distinction: it
is true of Slashdot itself in its first year.

**Resolution.** The granting *policy* is pluggable, not merely parameterised.
The wallet, the moderator log and the spend path are unchanged; what varies is
what puts credits in the wallet. `IntervalGrantor` is the default and is what
most deployments will run indefinitely. `TokenPoolGrantor` is Slashdot's mint,
available to a site that outgrows the simple one. See
[The credit economy](#the-credit-economy).

### Moderation review needs a crowd even more than moderation does

`review_consensus` is nine votes per moderation before it reconciles, and the
parts that make review mean anything are statistical: the eligibility reweighting
will not compute a fairness ratio until a user has five judged moderations in
each direction, and the consequences table interpolates across eleven fairness
bands. Dropping the consensus to three does not scale review down; it replaces a
consensus mechanism with three people's opinions while keeping that
mechanism's costs, which at the bottom of the table are a hundred tokens and a
karma point.

**Resolution.** review ships disabled. The columns and the code are built, because
they cost nothing to carry and retrofitting them is painful, but a hub turns
review on only once it has the volume to support it. This moves review to
[phase 11](#delivery-phases), after the component is otherwise complete and
shippable; it and the reweighting work in phase 12 may never ship on a given
hub, and the plan says so rather than presenting them as inevitable.

### Friends and foes do not belong here

Hubzero has no social graph anywhere in the codebase. It has groups instead,
and that reads as a decision rather than an omission. A foe list
institutionalises antagonism in a community where comments carry real names
and institutional affiliations — and in this design it would exist solely to
feed six entries in a display-score stack, at the cost of a relationship
table, a derived fan/freak/friend-of-friend/foe-of-friend computation, a
management UI, and a set of privacy questions about who can see they have been
foed.

**Resolution.** Cut. The per-viewer modifier stack stays — it is the right
shape, and it still carries the length, anonymity, new-user, per-reason and
karma-bonus modifiers. If a social graph is ever wanted, it should be built
because something needs a social graph, not smuggled in through comment
scoring.

### "Points" is already taken, and it is a currency

Hubzero has a first-class user-facing Points system:
[`Hubzero\Bank\Teller`](../../core/libraries/Hubzero/Bank/Teller.php) with
`deposit()`, `withdraw()` and `hold()`, a market, a members profile tab and
`mod_mypoints`. A user told they have five points from moderation, next to
three hundred and forty points in the bank, will reasonably assume these are
the same thing — and the bank's are spendable on real goods.

**Resolution.** One user-visible quantity throughout, called **moderation
credits**. Slashdot's two-tier token/point split survives only inside
`TokenPoolGrantor` as an implementation detail, and tokens are never shown to
a user. Nobody ever needed to understand the token layer to use Slashdot, and
exposing it here would buy nothing.

### The front page goes stale first

The most likely way this component dies is not a bug. It is that the front
page depends on a handful of editors promoting submissions, nobody promotes
anything for three weeks, and readers stop visiting. On a research hub those
editors are busy academics rather than full-time staff, which makes it acute;
on any young site they are volunteers, which makes it likely.

FireHose was Slashdot's answer to exactly this: expose the raw queue publicly,
let reader votes do the first pass of sorting, and let editors work the top of
a ranked list instead of reading everything. It is easy to file that as a
big-site feature and it was originally rejected here on those grounds. That is
backwards — editorial load per editor is *worse* when there are few of them,
not better.

**Resolution.** A reduced version is in scope and lands with the submissions
queue rather than after it: a karma-weighted popularity score, a public ranked
queue, and the two editorial signals that made the console work. The ten
thousand lines of plumbing behind Slashdot's version stay out. See
[The ranked queue](#the-ranked-queue).

### Smaller adjustments

- **Reason vocabulary.** The reference implementation's set labels people —
  troll, flamebait, crank. Ours labels contributions, and is our own wording
  throughout: see [Reasons and anonymity](#reasons-and-anonymity). Reasons are
  per-`item_type` rows, so this is a seeding decision rather than an
  architectural one, but it is one to make deliberately. The mechanic worth
  keeping is a separable humour category with a reader-settable adjustment;
  the word for it does not have to be theirs.
- **Anonymous posting.** Karma needs stable identity, and anonymous comments
  correctly cannot move it. [`com_forum`](../../core/components/com_forum/site/controllers/threads.php)
  defaults `allow_anonymous` to 1; `com_story` makes it per-section and
  defaults it off for story discussions, so the karma signal is not thinned
  from the start.

### Where the fit is better than it was on Slashdot

- **Group-scoped stories.** The `Manager` and adapter pattern means each
  research group can have its own front page and submissions queue for close
  to free. For a hub that is arguably a stronger product than a single
  site-wide front page, and it is the reason the scope columns are carried
  from phase 1.
- **Real per-asset ACL.** Slashdot had a `seclev` integer and a hardcoded
  `authors_unlimited` check. Editorial roles — who publishes, who triages the
  queue, who moderates without spending credits — fall out of `access.xml`.
- **Karma has somewhere to go.** Slashdot had one contribution surface. A hub
  has answers, wiki, resources, support tickets and publications, and no
  cross-cutting trust signal today. Breaking karma out is better justified
  here than on the site the idea came from.

### Three gates, all approved

[`com_news`](../../core/components/com_news) is a stub — an API router and one
controller, no site or admin application — so there is no duplication there.
But `com_blog` plus `com_forum` already cover "posts with threaded comments,"
so the build has to justify itself against them. It does so in three
independent stages, and the dependency runs one way, so work can stop after
any of them and keep what it built.

| Gate | What it delivers | Needs com_story? |
|---|---|---|
| **A** | Site-wide karma: the library, its administration, and the first sources | No |
| **B** | Metered moderation applied to content that already exists — `com_forum` posts | No |
| **C** | The news component: stories, the submissions queue, scored comments | Yes |

All three are approved. Gate A answers a long-standing request for
contributor recognition and is the reason the karma library is a framework
library rather than a part of this component. Gate B is anticipated rather than
urgent — more moderation capacity is expected to be wanted — and it costs
little once the library exists, because `Hubzero\Moderation` is generic over
item type by construction. Gate C is the news component itself.

**An honest accounting of what Gate C adds**, since a crude version of comment
scoring already ships: `plg_hubzero_comments::_vote()` records votes into
`#__item_votes` and denormalises `positive` and `negative` onto
`#__item_comments`, and `com_blog` renders them.

| | Already exists | Gate C adds |
|---|---|---|
| Up and down votes on a comment | yes | — |
| A score that changes *display* — thresholds, per-viewer modifiers | no | yes |
| Reasons attached to a score change | no | yes |
| Metering — moderation is finite, voting is not | no | yes |
| Karma consequences for the author | no | yes |
| A submissions queue | no | yes |

"Turn on comment voting instead" is therefore a real alternative, and it is
rejected on the merits rather than by omission: it gives a counter, not a
filter, and nothing about it is metered or accountable.

### The empty queue

The likeliest way Gate C disappoints is not that it breaks. It is that the
queue ships empty.

Slashdot's submissions queue worked because a large readership existed before
the queue did. A site turning com_story on for the first time has no stories,
no submitters, and a page advertising its own emptiness — which is worse than
no page. So the deployment order is not the delivery order:

1. **Editor-published stories only.** Build something worth reading, and a
   readership.
2. **Turn on submissions** once there are readers worth submitting to.
3. **Turn on crowd moderation** once there are enough submitters and comments
   to be worth moderating.

Each step waits on evidence from the one before it. Notably, each is a
permission or configuration change rather than a deployment, because of how
[the governance model](#acl-and-the-governance-model) landed — step 3 is
granting `story.moderate` to registered users and nothing else.

## Fork point and conventions

This work forks `2.4-main` and takes nothing from `2.4-dev`. That branch is
220 commits ahead and carries the reformat — PSR-12, namespaced plugins and
migrations, class-based component loading, autoloading, `v1_0` API controllers
renamed to `v1r0`, and a rewritten `Hubzero\Database` — but it rebases often,
so pinning to it is not workable. `com_story` is therefore written in
`2.4-main`'s idiom and carried through that restructuring later along with
every other component.

What that means concretely:

| | |
|---|---|
| Code style | Tabs, Allman braces, `require_once` at the top of models |
| Plugins | Global classes — `class plgCronStory extends \Hubzero\Plugin\Plugin` |
| Migrations | Global classes extending `Hubzero\Content\Migration\Base` |
| API controllers | `storiesv1_0.php`, not `v1r0` |
| Component entry | `site/story.php` dispatching by `require_once`, not a loaded class |

Four consequences of taking no cherry-picks, each with what to do instead:

- **No `$casts`.** JSON columns get a `transform*()` accessor wrapping the
  value in a `Registry`, the way
  [`com_blog`'s `transformParams()`](../../core/components/com_blog/models/entry.php)
  does. Six columns need it: `params` on most tables, `reason_adjustments` on
  preferences, `review_consequences`, and the ledger's context blob.
- **No schema builders.** Migrations are hand-written `CREATE TABLE`
  heredocs, as every existing migration is. Roughly a dozen of them.
- **No transactional migrations.** Mitigated by the house style — wrap every
  statement in `if (!$this->db->tableExists(...))` so a re-run after a partial
  failure is safe — plus one rule this plan adds: **create tables in one
  migration and seed data in a separate one**, so a failed seed never leaves
  half-built schema behind.
- **Transactions themselves are available**, and the karma award must use
  them. `transactionStart()`, `transactionCommit()` and
  `transactionRollback()` are on
  [`Driver`](../../core/libraries/Hubzero/Database/Driver.php) and implemented
  in [`Driver/Pdo.php`](../../core/libraries/Hubzero/Database/Driver/Pdo.php).
  Writing a ledger row and updating the balance is one transaction, not two
  statements and a hope.

One schema note that follows from the last point: the karma ledger, the karma
balances and the moderation log are declared `ENGINE=InnoDB`, not the
`MyISAM` the 2017-era migrations use. Transactions require it, and a
write-heavy ledger under MyISAM's table-level locking would be a problem on
its own. Recent practice already does this —
`Migration20240815000000ComForum.php` creates its table as InnoDB.

## The shape of the build

```text
core/libraries/Hubzero/Karma/        reputation: scales, ledger, balances, gates
core/libraries/Hubzero/Moderation/   credits, grantors, mod log, reasons, review
core/components/com_karma/           admin + member UI over both libraries
core/components/com_story/           stories, topics, submissions, comments
core/plugins/karma/*                 karma sources, one per contributing component
core/plugins/cron/karma/             decay, recompute
core/plugins/cron/moderation/        grant credits, expire, reconcile reviews
core/plugins/cron/story/             publish queue, archive, freshen counts
core/plugins/search/story/           indexing
core/plugins/members/karma/          profile tab
core/modules/mod_story_*             sidebar modules
```

The dependency direction is one-way: `com_story` depends on `Hubzero\Moderation`,
which depends on `Hubzero\Karma`, which depends on nothing. `com_karma` is the
UI for the two libraries and knows nothing about stories. That is what makes
the karma system reusable, and it is worth being strict about it.

## Part 1: `Hubzero\Karma`

### Why a library and not a component

Karma is only interesting if things outside the component that generates it
can read it. Question rate limits, wiki edit approval, support ticket
triage, group join requests, resource review weighting — all of them want to
ask "is this person trusted?" without knowing that the answer came from
comment moderation. Putting the store in a library, the administration in
`com_karma`, and the arithmetic in plugins keeps that possible.

### Tables

Namespace `karma`, so `protected $namespace = 'karma'` yields these names.

`#__karma_scales` — a named dimension of reputation.

| Column | Type | Notes |
|---|---|---|
| `id` | int | |
| `alias` | varchar(100) | `global`, `story.comment`, `answers` |
| `title` | varchar(255) | |
| `description` | text | |
| `floor` | int | default −25 |
| `ceiling` | int | default 50 |
| `initial` | int | default 0 |
| `decay_per_day` | float | 0 = no decay |
| `decay_after_days` | int | inactivity before decay starts |
| `decay_toward` | int | usually the initial value, not zero |
| `visibility_self` | enum | `exact`, `adjective` — default `adjective` |
| `visibility_public` | enum | `exact`, `adjective`, `opt_in`, `hidden` — default `hidden` |
| `adjectives` | text | `-10=Terrible\|-1=Bad\|0=Neutral\|12=Positive\|25=Good\|99999=Excellent` |
| `state`, `ordering`, `params` | | |

`#__karma_ledgers` — append-only. Never updated, only inserted and pruned.

| Column | Type | Notes |
|---|---|---|
| `id` | bigint | |
| `scale_id` | int | |
| `subject_id` | int | whose karma moves |
| `actor_id` | int | who caused it; 0 for the system |
| `delta` | float | signed, before clamping |
| `applied` | float | what actually landed after clamping |
| `rule` | varchar(100) | `comment.upmod`, `submission.accepted` |
| `source_type` | varchar(100) | `com_story.comment` |
| `source_id` | int | |
| `expires` | datetime | null for permanent; set for time-limited karma |
| `state` | tinyint | 1 active, 0 reversed |
| `created` | datetime | |
| `params` | text | JSON context for the audit trail |

Indexes: `(subject_id, scale_id, state)`, `(source_type, source_id)`,
`(scale_id, created)`, `(expires)`.

`#__karma_balances` — the materialised current value, so the hot path is one
indexed row read rather than a `SUM()`.

| Column | Type | Notes |
|---|---|---|
| `user_id`, `scale_id` | int | composite key |
| `karma` | float | clamped |
| `raw` | float | unclamped running total, so a user who earned their way to the ceiling does not fall off it on one downmod |
| `positive_count`, `negative_count` | int | |
| `last_event` | datetime | |
| `last_recalculated` | datetime | |

`#__karma_rules` — what a source is worth, editable by an administrator rather
than hard-coded.

| Column | Type | Notes |
|---|---|---|
| `id`, `scale_id` | int | |
| `alias` | varchar(100) | matches the `rule` written to the ledger |
| `title`, `description` | | |
| `delta` | float | |
| `daily_cap` | int | most this rule can move one user per day |
| `per_source_cap` | int | most this rule can move one user from one source |
| `requires_karma` | int | actor must have at least this much for the award to count |
| `state` | tinyint | |

The caps matter. Without them, karma is farmable by anyone who can generate
events.

`#__karma_gates` — banded thresholds, the generalisation of
`comments_perday_bykarma`.

| Column | Type | Notes |
|---|---|---|
| `id`, `scale_id` | int | |
| `alias` | varchar(100) | `com_story.comments_per_day` |
| `bands` | text | `-1=2\|25=25\|99999=50` |
| `default_value` | varchar(100) | value for users with no balance row |

### API

```php
use Hubzero\Karma\Karma;

// Read
Karma::of($userId);                        // global scale
Karma::of($userId, 'story.comment');
Karma::describe($userId, 'story.comment', $viewer); // "Good", or "23", or null
Karma::rank($userId, 'global');                     // percentile, cached

// Write, always through a rule so caps and clamps apply
Karma::award($subjectId, 'comment.upmod', [
    'scale'  => 'story.comment',
    'actor'  => $moderatorId,
    'source' => $comment,               // anything with getType()/getId()
    'params' => ['reason' => 'substantive'],
]);

// Undo, for when a moderation is reversed
Karma::revoke('com_story.comment', $commentId, 'comment.upmod');

// Gate
Karma::gate('com_story.comments_per_day', $userId);   // => 25
Karma::atLeast($userId, 0, 'story.comment');          // => bool

// What the gates currently buy this user, for showing them
Karma::standing($userId);   // => ['com_story.comments_per_day' => 25, 'moderate' => true, ...]
```

`award()` writes the ledger row, applies the caps, updates the balance in one
statement, and fires events. It never throws on a missing rule — an unknown
rule alias is logged and ignored, so a component can emit events before an
administrator has configured what they are worth.

### Visibility

Who may see a karma value is four separate questions, not one, and the four
pull in opposite directions. A single obfuscation flag would have to be set
for the most sensitive of them and would then be wrong for the rest.

| Audience | Sees | Configurable |
|---|---|---|
| Administrators | The exact number and the full ledger | No |
| The user themselves | `visibility_self` — adjective by default, exact if the scale says so — plus the ledger entries that moved it | Per scale |
| Other users | `visibility_public` — `hidden` by default; `opt_in` defers to the user's own preference; `adjective` or `exact` publish it | Per scale |
| The user, about the gates | Always what the gates currently allow them | No |

`Karma::describe()` takes the viewer and resolves all of this in one place, so
no template has to reason about it. The per-user "show my karma publicly"
preference has effect only when the scale is set to `opt_in` — which is the
state the current design was missing, since it had no way for an administrator
to say "nobody may publish this."

**Show the consequence, not the score.** A user does not need to know they have
karma 23. They need to know they can post twenty-five comments today and that
they are eligible to moderate. `Karma::standing()` returns exactly that, and
the answer is shown to the affected user regardless of what the scale's
visibility settings say. This is a floor, not an option.

Two reasons. It is the information someone can actually act on — a number
without its consequences is not feedback. And it removes most of the incentive
to farm, because there is nothing to optimise toward except a step change that
is already visible. Slashdot showed an adjective and left the user to infer
what it bought them, which is the worst arrangement available: unactionable and
still tempting to grow.

The counterpart matters just as much. If karma restricts what somebody may do,
they are told so and told why, with the ledger entries that got them there. A
silent restriction reads as arbitrary and unappealable, and the people it hits
leave rather than improve.

> **Note:** obfuscation is not confidentiality. Karma is computed from public
> actions — upmods on public comments — and its effects are observable in any
> discussion, since birth scores and moderation eligibility both show. A
> motivated observer can infer anyone's band. The adjective bands are a soft
> screen against fixation and leaderboard-building, not an access control, and
> should never be described to a user as though they were.

Defaults belong to the scale, not the site. An `answers.helpfulness` scale
reads as a contribution metric and sits comfortably beside the citation counts
and contributor rankings a hub already publishes. A `story.comment` scale is
partly a conduct record — it encodes that people downmodded you — and
publishing that against a real name and an institutional affiliation is a
different proposition. Same mechanism, opposite sensible defaults.

### Events

- `onKarmaBeforeAward($subject, $rule, $context)` — a plugin returning `false`
  vetoes the award. This is where per-component abuse rules live.
- `onKarmaAfterAward($ledgerEntry, $balance)` — badges, notifications, bank
  deposits, anything that reacts.
- `onKarmaCrossThreshold($user, $scale, $from, $to, $threshold)` — fired when
  a balance crosses a configured boundary. This is what drives "you can now
  moderate" and "your posting privileges have been restricted" messages.

### Sources

A new plugin group, `karma`. Each plugin declares its rules and listens to its
own component's events:

```php
class plgKarmaStory extends \Hubzero\Plugin\Plugin
{
    public function onKarmaRules()   { /* array of rule definitions, seeded on install */ }
    public function onStoryCommentModerated($comment, $moderation) { /* Karma::award(...) */ }
    public function onStorySubmissionAccepted($submission) { /* ... */ }
}
```

Ship `plg_karma_story` with `com_story`. Write `plg_karma_forum` and
`plg_karma_answers` in the same pass, even though neither is strictly needed
for `com_story` — they are the proof that the break-out worked, and they are
each about a hundred lines.

### Decay and recompute

`plg_cron_karma` provides two jobs:

- `decayKarma` — for each scale with `decay_per_day`, move balances of users
  inactive for `decay_after_days` toward `decay_toward`, writing a ledger row
  for each so the history stays honest. Also expires ledger rows whose
  `expires` has passed and reduces the balance accordingly.
- `recalculateBalances` — rebuild `#__karma_balances` from the ledger in
  batches. Needed after a rule change, and as a repair job. Must be safe to
  run against a live site, so it works in chunks by `user_id` range and
  compares before writing.

### com_karma

Administration:

- **Scales** — list and edit, with a live preview of the adjective bands and
  of what each audience will see under the current `visibility_self` and
  `visibility_public` settings. The preview matters: the two settings interact
  with the per-user opt-in, and an administrator should not have to reason it
  out from a pair of dropdowns.
- **Rules** — grouped by scale, with the plugin that emits each one shown, so
  an administrator can see which rules have no source and which sources have
  no rule.
- **Gates** — band editor.
- **Ledger** — filterable by subject, actor, rule, source, date. This is the
  abuse-investigation tool and it deserves real effort; the equivalent on
  Slashdot (`dispModCommentLog`) is what admins actually lived in.
- **Reasons**, **Grants** and **Moderation review** dashboards — see Part 2. The
  Grants dashboard is the operational one: which grantor is running, how many
  users were eligible, how many credits were issued, and how many expired
  unspent.

Member-facing, under `/karma`:

- **what you can currently do** — `Karma::standing()`, shown first and shown
  always, whatever the scale's visibility settings say
- your own karma per scale, at whatever granularity `visibility_self` allows,
  with the recent ledger entries that moved it
- your unspent moderation credits and when they expire
- your moderation record, and how it was reviewed if review is on
- the moderation review queue, when review is on
- a "show my karma on my profile" toggle, present only for scales set to
  `opt_in` and absent otherwise, so the control never implies a choice the
  hub has not offered

`plg_members_karma` puts a summary on the profile through
`Karma::describe($user, $scale, $viewer)`, which resolves the scale setting
and the user's preference together. The plugin itself contains no visibility
logic — that rule lives in one place, in the library.

## Part 2: `Hubzero\Moderation`

Separate from karma because karma is a number about a person and moderation is
an economy of actions over items. Components that want reputation but not
crowd moderation take only the first.

One user-visible currency: **moderation credits**. A user has some number of
credits, spends them to moderate, and loses the unspent ones after a while.
That is the whole mental model, and no part of the UI says anything else. See
["Points" is already taken](#points-is-already-taken-and-it-is-a-currency).

### Generic over the moderated item

Everything is keyed by `(item_type, item_id)` in the same way
`Hubzero\Item\Vote` is. `com_story` registers `com_story.comment`;
`com_forum` could later register `com_forum.post` and get the whole system for
free. A registered type supplies an adapter implementing:

```php
interface Moderatable
{
    public function currentScore();
    public function scoreBounds();          // [min, max]
    public function applyScore($delta, $reasonId);
    public function authorId();
    public function containerId();          // discussion/thread, for "modded here, can't comment here"
    public function permalink();
}
```

### Tables

Namespace `moderation`.

- `#__moderation_reasons` — `alias`, `title`, `value`, `karma`, `m2able`,
  `listable`, `fair_fraction`, `unfair_title`, `item_type`, `ordering`. Per
  item type, so stories and forum posts can have different vocabularies.
- `#__moderation_logs` — `item_type`, `item_id`, `container_id`, `user_id`
  (moderator), `author_id` (moderated), `reason_id`, `value`,
  `credits_spent`, `active`, `score_before`, `ip` (advisory only — see
  [IP records](#ip-records)), `created`, plus the review
  columns `review_count`, `reviews_needed`, `review_status`.
- `#__moderation_reviews` — `log_id`, `user_id`, `value` (+1 fair / −1
  unfair), `active`, `created`.
- `#__moderation_wallets` — `user_id`, `item_type`, `credits`,
  `credits_expire`, `last_granted`, `total_mods`, `expired`, `up_mods`,
  `down_mods`, `last_m2`, the review fairness counters (`reviews_fair`, `reviews_unfair`,
  `up_fair`, `up_unfair`, `down_fair`, `down_unfair`, `reviews_voted_with`,
  `reviews_voted_alone`), and `tokens` — used only by `TokenPoolGrantor` and
  never displayed.
- `#__moderation_grants` — one row per grantor run per item type: how many
  users were eligible, how many were granted, how many credits were issued,
  how many expired unspent, and how many were spent since the last run. This
  is the operational visibility Slashdot only had through its stats tables,
  and without it the economy cannot be tuned. On a small hub it is also the
  first place to look when nobody can moderate.

### The moderator

`Hubzero\Moderation\Moderator`:

```php
$moderator = new Moderator($user);

$moderator->credits();                      // unspent credits
$moderator->canModerate($item);             // eligibility + already-modded + commented-here
$moderator->reasonsFor($item);              // reasons allowed at this item's score
$moderator->moderate($item, 'insightful');  // the whole act
$moderator->undoIn($container);             // reverse everything this user did here
```

`moderate()` reproduces `moderateComment()` faithfully:

1. Refuse without credits, unless the user holds the
   `karma.moderate.unlimited` permission (the equivalent of
   `authors_unlimited`).
2. Refuse a second moderation of the same item by the same user.
3. Compute the target score. If it falls outside the item's bounds, write the
   log row with `active = 0` — the moderation still counts for review — and stop.
4. Charge credits: 1 for a downmod, `credits_for_score[target]` for an upmod,
   plus `unm2able_surcharge` for a reason that cannot be reviewed.
5. Apply the score through the adapter.
6. Move the author's karma via `Karma::award()`, letting the karma library's
   caps and clamps do their work.
7. Notify the author, unless the comment is already at the floor.
8. Fire `onModerationApplied`.

The "moderated here, so you cannot comment here; commented here, so your
moderations here are undone" rule lives in `canModerate()` and `undoIn()`. It
is the single most effective anti-abuse rule Slashdot had and it must not be
dropped.

### The credit economy

Who gets credits is a *policy*, and on this platform it has to be swappable —
Slashdot's mint is the wrong default for a young site. So the library defines
eligibility once and delegates the handing-out:

```php
interface Grantor
{
    public function name();
    public function run($itemType, array $eligible);  // returns a grant report
}
```

`plg_cron_moderation` runs three jobs: `grantCredits` (hourly, calls the
configured grantor), `expireCredits` (hourly, takes back unspent credits older
than `credit_lifetime_hours`), and `reconcileReviews` (hourly, and a
no-op while review is off). Every run writes a `#__moderation_grants` row.

**`IntervalGrantor` — the default.** Each pass, take the eligible users who
have not been granted within `grant_interval_hours`, shuffle, and give
`credits_per_grant` credits to at most
`ceil(eligible × grant_fraction)` of them. Credits expire after
`credit_lifetime_hours`. Four parameters, no hidden state, and it produces
moderators on a hub with five comments a day. This is what almost every hub
will run.

**`TokenPoolGrantor` — Slashdot's mint.** Mint `tokens_per_comment` tokens per
new comment; recycle tokens from credits that expired unspent, charging
`expire_token_cost` per credit; scatter tokens over the eligible list at most
`max_tokens_add` per user per pass; convert `tokens_per_credit ×
credits_per_grant` tokens into a grant for the users holding the most.
Tokens live in `#__moderation_wallets.tokens` and are never shown to a user.

The token pool is the better mechanism once a hub has the traffic for it,
because it ties moderation capacity to activity automatically instead of to an
administrator's guess. It is also the one that fails silently when the traffic
is not there. Ship it, default to the other one, and let
`#__moderation_grants` tell an administrator when to switch.

Eligibility is shared by both and needs a readership signal, which Hubzero has
no `accesslog` equivalent for. Two options, and the plan takes the first:

1. A lightweight `#__moderation_activities` table written by the component
   when a discussion is viewed — `(user_id, item_type, day, count)`, upserted,
   pruned after 48 hours. Only logged-in users are tracked, and logged-in
   users are never page-cached, so this write never sits on a cached path.
2. Derive it from `Hubzero\Activity\Log`. Rejected: the activity log is about
   authored actions, not reads.

Eligibility is then, in order: **holds `story.moderate`**, has read at least
`eligible_hitcount` discussions in the window, has `willing_to_moderate` set,
karma at or above the scale's `moderate` gate, account older than
`min_account_age_days`, and not currently jailed by
`Hubzero\User\Reputation`.

The permission check comes first because it is also the off switch. A hub that
grants `story.moderate` to nobody has an empty eligible pool, so no grantor
issues anything and moderation belongs entirely to whoever holds
`story.moderate.unlimited` — which is the editor-only arrangement, reached
without a special mode or a null grantor. See
[ACL, and the governance model](#acl-and-the-governance-model).

The reweighting step (`factorEligibleModerators`) — biasing the eligible list
by a user's review fairness ratios — is genuinely clever and genuinely hard to get
right, and it is meaningless without review history. It is a late phase, behind a
config flag defaulting off, and on a hub that never enables review it never turns
on at all.

### Moderation review

> **Important:** review ships disabled and stays that way until a hub has the
> volume for it. Nine votes per moderation, five judged moderations in each
> direction before a fairness ratio means anything, and eleven interpolated
> bands in the consequences table — none of that survives a site producing a
> few moderations a week. The code and columns are built anyway, because
> retrofitting them is painful and carrying them is free. See
> [Moderation review needs a crowd](#moderation-review-needs-a-crowd-even-more-than-moderation-does).

`Hubzero\Moderation\Reviewer`:

```php
$m2 = new Reviewer($user);
$m2->isEligible();                    // karma gate, frequency gate
$m2->deal($count = 10);               // pick moderations to judge
$m2->record($logId, +1);              // fair
$m2->commit();                        // writes the batch
```

Dealing follows Slashdot: pick reviewable moderations that have not yet reached
consensus, excluding the user's own moderations, their own comments, and
anything they have already judged; weight the pick by age using
`review_pick_bias`.

Reconciliation runs in cron, not inline. For each log row that has reached
`review_consensus` votes: compute the fairness fraction, look it up in the
consequences table, move credits for the fair voters, the unfair voters and
the moderator, and move the moderator's karma through `Karma::award()`. Apply
the early-moderation, thread-depth and starting-score multipliers, and the
repeat-target penalties. Update the fairness counters that feed
`factorEligibleModerators`.

The consequences table is stored as component configuration in the same
`fraction = a,b,c,d` form Slashdot used, with an editor in `com_karma` that
renders it as a table and validates monotonicity. Slashdot's numbers are in
tokens, which on a hub are one-eighth of a credit; the seeded table scales
them to credits and rounds, which flattens several of the middle bands to
zero. That is the correct outcome — those bands were a rounding artifact of a
very large site — but it means the table must be reviewed rather than
transcribed.

The editor should also refuse to enable review when the last thirty days produced
fewer than `review_consensus × 10` reviewable moderations, and say why. A moderation review
system that cannot reach consensus is worse than none: moderations sit in
`review_status = 0` forever and the fairness counters never populate.

## Part 3: `com_story`

### Tables

Namespace `story`.

`#__story_sections` — the front page and its siblings, Slashdot's nexus under
a name the rest of the CMS already uses for the same idea. `id`, `title`, `alias`, `description`,
`scope`, `scope_id`, `access`, `state`, `asset_id`, `ordering`, `params`
(stories per page, whether it appears in the main index, its own feed title).

`#__story_topics` — `id`, `parent_id`, `title`, `alias`, `description`,
`image`, `submittable`, `searchable`, `state`, `ordering`, `access`,
`asset_id`. A tree, not a DAG.

`#__story_stories` — the list-page row, deliberately without the bodies.

| Column | Notes |
|---|---|
| `id`, `alias` | alias is the URL slug, unique within a day |
| `section_id`, `topic_id` | primary section and topic |
| `title`, `kicker` | the `kicker` is the wry one-line subtitle under the headline, and it is not optional — it is half the voice of the thing |
| `state` | draft, queued, published, archived, trashed |
| `publish_up`, `publish_down` | the queue runs on `publish_up` |
| `created`, `created_by` | the editor |
| `submitter_id`, `submission_id` | credit and provenance |
| `modified`, `modified_by` | |
| `discussion_id` | |
| `hits`, `comment_count` | denormalised |
| `poll_id` | optional `com_poll` question |
| `scope`, `scope_id`, `access`, `asset_id` | |
| `params` | |

`#__story_texts` — `story_id`, `intro`, `body`, `related`, `rendered`,
`word_count`, `body_length`. One-to-one, loaded only on the article page.

`#__story_storytopics` — `story_id`, `topic_id`, `weight`.

`#__story_submissions` — the public queue.

| Column | Notes |
|---|---|
| `id`, `subject`, `body`, `url` | |
| `topic_id`, `section_id` | suggested |
| `created`, `created_by`, `email` | |
| `ip` | advisory only, nothing reads it automatically; see [IP records](#ip-records) |
| `state` | pending, hold, accepted, rejected, spam |
| `note` | the editor's shorthand note |
| `popularity` | karma-weighted reader votes plus a state base; see [The ranked queue](#the-ranked-queue) |
| `editor_popularity` | the same over voters holding `story.publish`, tracked separately |
| `attention_needed` | flag for "a human has to decide this" |
| `last_scored` | when popularity was last recomputed, for the decay pass |
| `decided_by`, `decided_at`, `story_id` | |
| `params` | |

Reader voting on submissions uses `Hubzero\Item\Vote` with
`item_type = com_story.submission`, so nothing new is needed to record a vote.
What the votes are *worth* is [The ranked queue](#the-ranked-queue).

`#__story_discussions` — `id`, `story_id`, `title`, `type` (open, recycle,
archived), `comment_status` (disabled, enabled, logged_in, karma_gated),
`comment_count`, `created`, `last_activity`, `access`, `asset_id`.

`#__story_comments` — the comment tree, plus the score columns from Slashdot.
See [Comment threading](#comment-threading) for why it is shaped this way.

| Column | Notes |
|---|---|
| `id`, `discussion_id`, `parent` | `parent` is the authoritative structure |
| `path`, `depth` | derived from `parent`, rebuildable; `path` sorts the tree and selects a subtree |
| `subject`, `comment` | |
| `created`, `created_by`, `modified`, `modified_by` | |
| `anonymous` | display-only; `created_by` is still recorded |
| `ip` | advisory only, nothing reads it automatically; see [IP records](#ip-records) |
| `state`, `closed` | |
| `score`, `score_original`, `score_max` | stored, clamped |
| `tweak`, `tweak_original` | display-only penalty, kept separate |
| `reason_id`, `last_moderator_id` | |
| `karma_bonus` | whether the poster's karma earned a point at post time |
| `length` | body length, for reader length bonuses |
| `signature` | |

`#__story_preferences` — one row per user, the `users_comments` equivalent:
`mode`, `threshold`, `highlight_threshold`, `comment_limit`, `comment_spill`,
`sort`, `hide_scores`, `reparent`, `hide_signatures`, `max_comment_size`,
`default_score`, `willing_to_moderate`, plus the modifier columns
`bonus_long`, `bonus_short`, `length_long`, `length_short`,
`bonus_anonymous`, `bonus_new_user`, `new_user_percent`, `bonus_karma`, and a
JSON `reason_adjustments` map.

There is no relationship table. Slashdot's friend/foe bonuses are cut — see
[Friends and foes do not belong here](#friends-and-foes-do-not-belong-here).
The modifier stack is built so a future modifier is an added row and an added
line, not a redesign.

`#__story_reads` — `user_id`, `discussion_id`, `last_comment_id`,
`last_read`, for the "N new since your last visit" marker.

### Models

Under `Components\Story\Models`, extending `Relational`, in the file layout
every component uses: `section.php`, `topic.php`, `story.php`, `text.php`,
`submission.php`, `discussion.php`, `comment.php`, `preference.php`,
`manager.php`, `adapters/{base,site,group}.php`.

`Manager` takes the conventional scope signature — `new Manager('site', 0)`
— so a group-scoped story feed is a
configuration change rather than a rewrite. The group adapter is the reason to
keep `scope`/`scope_id` on every table from the start even though phase 1 only
uses `site`, and group-scoped stories are the most likely place this component
earns its keep on a hub: a front page and a submissions queue per research
group, rather than one site-wide feed competing with `com_blog`.

### Comment threading

`parent` is the truth. `path` and `depth` are derived from it, and can be
rebuilt from it at any time.

`path` is a fixed-width dotted string of ancestor ids —
`000123.000456.000789` — so `ORDER BY path` yields tree order in one index
scan, `WHERE path LIKE '000123.%'` selects a subtree for a permalink or a
descendant count, and inserting a reply writes one row and shifts nothing.
`depth` is the indent level, stored rather than counted so the renderer does
not parse the path.

**Why not a nested set, when `com_forum` has one.** Because `com_forum`'s is
not one. Three things are true of it on `2.4-main`, and all three still hold
on `2.4-dev`:

- Nothing reads `lft`/`rgt` as a range. The only two consumers in the
  component are sort keys — `->order(($threading == 'tree' ? 'lft' : 'id'),
  'asc')` in the thread view, and `rgt` as a reply-count proxy in the category
  sort. There is no `WHERE lft BETWEEN` anywhere.
- The tree is rebuilt in PHP from `parent` regardless, by `toTree()` and
  `treeRecurse()`.
- `destroy()` deletes replies recursively through `parent` and never closes
  the gap it leaves. There is no `lft - 2` pass, and no repair job anywhere in
  the component, its admin, or `plg_cron_forum`.

So it pays two unbounded `UPDATE ... SET lft = lft + 2` statements on every
reply — with no transaction or lock, so two concurrent replies to one thread
can interleave — to maintain an invariant nothing depends on and that any
deletion silently breaks.

That is the argument for making `path` *derived*. `com_forum`'s numbering
rotted precisely because it is not reconstructible from anything else: with no
ground truth there was nothing to write a repair against, so nobody wrote one,
so nobody noticed the drift. A `rebuildPaths($discussion)` pass over `parent`
is twenty lines, safe to run at any time, and turns the invariant from
aspirational into checkable. Phase 5 ships it alongside the tree, not later.

**And nothing is promoted to a trait.** Write it once, here, with the tests
below. If a second consumer ever wants it, promote then, with two real call
sites to design against. Generalising from one speculative call site is how
`com_forum` ended up with a nested set nothing uses.

The platform's own answer to threaded comments, for reference, is
[`Hubzero\Item\Comment`](../../core/libraries/Hubzero/Item/Comment.php): a
`parent` column and no ordering key at all. `com_blog`, `com_wiki`, `com_kb`
and `com_answers` each do the same. `com_story` adds `path` only because a
Slashdot-style discussion is read whole and in tree order on every page view,
and at the tail — a few thousand comments — rebuilding that order in PHP on
every request stops being free.

### IP records

The three tables that record an IP — comments, submissions and the moderation
log — store it as a plain advisory field. **Nothing in the design reads it.**
No feature groups by it, no rule blocks on it, no view displays it, and no
automated decision consults it. It is there so that an administrator
investigating a specific incident has one more piece of context, and that is
the whole of its role.

This is a deliberate demotion from Slashdot, where `ipid` and `subnetid` were
load-bearing: hashed identity for Anonymous Cowards, subnet grouping for abuse
sweeps, and an admin-visible display on every comment. All of that is out. The
column survives; the features built on it do not.

> **Warning:** on an academic hub a shared IP address is the *expected* case,
> not a signal. A whole campus commonly NATs to a handful of addresses, so two
> accounts posting from one address are far more likely to be two colleagues
> than one person with two accounts. Add CGNAT, IPv6 privacy extensions that
> rotate addresses daily, institutional VPNs for off-campus access, and
> Private Relay, and the field is high-false-positive and low-yield. Treat a
> match as a question worth asking, never as evidence, and never automate on
> it.

Two reasons it is safe to keep it anyway, both of which are also reasons not
to lean on it:

- **Account identity is always known.** Unlike Slashdot's Anonymous Coward,
  our `anonymous` flag is display-only — the row still carries `created_by`,
  and an account is required to comment. So the question IP existed to answer
  is already answered, correctly, by a column we can trust.
- **The real abuse instruments are behavioural and already in the plan.**
  Moderation collusion shows up in `#__moderation_logs` as repeated
  (moderator, author) pairs, which `review_repeat_penalties` already
  penalises. Submission spam goes through
  [`Hubzero\User\Reputation`](../../core/libraries/Hubzero/User/Reputation.php)
  and the existing spam jail. Rate limiting is a karma gate. Ban evasion is an
  account-creation concern and belongs to `com_members`. Every one of those
  works through a VPN, which IP does not.

Note that this makes `com_story` a mild outlier: `#__forum_posts` and
`#__item_comments` store no IP at all, and across the schema the column
otherwise appears only on logs, tracking, votes and sessions. The justification
is that a moderation economy has an adversarial surface a plain comment table
does not — but the bar for *using* the field stays where this section puts it.

Three obligations that come with keeping it:

- It is admin-visible only, never rendered to a reader, never exposed through
  the API.
- Account deletion and `muse user merge` must reach all three columns. A field
  nothing reads is exactly the field that gets forgotten in a purge path.
- A hub with an institutional incident-response requirement should satisfy it
  at the reverse proxy, under one retention policy, rather than treating these
  columns as a log.

One inherited column, for completeness: `Hubzero\Item\Vote` carries its own
`ip varchar(15)`, which submission votes land in. It is the platform's table,
it is nullable, and `com_story` leaves it unpopulated rather than reaching into
a shared table from inside this component.

### Menus

A component that cannot be pointed at from a menu is invisible to the people
who run the hub, so this is wiring, not polish, and it ships with the views
rather than after them.

[`com_menus`' `menutype.php`](../../core/components/com_menus/models/menutype.php)
discovers a component's menu item types by scanning `site/views/`. For each
view it takes `site/views/<view>/metadata.xml` if one exists, and otherwise
treats every `site/views/<view>/tmpl/<layout>.xml` as one selectable type. The
request it builds is `option=com_story&view=<view>`, with `layout=<layout>`
appended whenever the layout is not named `default`.

> **Note:** the third discovery path, a `site/metadata.xml` at the component
> root, is dead on `2.4-main` — `getTypeOptionsFromXML()` returns false on both
> branches of its own conditional. Do not write one; use the per-view files.

Each type file is a `<metadata>` document with a `<layout title="…">`, a
`<message>` that becomes the description in the type picker, and a
`<state><params>` block declaring the parameters an administrator fills in
when creating the item:

```xml
<metadata>
	<layout title="Section front page">
		<message><![CDATA[A section's stories, newest first]]></message>
	</layout>
	<state>
		<name>Section front page</name>
		<description>A section's stories, newest first</description>
		<params>
			<param name="section" type="text" label="Section alias" description="Enter a section alias" />
		</params>
	</state>
</metadata>
```

What `com_story` exposes:

| Menu item type | View and layout | Parameters |
|---|---|---|
| Story front page | `stories` / `display` | optional section alias |
| Section front page | `sections` / `display` | section alias |
| Topic front page | `topics` / `display` | topic alias |
| Submission queue | `queue` / `display` | — |
| Submit a story | `submissions` / `new` | optional section, topic |
| My submissions | `submissions` / `mine` | — |
| Story archive | `archive` / `display` | optional section |

And `com_karma`:

| Menu item type | View and layout | Parameters |
|---|---|---|
| My karma | `karma` / `display` | optional scale alias |
| Moderation review queue | `review` / `display` | — |

Views reached only by a route — a single story, a discussion, a comment
permalink, the edit forms — get `hidden="true"` on their layout node so they
do not clutter the picker. `com_forum` omits the files entirely for those,
which works, but the explicit flag says the omission was deliberate.

Two supporting pieces, both easy to forget:

- `admin/language/en-GB/en-GB.com_story.sys.ini` carries the strings the menu
  manager shows, in the conventional
  `COM_STORY_<VIEW>_VIEW_<LAYOUT>_TITLE` / `_OPTION` / `_DESC` triple. The
  `.sys.ini` is the one the menu manager loads; putting them in the ordinary
  `.ini` leaves the picker showing raw keys.
- `<administration><menu>Stories</menu></administration>` in `story.xml`, and
  the same in `karma.xml`, so both components appear in the admin
  **Components** menu at all.

The router's `parse()` must accept what these items produce. A menu item
pointing at `option=com_story&view=sections&layout=display&section=hardware`
and the SEF URL `/story/section/hardware` have to resolve to the same
controller and task, which is the usual place this wiring breaks. Phase 4
tests both forms for every type in the table above.

### Reasons and anonymity

Two seeding decisions that shape how the component reads, made deliberately
rather than inherited.

**Reasons.** `com_story.comment` seeds with eight of our own, four either
way:

| Reason | Value | Says |
|---|---|---|
| Substantive | +1 | adds something, rather than agreeing at length |
| Well-sourced | +1 | backed by something a reader can follow |
| Clarifying | +1 | made an earlier point easier to understand |
| Levity | +1 | funny, and welcome, and separable — see below |
| Tangential | −1 | about something else |
| Duplicative | −1 | already said, upthread |
| Unsupported | −1 | asserts what it does not show |
| Dismissive | −1 | argues with the person rather than the point |

All eight are reviewable. **Levity is the one worth keeping separate.**
A reader who wants only substance sets its per-reason adjustment to `−1` and
never sees jokes again; a reader who enjoys them leaves it alone. That single
option is what makes the per-reader modifier stack visibly worth having
rather than a theoretical nicety, and it is the reason comedy gets a category
of its own instead of being folded into Substantive.

The negative four are deliberately about the *contribution*, not the
contributor. Nothing here says troll, flamebait or crank. On a hub where
comments carry real names and institutional affiliations, a moderation
vocabulary that labels people rather than posts would be both unpleasant and
a liability; "Dismissive" is as close to conduct as this set goes, and it
still describes what the comment did.

**Anonymity.** Karma needs stable identity, and an anonymous comment correctly
cannot move anyone's. [`com_forum`](../../core/components/com_forum/site/controllers/threads.php)
defaults `allow_anonymous` to 1; `com_story` makes it a per-section setting and
defaults it **off** for story discussions, so the karma signal is not thinned
from the first day. A section that wants anonymous comment can still have
them; they are simply born at `anonymous_default_score` and stay outside the
karma economy in both directions.

### The ranked queue

The reduced FireHose, and the answer to the failure mode in
[The front page goes stale first](#the-front-page-goes-stale-first).

A submission carries a `popularity` float:

```php
$popularity = $base[$submission->state]          // pending 102, hold 93, …
            + $submission->votes()->reduce(fn($v) => $v->vote * $this->weightOf($v->created_by));
```

Four decisions make this cheap where Slashdot's was not:

- **Karma is the clout.** `weightOf()` is a bounded function of the voter's
  karma on the `story` scale — not a separate `users_clout` system. Slashdot
  needed one because it had no general reputation library; we are building
  one in phase 1, and this is its second consumer.
- **Synchronous, not a daemon.** A vote is one `UPDATE` against one row. There
  is no feederlog, no run queue and no tagbox framework, because there is only
  one scorer and one object type.
- **Two content types, not eleven.** Stories and submissions both live in
  `com_story` and share one index. That is where the cheap version stops; see
  below.
- **Colour bands are computed, not stored.** `popularity` maps to a display
  band at render time through a configured list of cut points, exactly as
  `getPopLevelForPopularity` did. No `set_color_ranges` job, no stored ranges.

`editor_popularity` is the same sum restricted to voters holding
`story.publish`. Keeping it separate is what let Slashdot's editors signal to
each other — "I would run this" — without moving the public ranking.
`attention_needed` is the other half of that workflow: a flag meaning a human
has to decide, set by an editor or by a rule (an unusually high-karma
submitter, a submission whose popularity is climbing fast).

`plg_cron_story` gains a `decaySubmissionScores` job: a submission's
popularity decays toward its state base over `popularity_halflife_hours`, so a
three-week-old entry sinks out of the way without anyone rejecting it, and
`last_scored` says when each row was last touched.

Public route: `/story/queue`, the ranked pending queue with the same voting
affordance readers get anywhere else. The editor's triage screen is the same
data sorted by `editor_popularity` and `attention_needed` instead.

**What stays out.** The `globjs` registry and the tagbox daemon buy a
*site-wide* stream spanning blog, wiki, resources and publications. That is a
platform project, not a component one, and it is the honest boundary: unifying
stories and submissions gets most of the value here precisely because both are
already in this component. Also out: clout types, FHBayes spam prediction,
thumbnails, sprites, and the eleven-type union.

> **Warning:** karma weights submission votes, and an accepted submission
> awards karma — a feedback loop. Slashdot had the same loop, and the gain was
> small enough not to matter across hundreds of thousands of accounts; at hub
> scale a handful of coordinated accounts is a much larger share of the
> electorate. Bound it in two places: put a `per_source_cap` on the
> `submission.accepted` rule, and never award karma for *casting* a submission
> vote. Both are one line each, and both are far cheaper now than after the
> first farming incident.

### The comment score pipeline

The most important code in the component, and the easiest to get wrong.

`Comment::displayScore(Preference $prefs, Context $ctx)` returns both the
final integer and the breakdown, because the breakdown is shown to the reader
on hover, exactly as Slashdot did:

```php
$score = clamp($this->score_original + $this->tweak_original);
$score += $this->moderationDelta();      // clamp(score+tweak) - clamp(orig+tweak_orig)

$mods = [];
if ($prefs->bonus_long  && $this->length > $prefs->length_long)  $mods['long']  = $prefs->bonus_long;
if ($prefs->bonus_short && $this->length < $prefs->length_short) $mods['short'] = $prefs->bonus_short;
if ($this->anonymous)          $mods['anonymous'] = $prefs->bonus_anonymous;
if ($ctx->isNewUser($this))    $mods['new_user']  = $prefs->bonus_new_user;
if ($this->reason_id)          $mods['reason']    = $prefs->reasonAdjustment($this->reason_id);
if ($this->karma_bonus)        $mods['karma']     = $prefs->bonus_karma;

$score = clamp($score + array_sum($mods));
```

`Context` is built once per discussion and holds the current maximum user id
for the new-user percentile and the reason table. Constructing it costs one
query. Scoring a comment after that costs nothing.

The rendering rules that go with it:

- Below `threshold`: render a one-line stub with the subject, author, score
  and a link to expand. Do not omit it — the stub is what makes the threshold
  feel like a filter rather than censorship.
- At or above `highlight_threshold`: full body, highlighted.
- Longer than `max_comment_size`: truncate with a "read the rest" link, unless
  the comment is the one the URL points at.
- `comment_limit` / `comment_spill`: show at most `comment_limit` full
  comments; past that, collapse to stubs.
- Modes: threaded (`path` order, indented by `depth`), nested (`path` order,
  flat), flat (chronological), and none.

None of this can be cached as HTML across readers. It can and should be cached
as *data*: one query returns the discussion's comment rows `ORDER BY path`,
and the cache key is the discussion plus its `last_activity`.

### Controllers and routes

Site controllers under `site/controllers/`:

| Controller | Tasks | Route |
|---|---|---|
| `stories` | `display` (front page), `article`, `feed` | `/story`, `/story/2026/09/13/a-slug` |
| `sections` | `display`, `feed` | `/story/section/hardware` |
| `topics` | `display`, `feed` | `/story/topic/linux` |
| `comments` | `display`, `new`, `save`, `edit`, `delete`, `moderate`, `preferences` | `/story/comments/<discussion>` |
| `submissions` | `display`, `new`, `save`, `vote`, `mine` | `/story/submit` |
| `queue` | `display`, `vote` | `/story/queue` — the public ranked queue |
| `archive` | `display` | `/story/archive/2026/09` |

`site/router.php` implements the conventional `build()`/`parse()` pair over
[`Hubzero\Component\Router\Base`](../../core/libraries/Hubzero/Component/Router). Dated story URLs are worth the extra parsing: they
are what the format is known for, and they make archive pages fall out for
free.

Moderation posts to `comments/moderate` and is also exposed as an AJAX
endpoint, mirroring Slashdot's `ajaxModerateCid`, so a moderator can work
through a discussion without a page reload. Both paths go through the same
`Moderator::moderate()`.

Admin controllers: `stories`, `sections`, `topics`, `submissions`,
`comments`, in the conventional `admin/controllers` plus `admin/views/*/tmpl`
layout. The submissions screen is
the one that needs design attention — it is where editors spend their time,
and it wants keyboard-driven accept/reject/hold, inline editing of the title
and body on the way to publication, and a visible weight from reader votes.

### ACL, and the governance model

`config/access.xml` declares sections for `component`, `section`, `topic`,
`story`, `discussion` and `comment`, each with the standard
create/edit/edit.own/edit.state/delete actions, in the shape all 45
`config/access.xml` files share. `config/config.xml` carries the matching
`<field name="rules" type="rules" component="com_story" section="component" />`
that renders the per-group permission grid, as every other component's does.

Four extra component-level actions:

| Action | Grants |
|---|---|
| `story.publish` | turn a submission into a story; access the triage screen |
| `story.moderate` | be eligible to earn moderation credits and spend them |
| `story.moderate.unlimited` | moderate without spending credits |
| `story.review` | access the review queue at all |

**This is the governance model.** There is no separate setting for it. Who
holds `story.moderate` decides whether moderation is a crowd activity or a
staff one, and the usual arrangements are ACL recipes rather than modes:

| Arrangement | `story.moderate` | `story.moderate.unlimited` |
|---|---|---|
| Editor-only | nobody | editorial group |
| Delegated moderators | nobody | a moderators group |
| Metered crowd | registered users | — |
| Editors plus metered crowd | registered users | editorial group |
| Full Slashdot | registered users | — , with review on and the token pool grantor |

The fourth row is the one a mode enum could not express, and is probably what
most deployments want: staff who moderate freely and readers who earn credits,
on the same site at the same time.

Two things follow. **Holding `story.moderate` is the first eligibility check
for a credit grant** — see [The credit economy](#the-credit-economy) — so
withholding the permission empties the eligible pool, nothing is granted, and
nobody can moderate. The off switch is a permission, not a config value, and
no null grantor is needed. And what remains as actual configuration is two
values: which grantor runs, and whether review is on.

### API

`api/controllers/` with `storiesv1_0`, `submissionsv1_0`, `commentsv1_0` and
`topicsv1_0`, plus `api/router.php` — the `v1_0` naming that `2.4-main` uses.
Submissions over the API are what makes an RSS-to-submission bridge or a bot
possible, which is how a small hub keeps a story queue full.

## Part 4: the rest of the site

| Extension | Purpose |
|---|---|
| `plg_cron_story` | publish the queue on `publish_up`, decay submission popularity, archive discussions past `archive_after_days`, refresh comment counts |
| `plg_search_story` | index stories and comments; a matching Solr adapter |
| `plg_whatsnew_story` | stories in the What's New listing |
| `plg_tags_story` | tag stories through `com_tags` |
| `plg_activity_story` | story published, submission accepted, comment posted |
| `plg_members_karma` | karma and moderation record on the profile |
| `plg_karma_story` | the rules that turn story events into karma |
| `mod_story_latest` | recent stories, for a sidebar |
| `mod_story_submissions` | queue depth and the top pending submissions by `editor_popularity`, for editors |
| `mod_karma_mine` | your karma and your unspent moderation credits |
| `mod_story_poll` | the current story poll, via `com_poll` |

## Configuration

`com_story`'s `config/config.xml` and `com_karma`'s carry the tuning
parameters. Slashdot's values are the right starting point wherever the
mechanism is unchanged — they are the product of a decade of live tuning. They
are *not* the right starting point for the credit economy, where the mechanism
itself was changed.

Scoring and karma, unchanged from Slashdot:

| Parameter | Default | Meaning |
|---|---|---|
| `comment_min_score` | −1 | |
| `comment_max_score` | 5 | |
| `comment_default_score` | 1 | |
| `anonymous_default_score` | 0 | |
| `allow_anonymous` | off | per section; see [Reasons and anonymity](#reasons-and-anonymity) |
| `karma_floor` / `karma_ceiling` | −25 / 50 | |
| `karma_good` | 25 | earns a `+1` bonus on posts |
| `karma_bad` | −10 | costs a point on posts |
| `karma_adjectives` | `-10=Terrible\|-1=Bad\|0=Neutral\|12=Positive\|25=Good\|99999=Excellent` | |
| `comments_per_day_by_karma` | `-1=2\|25=25\|99999=50` | |
| `karma_bonus_max_downmods` | 2 | downmods before the karma bonus is lost |

The credit economy. The defaults describe `IntervalGrantor`, sized for a hub,
not for Slashdot:

| Parameter | Default | Meaning |
|---|---|---|
| `grantor` | `interval` | `interval` or `tokenpool` |
| `credits_per_grant` | 5 | |
| `credit_lifetime_hours` | 96 | unspent credits expire |
| `grant_interval_hours` | 72 | minimum between grants to one user |
| `grant_fraction` | 0.15 | share of the eligible pool granted each pass |
| `credits_for_score` | `1` at every target | raise for upmods to 4 and 5 if the top of the range is being overused |
| `unm2able_surcharge` | 0 | 1 once review is on |
| `eligible_hitcount` | 3 | discussions read in the window |
| `min_account_age_days` | 30 | |

`TokenPoolGrantor` adds `tokens_per_comment` (6), `tokens_per_credit` (8),
`max_tokens_add` (3), `expire_token_cost` (2) and `grant_band` (0.0–0.8888).
These are Slashdot's numbers and they assume Slashdot's traffic.

The ranked queue. Slashdot's `firehose_slice_points` assumed a stream carrying
every content type; these are re-derived for two:

| Parameter | Default | Meaning |
|---|---|---|
| `queue_public` | on | expose `/story/queue` to readers |
| `popularity_base` | `pending=100\|hold=90\|accepted=120\|rejected=0` | starting score by state |
| `popularity_bands` | `200\|150\|110\|90\|60` | cut points for the display colour bands |
| `popularity_halflife_hours` | 168 | decay toward the state base |
| `vote_weight_by_karma` | `-1=0\|0=1\|12=2\|25=3` | a voter's clout, bounded at both ends |
| `editor_votes_separate` | on | keep `editor_popularity` out of the public ranking |
| `attention_needed_auto` | off | let a rule set the flag, not just an editor |

Moderation review, off by default:

| Parameter | Default | Meaning |
|---|---|---|
| `reviews_enabled` | off | |
| `review_consensus` | 9 | votes to resolve, forced odd |
| `reviews_per_session` | 10 | per session |
| `review_frequency` | 86400 | seconds between sessions |
| `review_consequences` | the table above, rescaled to credits | |

> **Note:** `#__moderation_grants` is the instrument for all of this. If it
> shows credits being issued and expiring unspent, the grant is too wide or
> the lifetime too short. If it shows few eligible users, the problem is
> `eligible_hitcount` or `min_account_age_days`, not the grantor. If it shows
> nothing granted at all under `tokenpool`, switch to `interval` — the hub
> does not have the traffic.

## Delivery phases

Each phase ends with something that works and can be demonstrated. The phases
are grouped by the three gates in
[Three gates, all approved](#three-gates-all-approved); each gate ends at
something deployable on its own.

### Gate A — site-wide karma

Delivers a reputation system usable anywhere on the hub. Nothing here depends
on `com_story`, and if the later gates never happened this would still stand.

**Phase 1 — karma library.** `Hubzero\Karma` with scales, ledger, balances,
rules, gates, the facade, the events, and unit tests for clamping, caps and
revocation. Migrations that create the tables and seed a `global` scale. No UI.
*Done when* `Karma::award()` and `Karma::of()` round-trip under test and the
migration runs up and down cleanly.

**Phase 2 — com_karma administration.** Scales, rules, gates and ledger
browser in the admin, the per-scale visibility settings with their preview,
and `Karma::describe()`/`Karma::standing()` behind them. `plg_cron_karma` with
decay and recompute.
*Done when* an administrator can define a scale and a rule, award karma from a
test harness, watch it appear in the ledger and browser, see it decay, and
flip a scale through all four `visibility_public` states with the member view
and the profile changing accordingly — including `opt_in`, where the toggle
appears and the user's choice decides.

**Phase 3 — the first karma sources.** `plg_karma_answers` and
`plg_karma_forum`, their rule definitions seeded on install, and at least one
`Karma::gate()` consumed outside `com_story`. `plg_members_karma` on the
profile.
*Done when* answering a question or having a forum post well received moves a
visible karma balance, an administrator can retune what each is worth without
touching code, and a gate somewhere on the hub reads that balance. This is the
phase that proves the break-out worked, and it is deliberately before any of
`com_story` rather than after all of it.

**Gate A is complete here.** The hub has karma, an administration UI for it,
sources feeding it, and at least one consumer. Stopping at this point leaves
nothing half-built.

### Gate B — moderation of existing content

Delivers metered crowd moderation, applied first to the forum. Still nothing
that depends on `com_story`.

**Phase 4 — moderation library, M1 only.** `Hubzero\Moderation`: reasons,
wallets, the moderator log, `Moderator::moderate()`, the `Grantor` interface
with `IntervalGrantor` behind it, and `plg_cron_moderation`'s grant and expire
jobs. The `Moderatable` interface, with a test-double adapter. No token pool,
no review.
*Done when* the cron job grants credits to a synthetic eligible pool, a test
user spends them on a test item, karma moves, unspent credits expire on
schedule, and `#__moderation_grants` records every pass.

**Phase 5 — moderation on the forum.** Register `com_forum.post` as a
moderatable type with its own `Moderatable` adapter and reason set, the
moderation affordance in the thread view, and the permission wiring. Forum
posts gain a moderated score, not a per-viewer one — the modifier stack stays
in `com_story`.
*Done when* a forum post can be moderated by a credit holder, the author's
karma moves, and the whole arrangement can be switched off by revoking one
permission.

**Gate B is complete here.** Crowd moderation exists and is in use, on content
that predates this project. Note what has *not* been built yet: no stories, no
submissions queue, no per-viewer scoring.

### Gate C — the news component

**Phase 6 — com_story skeleton.** Tables, models, scope adapters, the site
router, sections, topics, stories with text, the front page, the article page,
admin CRUD for all of them, the menu item types and their `.sys.ini` strings,
and the admin menu entry. No comments yet.
*Done when* an editor can create a topic and a story in the admin, reach it on
the front page at a dated URL, build a menu item for every type in
[Menus](#menus) and have each one resolve to the same place as its SEF URL,
and the feeds validate.

**Phase 7 — discussions and comments.** The comment tree — `parent`
authoritative, `path` and `depth` derived, `rebuildPaths()` shipping with it —
posting, editing, deleting, threading modes, the per-section anonymity
setting, and the preferences screen, but with a fixed score of 1 and no
moderation.
*Done when* a threaded discussion renders in all four modes, the tree survives
deletion of an interior comment, and `rebuildPaths()` over a deliberately
corrupted `path` column restores it exactly.

**Phase 8 — scoring.** The birth score with karma bonus and penalty, the
per-viewer modifier stack, thresholds, highlighting, stubs, truncation, limit
and spill. `plg_karma_story` and the rules that go with it.
*Done when* two readers with different preferences see demonstrably different
scores on the same comment, with a correct breakdown on each.

**Phase 9 — moderation in the component.** Register `com_story.comment` as a
moderatable type, seed its reason set, the moderation UI in the discussion,
the AJAX endpoint, the commented-here/moderated-here exclusion, the undo path,
and notification of the moderated author. The four permission arrangements in
[ACL, and the governance model](#acl-and-the-governance-model) are wired here.
*Done when* a user holding credits can moderate a comment, the score and the
author's karma both move, commenting in the same discussion reverses it, and
each of the four arrangements behaves as tabulated — in particular, revoking
`story.moderate` from everyone leaves moderation working for holders of
`story.moderate.unlimited` and impossible for anyone else, with no grantor
change.

**Phase 10 — submissions and the ranked queue.** The submission form, reader
voting via `Hubzero\Item\Vote`, karma-weighted popularity with its decay job,
the public ranked queue at `/story/queue`, `editor_popularity` and
`attention_needed`, the editor's triage screen, accept-into-story with
provenance, and the karma award for an accepted submission — capped, with
vote-casting awarding nothing.
*Done when* a reader can submit, other readers' votes move it up the public
queue by an amount that depends on their karma, an old submission sinks on its
own, and an editor can accept the top entry into a draft story in one screen.

The ranking is not a later addition to this phase. It changes
`#__story_submissions` and the triage screen, so it costs four columns now and
a migration plus a UI rewrite later.

**Phase 11 — integration.** Search, tags, activity, what's new, the modules,
the API controllers, the member profile tab.
*Done when* a story is findable in site search, appears in activity feeds, and
the sidebar modules render.

**Gate C is complete here**, and the component is shippable. The two phases
below are scale-gated: a hub runs them when it has outgrown the simple grantor and
has the volume to make moderation review mean something. Many hubs never will,
and that is a successful outcome, not an unfinished one.

**Phase 12 — moderation review.** `Reviewer`, the dealing algorithm, the review
queue under `/karma/review`, cron reconciliation against the rescaled
consequences table, the fairness counters, and the admin guard that refuses to
enable review below the volume threshold.
*Done when* nine users can judge one moderation, reconciliation fires, and the
moderator's credits and karma move by the amounts the table specifies.

**Phase 13 — token pool and eligibility reweighting.** `TokenPoolGrantor`,
`factorEligibleModerators` behind its flag, and the grants dashboard.
*Done when* a hub can switch grantors without losing wallet state, and
`#__moderation_grants` shows a stable issue-to-spend ratio over two weeks.

Phases 1 and 2 have no visible output and will feel slow. They are also the
phases that determine whether the karma system is reusable or is secretly a
part of `com_story`. Do not compress them — and note that phase 3 exists
specifically so that the slow start ends in something the hub can use, rather
than in a library waiting on a component.

Phase 3 deliberately ships the *simple* grantor first. The token pool is the
more interesting mechanism and the temptation is to build it first; resist it.
Building `IntervalGrantor` first forces the `Grantor` seam to exist, and the
seam is what makes the token pool optional later instead of load-bearing
now.

## Testing

- Unit tests under each library's `Tests/` directory, following
  [`Hubzero\Tests`](../../core/libraries/Hubzero/Tests). Cover: clamping at
  both bounds, the raw-versus-clamped balance behaviour, daily and per-source
  caps, revocation, decay arithmetic, band lookup at and between boundaries.
- `Karma::describe()` is a pure function of (scale settings, user preference,
  viewer relationship, value). Table-driven tests over every combination of
  `visibility_self`, `visibility_public` and the opt-in preference, for a
  viewer who is the subject, another user, and an administrator. Twenty-odd
  cases, and the one to assert hardest is that an administrator always gets the
  exact number and a stranger never gets more than the scale allows.
- `Karma::standing()` returns the same answer whatever the visibility settings
  are. One test that flips every visibility column and asserts the standing
  output does not move.
- Both grantors need a deterministic test: fix the random seed, run the
  grantor against a synthetic eligible pool, and assert the number of grants
  and the expiry schedule. Run `TokenPoolGrantor`'s test at two traffic
  levels — one Slashdot-sized, one hub-sized — and assert that the hub-sized
  one grants *something*. That assertion is the regression test for the whole
  reason `IntervalGrantor` exists; if it ever passes trivially, the default
  has silently changed.
- The review consequences table is a pure function from a fairness fraction to
  four numbers. Test it exhaustively at every boundary in the table, against
  the credit-rescaled values rather than Slashdot's token values.
- Comment scoring is also pure given a comment, a preference row and a
  context. Table-driven tests over the whole modifier stack.
- Submission popularity is a pure function of a state base and a set of
  (vote, voter karma) pairs. Table-driven tests over it, including the decay
  pass at several ages, and one test asserting that a voter at the karma floor
  cannot move a submission further than a voter at the ceiling — the farming
  bound, expressed as a test rather than a comment.
- Comment tree integrity. Test insert, delete-leaf, delete-interior and
  reparent, asserting after each that `path` and `depth` agree with what
  `rebuildPaths()` would produce from `parent` alone. That equality is the
  whole invariant, and making it a one-line assertion is the reason `path` is
  derived rather than independent — it is exactly the check `com_forum`'s
  `lft`/`rgt` cannot be given.

## Deliberate omissions

Recorded so they are decisions rather than oversights.

| Slashdot feature | Disposition |
|---|---|
| FireHose, in full | Out: ~10k lines of Perl, an async tagbox daemon, a globj registry and a parallel clout system, to unify eleven content types |
| FireHose's popularity ranking | **In**, reduced, over stories and submissions only, with karma as the clout and no daemon. See [The ranked queue](#the-ranked-queue) |
| Achievements | Out of `com_story`. `Hubzero\Badges` already exists and `onKarmaAfterAward` is the hook it needs |
| Hall of Fame | A module over the karma balances, deferred to after phase 9 |
| Journals | Out. `com_blog` covers it |
| Bookmarks, Remarks | Out |
| Tag clout | Out. `com_tags` has no weighting and adding it is its own project |
| Subscriptions, Daypass | Out. The subscriber score bonus is left as an unused column and a config flag |
| Skins | Out. Hubzero templates cover it |
| The template system (`templates` table) | Out. Hubzero has views |
| `karma_obfuscate` as one site-wide flag | Replaced by per-scale `visibility_self` and `visibility_public`, plus a standing view that is never hidden. See [Visibility](#visibility) |
| Zoo (friends, foes, fans, freaks) | Out, and not deferred. See [Friends and foes do not belong here](#friends-and-foes-do-not-belong-here) |
| The token pool as the only grantor | Replaced by a pluggable `Grantor`, with the pool as the opt-in option. See [The token economy does not survive a young site](#the-token-economy-does-not-survive-a-young-site) |
| Moderation review as a default | Built but shipped off, and guarded by a volume check |
| Tokens as a user-visible quantity | Out. One currency, moderation credits; tokens survive only inside `TokenPoolGrantor` |
| Unauthenticated posting (Anonymous Coward proper) | Out, and this one is settled by the reference implementation rather than by us — Slashdot dropped it. The 2009 tree shows what it already cost to sustain: `comments_perday_anon` (a per-IPID daily cap), `comments_portscan` (scanning the poster's IP for open proxy ports 80/8080/8000/3128), `comments_anon_speed_limit` with an escalating multiplier, `subnet_karma_post_limit_range` (blocking by *subnet* reputation), and a `nopostanon` ban class. A portscanner and a subnet reputation system in support of one feature, and it was abandoned anyway |
| Anonymous Coward as a real uid | Replaced by the platform's `anonymous` flag plus `created_by` — display-only anonymity over a known account, which is where Slashdot itself landed. Off by default per section |
| `ipid`/`subnetid` hashing, and every feature built on it | Out. The raw IP is recorded and nothing more — no hashing, no subnet grouping, no posts-by-this-address view, no automated action. See [IP records](#ip-records) |

## Decisions taken

Recorded so they are not re-litigated, with what settled each.

| Decision | Resolution |
|---|---|
| Build this at all? | Yes, in three gates, all approved. Gate A answers a long-standing request for contributor recognition; Gate B is anticipated moderation demand; Gate C is the news component. See [Three gates, all approved](#three-gates-all-approved) |
| Comment tree shape | `parent` authoritative, `path` and `depth` derived and rebuildable, in com_story's own table. Nothing promoted to a trait. See [Comment threading](#comment-threading) |
| Governance model | Not a component setting — an ACL arrangement. Who holds `story.moderate` and `story.moderate.unlimited` decides it per deployment. See [ACL, and the governance model](#acl-and-the-governance-model) |
| Karma visibility | Per-scale, per-audience, with a standing view that is never hidden. See [Visibility](#visibility) |
| IP records | Kept, advisory only, nothing reads it automatically. See [IP records](#ip-records) |
| Unauthenticated posting | Out — abandoned by the reference implementation after extensive scaffolding |
| Branch | Fork `2.4-main` at `b2f01c4958`, no cherry-picks. See [Fork point and conventions](#fork-point-and-conventions) |
| Granting policy | Pluggable; `IntervalGrantor` default, `TokenPoolGrantor` opt-in |
| Moderation review | Built, shipped off, volume-guarded |

Two things remain genuinely undecided, and both are deployment-time rather
than design-time:

1. **Which karma scales a given hub defines, and their visibility defaults.**
   The mechanism is settled; the policy is per-hub and belongs to whoever runs
   it. Phase 2 ships the administration for it.
2. **When to move from `IntervalGrantor` to the token pool, and whether to
   enable review at all.** Both are answered by `#__moderation_grants` once there
   is traffic to read, not in advance.
