<!--
status: rewritten
reviewed-against: 2.4-main @ 009ec973b7
reviewed: 2026-09-10
screenshots: none
-->
# Feedback

Feedback collects success stories: short testimonials that members write about
what the hub did for their work, which the hub then quotes on its front page
and on a "Notable Quotes" page. It is not a survey tool and it does not gather
structured responses. One member writes one story about themselves, an editor
reads it, and the editor decides whether it is good enough to quote.

The component also owns `/feedback`, a signpost page that points visitors at
the several different ways of telling the hub something — a success story, a
support ticket, a wish list entry, a poll — without owning any of them but the
first.

## Whether your hub needs it

Almost every hub is funded by somebody who wants to know it was worth it.
Feedback exists for that: a stock of members' own words about what the hub
did for their work, which you can put on the front page, quote in a renewal,
or hand to a communications office. If your hub does not have to justify
itself to anyone, you can leave this component alone — it does nothing until
someone submits a story.

The typical case: an annual report is due in six weeks and you need three
sentences from three different members saying the hub mattered. Mail the
`/feedback/success_story` link to a handful of people whose work you know,
and the stories arrive over the following fortnight.

There is one thing to understand before you use it at all: **the consent
checkbox is recorded but never enforced.** The author's answer to *"I
authorize … to use my quote"* is stored on the row and shown in the list, and
nothing in the site or the modules ever looks at it. Publishing is entirely
in your hands, which means the check is yours to make. See
[What publishing means](#what-publishing-means).

**What it is not:** it is not a survey and it does not collect structured
answers — there is one free-text box and no questions. It is also not the
place members complain: the **Report a Problem** card on `/feedback` goes to
[Support](34-support.md), and this component never sees it. And nothing here
notifies anyone. A story sits in the list until a person opens the screen and
reads it, so put a note in your calendar rather than expecting the hub to
tell you.

## Collecting a story

A logged-in member reaches the form at `/feedback/success_story`, from the
**Write a Success Story** button on `/feedback`. Guests are sent to the login
screen first. The form asks for:

| Field | Notes |
|---|---|
| Name | Prefilled from the member's profile. Required. |
| School / Organization | Prefilled from the member's profile. Required. |
| Pictures | Drag-and-drop upload, any number. Held in a temporary directory until the story is saved. Extensions and size limit come from the Media component's settings, and each file is virus-scanned. |
| Describe your experience | The story itself. Required. Scripts, images and all other tags are stripped on save; line breaks become `<br />`. |
| *I authorize … to use my quote on the … website.* | Consent to publish. A checkbox. |
| *I authorize … to contact me for further information.* | Consent to be contacted. A checkbox. |

Saving stores a row in `#__feedback` and moves the pictures into
`<upload path>/<id>/`. The member gets a thank-you page. Nothing is published
and nobody is notified; the story simply waits in the administrator's list.

The two consent checkboxes are the reason this component exists, and they are
the only record of what the author agreed to. They are shown but disabled on
the administrator's form once a story has been saved, so an editor cannot
change them afterwards.

## The Quotes list

Open the component under **Components > Feedback**. The list shows every story
that has been submitted, newest first.

| Column | Notes |
|---|---|
| ID | The record's number. Sortable. |
| Submitted | The date the story was sent. Sortable. |
| Author | The author's name; a link to the edit form. Sortable. |
| Organization | The author's affiliation. Sortable. |
| Quote | The first hundred characters of the full quote, falling back to the short quote, then to the mini quote, then to *(blank)*. A link to the edit form. |
| Notable Quotes | **Yes** when the story is selected for the Notable Quotes page. |
| OK to Publish | **Yes** when the author consented to publication. |

Above the list, type a name into the search box and press **Go**; **Clear**
restores the full list. The search matches the author's name only, not the
text of the quote.

The toolbar offers **Options**, **New**, **Edit**, **Delete** and **Help**,
each shown only if you hold the matching permission. **Delete** removes the
record and its whole picture directory, without a confirmation step and
without a trash state.

## Editing a story

The form is in two columns. The left column is the story:

| Field | Notes |
|---|---|
| Select for Notable Quotes page. | The publish switch. See [What publishing means](#what-publishing-means). |
| Author full name | Required. |
| Organization | Required. |
| User id | The `uidNumber` of the member who sent the story. Read-only once set. Leave it blank for a quote from someone with no account. |
| Author consents | Two disabled checkboxes recording what the author agreed to on the front-end form: consent to publish, and consent to be contacted. Editable only while creating a record by hand. |
| Short Quote | An editor's trim of the story, for places with limited room. 270 characters. If you leave it blank, the first 270 characters of the full quote are used. |
| Mini Quote | A 150-character version, for the front-page quote module. Defaults to the first 150 characters of the short quote. |
| Full Quote | The story as submitted. Required. |
| Quote submitted | The date, editable as plain text. |
| Editor notes | Private working notes: where the quote came from, how to reach the author. Never shown on the site. |

The right column uploads and removes pictures. Each existing picture has a
**Delete** button, which marks it for removal on the next save; the file input
below adds more. A new record must be saved before pictures can be attached.

Creating a record from the toolbar's **New** button first asks for a
**Username**, so that a quote typed in by an editor can be attached to the
right member; leave it blank for someone with no account.

## What publishing means

Only one thing decides whether a story appears anywhere on the site: the
**Select for Notable Quotes page.** checkbox. With it ticked, the story is
shown by:

- `/feedback/quotes`, the list of notable quotes.
- The **Quotes** module (`mod_quotes`), which lists notable quotes and can
  cycle through them.
- The **Random Quote** module (`mod_randomquote`), which shows one at a time
  and is the usual front-page fixture.

> **Warning:** The consent checkbox is not enforced. `OK to Publish` is
> recorded and displayed, but nothing in the front end or the modules checks
> it. Ticking **Select for Notable Quotes page.** on a story whose author did
> not consent will publish it. Read the **OK to Publish** column before you
> tick anything.

Which makes selecting a quote a two-step job rather than one. Following the
annual-report example:

1. Go to **Components > Feedback**. The stories are listed newest first.
2. Read the **OK to Publish** column *first*. A story marked anything but
   **Yes** is not publishable, whatever it says — the author read the same
   sentence you did and declined. Ignore it or write to the author and ask.
3. Open a story whose column reads **Yes** and read the **Full Quote**.
4. Write a **Short Quote** and a **Mini Quote** by hand. Left blank they are
   cut from the full quote at 270 and 150 characters, which usually stops
   mid-sentence. This is an edit of somebody's words for publication, so make
   it one they would recognise.
5. Tick **Select for Notable Quotes page.** and save.
6. Check `/feedback/quotes` and, if you run the modules, the front page.

Step 5 is reversible — untick it and the quote comes off every page it was
on. Step 2 is the one that is not, in the sense that matters: a quote
published without consent has been published, and taking it down afterwards
does not undo that. **Delete** is worse still, since it removes the record
and its whole picture directory with no confirmation and no trash state, so
the consent record goes with it.

> **Note:** The author's two consent checkboxes are shown but disabled on the
> administrator's form once a story has been saved, so nobody can quietly
> flip a No to a Yes. That is deliberate and it is the only protection the
> flag has.

The **Random Quote** module's **Quote pool** parameter offers *Flash
rotation*, *Notable quotes* and *All*, but only *Notable quotes* selects
anything sensible: the other two ask for stories that are **not** marked
notable. Its second filter, on the mini quote, compares a text column against
1 or 0 and matches almost nothing. Leave that module on *Notable quotes* and
expect it to show the same quote each time rather than a random one.

## The /feedback page

`/feedback` is a landing page with up to four cards:

- **Write a Success Story** — the form above.
- **Report a Problem** — a new support ticket. See [Support](34-support.md).
- **Add to the Wish List** — shown only if the Wishlist component is enabled.
  See [Wishlist](40-wishlist.md).
- **Take a Poll** — shown only if the Poll component is enabled; it opens
  `/feedback/poll`, which renders the **Poll** module. See [Poll](25-poll.md).

Two paths under `/feedback` are redirects rather than pages of their own:
`/feedback/report_problems` opens the new-ticket form, and
`/feedback/suggestions` goes to the wish list.

The page's introduction text, and the sentence pointing at *Notable Quotes*,
come from the language file `en-GB.com_feedback.ini`. That sentence links to
`/about/quotes/` as a fixed path, so it only works on a hub that has a menu
item there; the component's own quotes page is at `/feedback/quotes`.

There are five site layouts, so a menu item of type **Feedback** can be
pointed at the landing page, the success story form, the list of quotes, the
poll page or the thank-you page.

## Options

The component has two settings, both about pictures: **Default picture**, the
placeholder shown for an author with no photograph, and **Upload path**, the
directory under the hub's application root where the picture folders are
created — `/site/quotes` by default. Both are listed in the
[configuration reference](../../reference/configuration/components/feedback.md).

The **Permissions** tab sets, per user group, who may configure the component,
reach its screens, and create, delete, edit or change the state of a quote.
Reaching the screens at all requires Access Administration Interface.
