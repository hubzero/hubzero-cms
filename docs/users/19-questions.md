<!--
status: rewritten
reviewed-against: 2.4-main @ 42a7a5b5c7
reviewed: 2026-09-10
source: https://help.hubzero.org/documentation/240/users/questions
-->
# Questions and answers

Questions and answers is for a question that has an answer. A student
runs a device simulation on the hub, the log ends in "convergence
failed", and somebody on the hub has hit that before and knows what the
fix is. She asks; two people answer; she marks the one that worked. From
then on the next person who searches for the same message finds the
answer already written down, with the working one at the top.

That last part is the point of the tool, and it is why the shape is
different from a message board: a question is asked once, answered by
whoever knows, and then **closed** on the answer that helped. It lives
at `/answers` on the hub, and most hubs link it from their support or
help menu.

Anyone can read questions and answers. Asking, answering, commenting, and
voting all require you to be logged in; the links send you to the login
page and bring you back where you were.

## Which one do I want?

| If you want to | Use |
|---|---|
| Get one answer to a specific question, and mark it as the answer | Questions and answers — this page |
| Discuss something open-ended, where several answers are defensible | [Forum](09-forum.md) |
| Report something broken, or ask the hub's staff for help | [Support](12-support.md) |
| Ask for a feature, tool, or change to be built | [Wish list](29-wishlist.md) |

Ask here when you expect somebody to know. Ask in the forum when you
expect people to disagree. File a support ticket when the hub itself is
at fault — nobody has to answer a question, and no clock runs on one,
so a question is a bad place to report an outage. Check the
[knowledge base](13-knowledgebase.md) first; it holds the answers the
hub's staff have already written up.

## Finding a question

The main page lists questions newest first, with a search box at the top.
Type a keyword or phrase and press **Search** to match question titles and
text.

Two rows of links sit above the list:

- **Sort by** — **Recent** (newest first), **Popular** (most liked first),
  and, on hubs that award points, **Rewards** (largest reward first).
  Clicking the sort you are already using reverses its direction.
- **Filter by** — **All**, **Open**, or **Closed**. An open question is
  still accepting answers; a closed one has had its best answer chosen.

When you are logged in, a third row narrows the list to **Everything**,
**Questions I asked**, **Related to my contributions** (questions tagged
with tools you author), or **Tagged with my interests** (questions matching
the tags on your profile). The last two only have any effect on hubs that
have the matching plugins enabled.

Each row shows the question, who asked it and when, whether it is open or
closed, how many answers it has, and its vote buttons. A question that has
been reported as abusive is listed as **Question under review** and cannot
be opened until the hub's staff have looked at it.

The sidebar links to the knowledge base and to the help page, and, where
points are in use, to the page explaining how they are earned. The newest
questions are also published as an RSS feed at `/answers/latest.rss`.

## Asking a question

Take the convergence failure above the whole way. The student is logged
in, she is on `/answers`, and she presses **New Question** at the top of
the list.

1. Press **New Question**. You can also reach the form directly at
   `/answers/question/new`.
2. Tick **Post a question anonymously** if you do not want your name shown
   with the question.
3. Enter **Tags**. This field is required — a question without at least one
   tag is not saved. Tags are how other members and the "related to my
   contributions" filters find your question, so tag it with the tool,
   resource, or topic it is about.
4. Write the **Short question (one-liner)**. This is required and is what
   appears in the list.
5. Add **Long question (details)** if the one-liner needs explaining.
6. Where the hub awards points, **Assign a point reward for answering your
   question** takes a number up to the balance shown beside it. The field
   is disabled when you have no points to spend.
7. Press **Save**.

The question appears immediately and you are taken to it. If the hub
notifies particular staff of new questions, or if your tags name a tool
whose authors should hear about it, those people are messaged.

So the student's question reads "Transport solver stops with
convergence failed at bias > 0.4 V", tagged with the tool's name, with
the input file and the tail of the log pasted into the long description.
Both halves of that matter: the tag is what reaches the tool's authors,
and the log is what lets somebody answer without a round of questions
first.

> **Note:** Some hubs stop brand-new accounts from posting. If yours does,
> the form refuses to open until your account is old enough and tells you
> so.

You can also ask a question from a resource or publication page, on hubs
that show a **Questions** tab there. The question is tagged with that item
automatically, and the item's page then lists it.

## Answering a question

1. Open the question and press **Answer this question**, or use the
   **answer this question** link where a question has no answers yet.
2. Write your response in **Your Response**.
3. Tick **Post response anonymously** to keep your name off it.
4. Press **Save**.

The asker is messaged that an answer has arrived, unless they turned
notifications off. Your answer joins the list under the question, where
other members can vote on it and reply to it.

Write the answer for the person who finds it in six months, not only for
the person who asked. Two mechanics push the same way. Anyone
reading can vote on your answer, not just the person who asked, and
those votes decide the **Popular** sort; and on a hub that runs points,
an answer needs at least three votes with
a majority positive before it shares in the payout, which a one-line
"same here" never gets. If you only want to ask the asker something, use
**Reply** under the answer rather than posting a second answer — a
clarifying request posted as an answer sits in the list looking like a
solution.

## Voting

Every question and every answer has **Like** and **Dislike** buttons
showing how many people have voted each way. Press one to cast your vote;
press the other to change it. You cannot vote on your own question or your
own answer — the buttons are shown but do nothing. Votes decide the
**Popular** sort and, on hubs that award points, feed into how much a
question is worth.

## Commenting on an answer

Under each answer is a **Reply** link. It opens a small form: write your
comment, tick **Post comment anonymously** if you want to, and press
**Save**. You can reply to a reply, up to three levels deep. The asker and
the person you are replying to are both messaged.

Every question, answer, and comment has a **Permalink** you can copy to
link straight to it.

## Choosing the best answer

Only the person who asked a question can close it, and only while it is
open. Under each answer you will see **Accept answer**; press it on the one
that helped most. The answer moves to a **Chosen Answer** block at the top,
the question is marked closed, and no further answers are taken. Where
points are in use, this is the moment they are paid out, so a question left
open indefinitely pays nobody.

Come back and do it. The student who accepts the answer that fixed her
convergence failure turns a thread into a result: the fix sits at the
top of the page, the people who helped are paid, and the reward she put
up leaves hold. Nobody else can do this for her, and there is no
deadline that does it automatically.

## Deleting your question

Open your question and press **Delete**, then confirm on the page that
follows. The question disappears from the site. Any reward you were holding
is released back to your account and no points are awarded for the
question. You can only do this to your own questions, and hub managers can
delete any question.

## Reporting abuse

**Report abuse** appears on questions, answers, and comments. It files a
report with the hub's support staff and hides the content behind a notice
until they have reviewed it. See [Support](12-support.md).

## Earning points

Not every hub runs points. Where yours does not, there is no reward
field on the ask form, no **Rewards** sort, and nothing in this section
applies; the rest of the page still does.

Where a hub does run a points economy, each question has a market value
that grows with the answers, votes, and recommendations it collects.
When the asker accepts an answer, that value is split three ways: a
third to the asker, a third to the writer of the accepted answer, and a
third divided among the other answers — each qualifying answer takes
that third divided by the total number of answers, and an answer
qualifies only if it drew at least three votes and at least as many
likes as dislikes. If no other answer qualifies, the accepted answer
takes that share too. A reward the asker set is paid on top, out of
their own balance.

A reward is held, not spent, from the moment you post the question. It
comes off your available balance straight away, sits against the
question, and is released only when you accept an answer or delete the
question. Offering one is a way of saying the question is worth
somebody's afternoon; leaving the question open forever is a way of
tying up your own points and paying nobody.

Members also receive monthly royalty payments on the questions and answers
they have posted, based on the votes those have since collected. The
sidebar's **Learn more** link goes to your hub's own page on how points
work.
