# Looking at a running hub

Node with Playwright, driving the chromium that is already on the machine
rather than downloading another. `npm install` once, in this directory.

## overflow.mjs

Asks every page on a list whether anything a reader can see is cut off by
something that hides what runs past it — the commonest thing a template sweep
is looking for, and the one thing a screenshot cannot show you, because the
part that does not fit is simply not in the picture.

    node tools/screenshots/overflow.mjs [hub] [port]

It reports at two widths, says a finding once however many pages carry it, and
ignores three things on purpose: anything under 24px, because an icon glyph
sitting proud of its span is not a layout problem; anything the reader cannot
see, because a nav standing aside for a hamburger is still laid out at its full
width; and anything inside a box that scrolls, because the reader can reach it.

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

## contrast.mjs, veneer.mjs, surfaces.mjs, edges.mjs

`contrast.mjs` walks the rendered page for text that falls under WCAG 1.4.3 and
names the rule and stylesheet behind each finding. It declines anything with an
image behind it and says how much it declined.

`veneer.mjs` answers those: it composites an image over the ground it sits on,
finds the darkest pixel, and reports what text over it is left with.

    node tools/screenshots/veneer.mjs [--opacity=N] <image> <ground> <ink:px>

`surfaces.mjs` reports every element painting a neutral surface, and what
radius it has. `edges.mjs` says where things line up, at several widths.
