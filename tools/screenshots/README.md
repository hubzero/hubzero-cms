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
