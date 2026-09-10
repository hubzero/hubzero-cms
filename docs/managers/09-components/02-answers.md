<!--
status: rewritten
reviewed-against: 2.4-main @ 009ec973b7
reviewed: 2026-09-10
source: https://help.hubzero.org/documentation/240/managers/components/answers
-->
# Answers

Answers is the hub's question-and-answer forum. Members post a question,
other members answer it, everyone votes answers up or down, and the person
who asked picks the answer that helped. On the site it appears as
**Questions and Answers** at `/answers`. This chapter covers the
administrator's side; the [Hub users](../../users/19-questions.md) book covers
asking and answering.

## Whether your hub needs it

Answers is for the question that has an answer. A member is stuck on
something specific, someone who knows replies, and the reply is marked as
*the* answer so the next person to hit the same wall finds it in one look.
Enable it on a hub where members use tools or data they did not write and
will get stuck on — a hub around a simulation code, an instrument, a shared
dataset. On a hub that is really a project workspace, where every
conversation happens inside a group, it will sit empty.

The typical case: a paper cites your hub, a hundred new members arrive in a
fortnight, and the same three questions about running the tool land in your
inbox one at a time. Answers puts them somewhere public where the second
person to ask finds the first person's reply.

Against its siblings:

- Better than the [Forum](17-forum.md) at questions, because one reply gets
  marked accepted and floats to the top; a forum thread ends wherever it
  ends, and the reader has to read it all. Worse than the forum at
  discussion: replies to an answer are comments, three levels deep at most,
  and there is nothing to organise questions by except tags.
- Better than the [Knowledge base](19-kb.md) at questions nobody anticipated,
  because members write it and you do not. Worse at anything you want to
  stay correct: an accepted answer is one member's opinion on one day, and
  nothing ages it out. Hubs commonly run both, and promote a question that
  keeps recurring into a knowledge base article.
- It is the only one of the five that attaches to your catalogue. The
  `resources-questions` and `publications-questions` plugins put a
  **Questions** tab on each tool and publication page, so a question asked
  there is filed against the thing it is about.

**What it is not:** it is not a support queue. A question here is public,
nobody owns it, and nothing chases it if no one replies. Anything with a
member waiting on a person belongs in [Support](34-support.md).

Open it in the administrator interface under **Components > Answers**. Two
sub-menu links sit at the top left: **Questions**, the default list, and
**Answers**, the responses posted to those questions.

## Questions

The Questions screen lists every question with its **ID**, **Subject**,
**State**, **Created** date, **Author**, and **Answers** count. Click a
column heading to sort by it; click again to reverse the order. The author
column links to that member's record in the Members component and notes
below the name when the question was posted anonymously. The answers count
links to the Answers list filtered to that question.

Above the list, type a term in the search box and press **Go** to match it
against question subjects and bodies, or use **Filter by:** to show **Open
Questions**, **Closed Questions**, or **All Questions**. **Clear** resets
the search.

![The Answers Manager questions list, with its sub-menu, search box, state filter, and column headings](../media/answers-newquestion.png)

The toolbar offers:

- **Options** — the component's configuration; see [Options](#options)
  below. Shown only to administrators.
- **New** — open a blank question form.
- **Delete** — permanently remove the checked questions after a
  confirmation, along with their answers, comments, tags, and votes. There
  is no trash to recover from.
- **Help** — the built-in help screen.

Click the state icon in a row to toggle a question between open and closed.
Closing a question is the same act the asker performs on the site by
accepting an answer, except that no answer is marked as chosen and, when
banking is on, no points are distributed.

> **Note:** A question a member deleted on the site is not removed from the
> database; it is set to a trashed state. Such rows still appear in this
> list, as do questions reported as abusive, because the **All Questions**
> filter has no separate entry for them.

## Creating or editing a question

Click a subject, or **New**, to open the question form.

**Details**

| Field | Notes |
|---|---|
| Anonymous | Hides the author's name from other members on the site. Managers still see it here. |
| Notify of responses | Whether the asker is messaged when an answer or comment is posted. Questions asked on the site always have this set. |
| Subject | Required. The one-line question. Maximum 250 characters. |
| Question | Optional. The longer version, with details, written in the editor. |
| Tags | Required. A comma-separated list. Saving without at least one tag fails with an error and returns you to the form. |

**Parameters**

| Field | Notes |
|---|---|
| Creator | The numeric user ID of the member credited with the question. Defaults to your own ID on a new question. |
| Created | The timestamp, `YYYY-MM-DD hh:mm:ss`, picked from the calendar control. |
| State | **Open** or **Closed**. Trashed and reported questions cannot be set from this list. |

The panel beside the form shows the question's ID and, for a saved
question, its creation date and the creator's name.

Press **Save** to save and stay, **Save & Close** to return to the list, or
**Cancel** to discard changes.

> **Note:** Tags entered here are attached in your name, not the author's,
> and the tag field is required even on questions that were imported or
> created before tagging was enforced.

## Answers

The **Answers** sub-menu link lists the responses members have posted. With
no question chosen it shows **Responses to all questions**; reached from a
question's answer count it is filtered to that question, whose ID and
subject head the table and link back to the question.

Columns are **ID**, **Answer** (the first 75 characters, stripped of
markup), **Accepted**, **Created**, **Author**, and **Helpful**, which
shows the up and down vote totals. **Filter by:** offers **All Responses**,
**Accepted Response**, and **Unaccepted Responses**.

The toolbar offers **New** (only when the list is filtered to one
question), **Edit**, **Delete**, and **Help**. Click the accepted icon in a
row to accept or reject that answer. Accepting marks the answer as chosen,
clears the mark from any other answer to the same question, and closes the
question; a question can have only one accepted answer, so checking more
than one row and accepting is refused.

## Creating or editing an answer

**Details**

| Field | Notes |
|---|---|
| Anonymous | Hides the responder's name from other members on the site. |
| Question | The subject of the question being answered. Read-only. |
| Answer | Required. The response text, written in the editor. |

**Publishing**

| Field | Notes |
|---|---|
| Accept | Marks this response as the chosen answer. |
| Creator | The numeric user ID of the member credited with the answer. |
| Created | The timestamp, picked from the calendar control. |

The panel beside the form shows the answer's ID, its creation date and
creator, and its **Helpful** totals. When either total is above zero, a
**Reset Helpful** button appears; it zeroes both counts and deletes the
vote log for that answer, so members may vote on it again.

> **Note:** Accepting an answer from this screen sets the state directly.
> Unlike accepting on the site, it does not run the points distribution,
> so no bank transactions are created.

## Closing out a question that has gone stale

The one job this component regularly asks of a manager: a question from the
wave of new members has three replies, one of them is plainly right, and the
asker never came back to accept it. Left alone it stays open and the right
reply stays buried.

1. Go to **Components > Answers**. Set **Filter by:** to **Open Questions**
   and sort by **Created** to bring the oldest to the top.
2. Click the question's **Answers** count. The Answers list opens filtered
   to that question, with the vote totals in the **Helpful** column.
3. Read the replies and decide which one is the answer. The **Helpful**
   totals are a hint, not a verdict.
4. Click the **Accepted** icon on that row. The answer is marked as chosen,
   the mark is cleared from any other answer to the question, and the
   question closes.
5. Go back to **Questions** to confirm the state icon now reads closed.

Step 4 is reversible: click the icon again to un-accept, and toggle the
question's state icon back to open. Nothing is mailed to anyone and no
points move.

> **Warning:** If your hub has banking switched on, this is *not* the same
> as the asker accepting the answer on the site. Accepting here sets the
> state and nothing else — no points are distributed to the asker or the
> answerer. Where the reward matters, ask the member to accept it
> themselves rather than doing it for them.

## Comments

Members can reply to an answer, and reply to those replies, up to three
levels deep. These comments live in the hub's shared item-comment table and
have no administrator screen of their own. A comment reported as abusive
becomes a support ticket and is hidden behind a notice on the site until
the ticket is resolved; the same is true of a reported question or answer.
See [Support](34-support.md).

## Options

The **Options** button opens four settings, listed with their values in the
[configuration reference](../../reference/configuration/components/answers.md):

| Option | Effect |
|---|---|
| About points | The address of the page explaining the point system. Every "What are points?" and "Learn more" link on the site points here. Defaults to `/kb/points`. |
| Users notified on every activity | A comma-separated list of usernames messaged whenever a question or answer is posted. |
| Restrict accounts to create new posts | Disabled, or Enabled to block new accounts. |
| Minimum number of days to post | With the restriction enabled, how old an account must be before it may post. |

All four ship empty or disabled, and three of the four are sensible left
alone. **About points** matters only when banking is on, and its default of
`/kb/points` assumes a knowledge base article at that address; if you have
no knowledge base, or no article there, every "What are points?" link on the
hub leads to a not-found page. Either write the article or point the option
somewhere real. **Users notified on every activity** is empty by design —
filling it in mails a named person about every question and every answer on
the hub, which is fine on a quiet hub and unbearable on a busy one.

The two restriction settings are the ones to reach for after a spam wave. A
new account cannot post until it is the given number of days old. Turn the
restriction on, set **Minimum number of days to post** to something small
like 3, and the drive-by accounts registered that morning are shut out while
genuine new members are only delayed. Both changes take effect on the next
page load and are undone by setting them back, so they are safe to try on a
live hub.

> **Note:** The account-age restriction is checked when a member opens the
> new-question form and when they submit an answer, and again in the
> resources plugin, but not when a question is finally saved.

The **Permissions** tab sets, per access group, who may administer the
component, manage it (which gates the administrator screens entirely),
create, delete, edit, edit their own, and change the state of questions and
answers. On the site these same actions decide who sees the **New
Question** button, who may save a question, and who may delete one:
a member can delete their own question with `core.delete`, and anyone with
`core.manage` can delete any of them.

## Points and rewards

Everything about rewards only appears when banking is switched on in the
Members component (**Bank accounts**). With it off, no reward field, point
breakdown, or "Rewards" sort is rendered anywhere.

With banking on, each question carries a market value calculated from its
answers, votes, and recommendations, and the asker may put a bonus reward
on top from their own balance. The reward is held, not spent, until the
question closes. When the asker accepts an answer, the accumulated value is
split three ways: a third to the asker, a third to the writer of the
accepted answer, and a third shared among other answers that drew at least
three votes with a majority positive — or added to the accepted answer when
no other answer qualifies. Deleting a question releases the hold and
adjusts the asker's credit back.

## Modules and plugins

Five site modules draw on this component: **mod_answers**,
**mod_recentquestions**, **mod_popularquestions**, **mod_myquestions**, and
**mod_featuredquestion**. Two answers plugins add the site's extra filters:
`answers-tools` supplies "Questions related to my contributions" and
notifies tool authors of questions tagged with their tools, and
`answers-members` supplies "Questions tagged with my interests". Without
them, those filters return nothing. The `resources-questions` and
`publications-questions` plugins put a Questions tab on resource and
publication pages, `tags-answers` includes questions in tag results,
`search-questions` includes them in site search, and `support-answers`
handles abuse reports.

## API

The component exposes `/api/answers/questions` for listing, reading,
creating, updating, and deleting questions; see the
[API reference](../../reference/api/answers.md). The site also publishes an
RSS feed of the newest questions at `/answers/latest.rss`.
