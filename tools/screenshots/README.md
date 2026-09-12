# Looking at a running hub

Node with Playwright, driving the chromium that is already on the machine
rather than downloading another. `npm install` once, in this directory.

## pages.mjs

The catalogue every other tool reads: for each hub, which port it answers on,
who can sign in to it, which pages are worth looking at, and which colours are
its own. Name a hub and the tools find the rest.

Four are catalogued. `mesozoic` is a hub with content in it. `welcome` is what
somebody setting up a new one gets if they elect the sample data. `lucent` is a
hub with nothing in it, which is the state every area of every hub passes
through and the one no template is designed against.

`lucent-on-mesozoic` is not a hub. An entry can name another hub's `host` and
`port` and ask for a template by `style` id, and the tools then put
`templateStyle` on every address: the site renders that request in that
template and the hub's own default does not change. It is there because lucent
is delivered on the bare hub and a template shows almost nothing of itself
against nothing - no listing rows, no tabs, no tables, no pagination, no
comments. The first run of it found forty-four pieces of text under 4.5:1.

Two things in here are load-bearing. The port belongs to the hub, because
naming one hub and getting another's port once returned a whole clean run
against twenty-four blank pages. And the palette belongs to the hub, because
judging one template's neutrals against another's reported forty-seven colours
that had all been chosen on purpose.

## overflow.mjs

Asks every page on a list whether anything a reader can see is cut off by
something that hides what runs past it — the commonest thing a template sweep
is looking for, and the one thing a screenshot cannot show you, because the
part that does not fit is simply not in the picture.

    node tools/screenshots/overflow.mjs [hub] [port]

It takes its pages from the catalogue, reports at two widths, and says a
finding once however many pages carry it. Four things are not findings:
anything under 24px, because an icon glyph sitting proud of its span is not a
layout problem; anything the reader cannot see, because a nav standing aside
for a hamburger is still laid out at its full width; anything inside a box that
scrolls, because the reader can reach it; and text that is put out of sight
rather than text that did not fit.

That last one has to be asked of the words and not of the box. A ranking bar
sits at the left edge of the thing that clips it and is then indented 55em, so
its box starts inside and only its words are outside - which read as 817px of
lost content at every width until the question was put properly.

## inspect.mjs

Says why, once overflow.mjs has said what.

    node tools/screenshots/inspect.mjs chain  <url> '<selector>' [width]
    node tools/screenshots/inspect.mjs style  <url> '<selector>' [width]
    node tools/screenshots/inspect.mjs widest <url> [width]

`chain` walks from an element to the page, with each ancestor's width and what
it does about overflow, which is usually enough to see where a width is coming
from. `style` computes the properties that decide a width. `widest` lists
everything on the page wider than the viewport.

## shoot.mjs

Photographs a hub, page by page, as each of the people who use it, from the
catalogue in `pages.mjs`.

    node tools/screenshots/shoot.mjs [hub] [port] [--only=name,name]

Output is `docs/screenshots/<hub>/<desktop|phone>/<name>.png` plus a
`manifest.json` recording what was taken, as whom, and what it answered. Those
are working output rather than source, and this repository does not track them.

Everything that would otherwise drift between runs is pinned: the clock is
fixed at 2026-09-01T12:00:00Z, the timezone is UTC, animations and transitions
are zeroed, the caret is made transparent, the debug bar is hidden, and every
page is given until `document.fonts.ready` before it is captured. Text
measured before its font arrives is text of the wrong width, and this
repository has already had one check report five findings on one run and one on
the next for exactly that reason.

What is not pinned is the hub's own data. Run `tools/hubs/check-idempotent.sh`
if a diff looks like content rather than design.

## review.mjs

Walks the same catalogue looking for the two things a picture shows that can
also be measured.

    node tools/screenshots/review.mjs [hub] [port] [--desktop|--phone]

Grey that is not in the palette, and elements sitting on top of each other. It
reaches more of the hub than `surfaces.mjs` or `overflow.mjs` do on their own
lists, because it goes everywhere the catalogue goes and signs in where the
catalogue says to.

## Looking at the pictures

Neither tool can tell you whether a picture is any good, and that is the part
that matters: a run of fifty pages is the cheapest way there is to see a
template against real content, but only if somebody opens the files.

**Grey running through.** The hub is assembled from a dozen component
stylesheets that each reach for a neutral grey, and against a warm page they
read as cold patches laid on top of it rather than as part of it. `review.mjs`
finds these faster than an eye will, but it cannot see a grey that arrives as
an image, and it has no opinion about a colour that is in the palette and
wrong anyway.

**Overlap and collision.** Text over text, a button over a heading, a badge
sitting on a word. `review.mjs` finds boxes that cross; some of those are by
design - a count badge is meant to sit on its menu item - so the list wants
reading rather than obeying.

**Designs that are simply poor.** A heading with nothing under it, a column
empty at every width, a control the same colour as the page, three panels in a
row that each say the same thing, a page whose first screen is entirely
furniture. None of this is measurable and all of it is obvious in a picture.

**The phone, separately.** Everything above, plus anything that was a row and
is now a stack in the wrong order, a table that has become a wall, or a landing
page that is four screens of nothing before the content.

## When one of them is wrong

Fix it in the template, not in the component. A component's stylesheet is
shared by every hub and every template, and what is wrong in one of these
pictures is usually only wrong against this design. The template's own
`html/<extension>/<file>.css` replaces the component's, so it imports the
original and adds to it - `core/templates/mesozoic/html/` has a dozen examples.

The exception is a component that is broken rather than merely different, and
the difference is worth being honest about: a rule that contradicts the markup
it is written for is broken everywhere and belongs in the component.

## bare.mjs

Asks whether anything is labelled that is not there.

    node tools/screenshots/bare.mjs [hub] [port]

A heading is a promise that something follows it. Several components kept that
promise by guarding on the list they were handed and then rejecting every row
inside the loop, which is the same as not guarding at all - so "Categories" sat
over a blank half-screen. It only happens on a hub with nothing in it, which is
why it survived for as long as nobody photographed one.

Three things are deliberately not findings: a heading pair, where a name in an
h2 is followed by a section name in an h3; content this document cannot read,
which is an iframe or an image; and a row of controls, which is how a calendar
puts icon-only buttons beside the month. Without those it reported six on a
populated hub and every one was wrong.

## contrast.mjs, veneer.mjs, surfaces.mjs, edges.mjs

`contrast.mjs` walks the rendered page for text that falls under WCAG 1.4.3 and
names the rule and stylesheet behind each finding.

Where the ground is painted with an image the stylesheet cannot answer, so it
asks the page: it hides the ink, photographs what is behind it, and takes the
worst pixel under each run - the worst and not the average, because a frieze is
mostly its ground with a few dark shapes in it, and a reader whose word falls
across a dinosaur cannot read that word. The screenshot is decoded by the
browser that took it, so this needs no image library.

It also refuses to call a blank page a pass, and skips text that is painted
nowhere: a label pushed off its own box with text-indent so an icon can stand
in for it is not text anybody has to read.

`veneer.mjs` asks the same question of a file rather than a page - useful
before a band ships, when there is nothing to photograph yet.

    node tools/screenshots/veneer.mjs [--opacity=N] <image> <ground> <ink:px>

`surfaces.mjs` reports every element painting a neutral surface, and what
radius it has. `edges.mjs` says where things line up, at several widths.
