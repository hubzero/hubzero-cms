/**
 * Walk the catalogue looking for the two things a picture shows that can also
 * be measured: grey that is not in the palette, and things sitting on top of
 * each other.
 *
 * The third thing - a design that is simply poor - is not in here, because it
 * cannot be. That is what looking at the pictures is for, and
 * tools/screenshots/README.md says what to look for.
 *
 * This covers every page in tools/screenshots/pages.mjs, as the person the
 * catalogue says, which is more of the hub than surfaces.mjs or overflow.mjs
 * reach on their own lists.
 *
 *   node tools/screenshots/review.mjs [hub] [port] [--desktop|--phone]
 */
import { chromium } from 'playwright';
import { readdirSync, existsSync } from 'node:fs';
import { hubFor } from './pages.mjs';

/** The newest chromium already on this machine. */
function chromiumPath() {
    if (process.env.HUB_CHROMIUM) {
        return process.env.HUB_CHROMIUM;
    }

    const root = process.env.PLAYWRIGHT_BROWSERS_PATH
        || `${process.env.HOME}/.cache/ms-playwright`;

    if (!existsSync(root)) {
        return undefined;
    }

    for (const build of readdirSync(root)
        .filter(d => d.startsWith('chromium-'))
        .sort((a, b) => Number(b.split('-')[1]) - Number(a.split('-')[1]))) {
        for (const rel of ['chrome-linux64/chrome', 'chrome-linux/chrome']) {
            if (existsSync(`${root}/${build}/${rel}`)) {
                return `${root}/${build}/${rel}`;
            }
        }
    }

    return undefined;
}

/**
 * Wait for the page to stop changing width under us, but not for ever
 *
 * document.fonts.ready is a promise, and evaluate() awaits a returned promise
 * with no timeout of its own - so a font request that never settles hangs the
 * whole run silently. One did: a shoot took every picture it was asked for and
 * then sat for an hour without writing its manifest.
 *
 * @param   object  page  The page to wait on
 * @return  void
 */
async function settled(page) {
    await page.evaluate(() => Promise.race([
        document.fonts ? document.fonts.ready : Promise.resolve(),
        new Promise(resolve => setTimeout(resolve, 3000)),
    ])).catch(() => {});
}

const args = process.argv.slice(2).filter(a => !a.startsWith('--'));
const hub  = args[0] || 'mesozoic';

// The port is the hub's own, from the catalogue: naming a hub and getting
// another hub's port back is how a whole run of this once came back clean
// against twenty-four blank pages. An argument still overrides it.
const port = args[1] || (hubFor(hub) || {}).port || '7600';
const base = `https://${hub}.${process.env.HUB_DOMAIN || 'example.com'}:${port}`;

const only = process.argv.includes('--phone') ? 'phone'
    : (process.argv.includes('--desktop') ? 'desktop' : null);


// The hub's own catalogue and its own people. A page listed as `as: manager`
// needs a hub that has a manager, and not every hub does.
const catalogue = hubFor(hub);

if (!catalogue) {
    console.error(`there is no catalogue for "${hub}" in pages.mjs`);
    process.exit(2);
}

const people = catalogue.people;
const pages = catalogue.pages;

const viewports = {
    desktop: { width: 1440, height: 900 },
    phone:   { width: 390, height: 844 },
};

// The palette is the hub's own, from the catalogue: judging meridian's cool
// neutrals against mesozoic's warm ones reported forty-seven findings, every
// one of them a colour the template had chosen on purpose.
const PALETTE = catalogue.palette || [];

const SPLIT = ' :: ';

const browser = await chromium.launch({ executablePath: chromiumPath() });

/**
 * Sign in, or not
 *
 * @param   string  who  A key of people, or 'guest'
 * @return  object  A context
 */
async function contextFor(who) {
    const context = await browser.newContext({ ignoreHTTPSErrors: true });

    if (who === 'guest') {
        return context;
    }

    const person = people[who];
    const page = await context.newPage();
    const door = (who === 'admin') ? `${base}/administrator/` : `${base}/login`;

    await page.goto(door, { waitUntil: 'load' });
    await page.fill('input[name="username"]', person.username);
    await page.fill('input[name="passwd"]', person.password);
    await page.click('input.login-submit, input[type="submit"], button[type="submit"]');
    await page.waitForSelector('input[name="passwd"]', { state: 'detached', timeout: 20000 })
        .catch(() => {});
    await page.close();

    return context;
}

const LOOK = (palette) => {
    const out = { grey: [], overlap: [] };

    // Grey that is not in the palette
    for (const el of document.querySelectorAll('body *')) {
        const style = getComputedStyle(el);
        const box   = el.getBoundingClientRect();

        if (box.width < 24 || box.height < 12 || style.visibility === 'hidden') {
            continue;
        }

        const m = style.backgroundColor.match(/[\d.]+/g);

        if (!m || (m[3] !== undefined && Number(m[3]) < 0.05)) {
            continue;
        }

        const key = 'rgb(' + m[0] + ', ' + m[1] + ', ' + m[2] + ')';
        const [r, g, b] = [Number(m[0]), Number(m[1]), Number(m[2])];

        if (palette.includes(key) || (Math.max(r, g, b) - Math.min(r, g, b)) > 14) {
            continue;
        }

        const name = el.tagName.toLowerCase() + (el.id ? '#' + el.id : '')
            + (el.className && typeof el.className === 'string'
                ? '.' + el.className.trim().split(/\s+/).slice(0, 2).join('.') : '');

        out.grey.push(key + '  ' + name);
    }

    // Two pieces of text sharing a space
    //
    // Only leaves - an element whose text is its own - and only where the
    // boxes genuinely cross rather than merely touch, because a list of rows
    // that meet at the edge is a list and not a collision.
    //
    // The boxes the text is painted in, not the boxes of the elements.
    //
    // An element's box says almost nothing about where its words are. A
    // heading that fills a row has a box the width of the row and its text at
    // one end of it; an inline link that wraps has a box which is the union of
    // its lines and covers everything on both. Between them those two
    // accounted for nearly every collision the first version reported - 134 on
    // a phone, 46 on a desktop, almost none of them anything a reader would
    // see.
    //
    // A range over an element's own text nodes gives the line boxes actually
    // drawn, which is the only geometry worth comparing - once each of them
    // has been cut back to whatever clips it. A word inside a 26px box with
    // overflow hidden has a line box the width of the word, and only the part
    // inside the box is on the screen. overflow.mjs learned the same thing:
    // the question is always what a reader can see, never what was laid out.
    const leaves = [];

    for (const el of document.querySelectorAll('body *')) {
        const style = getComputedStyle(el);
        const box = el.getBoundingClientRect();

        if (box.width < 8 || box.height < 8 || style.visibility === 'hidden'
            || style.opacity === '0' || style.position === 'fixed') {
            continue;
        }

        const own = [...el.childNodes]
            .filter(n => n.nodeType === 3 && n.textContent.trim());

        if (!own.length) {
            continue;
        }

        // Whatever cuts this element's text off, if anything does
        const clips = [];

        for (let up = el; up && up !== document.body; up = up.parentElement) {
            const s = getComputedStyle(up);

            if (s.overflowX !== 'visible' || s.overflowY !== 'visible') {
                clips.push(up.getBoundingClientRect());
            }
        }

        const rects = [];

        for (const node of own) {
            const range = document.createRange();

            range.selectNodeContents(node);

            for (const rect of range.getClientRects()) {
                let left = rect.left;
                let right = rect.right;
                let top = rect.top;
                let bottom = rect.bottom;

                for (const clip of clips) {
                    left = Math.max(left, clip.left);
                    right = Math.min(right, clip.right);
                    top = Math.max(top, clip.top);
                    bottom = Math.min(bottom, clip.bottom);
                }

                if ((right - left) > 4 && (bottom - top) > 4) {
                    rects.push({
                        left: left, right: right, top: top, bottom: bottom,
                        width: right - left, height: bottom - top,
                    });
                }
            }
        }

        if (!rects.length) {
            continue;
        }

        leaves.push({ el: el, rects: rects, name: el.tagName.toLowerCase()
            + (el.className && typeof el.className === 'string'
                ? '.' + el.className.trim().split(/\s+/).slice(0, 2).join('.') : '') });
    }

    for (let i = 0; i < leaves.length; i++) {
        for (let j = i + 1; j < leaves.length; j++) {
            const a = leaves[i];
            const b = leaves[j];

            if (a.el.contains(b.el) || b.el.contains(a.el)) {
                continue;
            }

            let worst = null;

            for (const ra of a.rects) {
                for (const rb of b.rects) {
                    const across = Math.min(ra.right, rb.right) - Math.max(ra.left, rb.left);
                    const down   = Math.min(ra.bottom, rb.bottom) - Math.max(ra.top, rb.top);

                    // More than a hairline in both directions, and enough of
                    // the smaller line box to be seen
                    if (across < 4 || down < 4) {
                        continue;
                    }

                    const smaller = Math.min(ra.width * ra.height, rb.width * rb.height);

                    if (!smaller || ((across * down) / smaller) < 0.25) {
                        continue;
                    }

                    if (!worst || (across * down) > worst.area) {
                        worst = { across: across, down: down, area: across * down };
                    }
                }
            }

            if (!worst) {
                continue;
            }

            out.overlap.push(a.name + ' over ' + b.name + '  '
                + Math.round(worst.across) + 'x' + Math.round(worst.down));
        }
    }

    return out;
};

const contexts = {};
const findings = new Map();
let looked = 0;

for (const [label, size] of Object.entries(viewports)) {
    if (only && only !== label) {
        continue;
    }

    for (const entry of pages) {
        if (entry.at && entry.at !== label) {
            continue;
        }

        const who = entry.as || 'guest';

        if (!contexts[who]) {
            contexts[who] = await contextFor(who);
        }

        const page = await contexts[who].newPage();

        await page.setViewportSize(size);

        let response;

        try {
            response = await page.goto(base + entry.url, { waitUntil: 'load', timeout: 45000 });
        } catch (e) {
            await page.close();
            continue;
        }

        if (!response || response.status() >= 400) {
            await page.close();
            continue;
        }

        await settled(page);

        const found = await page.evaluate(LOOK, PALETTE);

        looked++;

        for (const kind of ['grey', 'overlap']) {
            for (const what of new Set(found[kind])) {
                const key = kind + SPLIT + what;
                const where = findings.get(key) || [];

                where.push(label + '/' + entry.name);
                findings.set(key, where);
            }
        }

        await page.close();
    }
}

for (const context of Object.values(contexts)) {
    await context.close();
}

await browser.close();

console.log('Looked at ' + looked + " pictures' worth of " + hub + '.\n');

for (const kind of ['grey', 'overlap']) {
    const mine = [...findings.entries()]
        .filter(([k]) => k.indexOf(kind + SPLIT) === 0)
        .sort((a, b) => b[1].length - a[1].length);

    console.log(kind === 'grey'
        ? 'Grey that is not in the palette: ' + mine.length
        : '\nThings sitting on top of each other: ' + mine.length);

    for (const [key, where] of mine.slice(0, 25)) {
        const what = key.slice(kind.length + SPLIT.length);
        const scope = where.length > 3 ? where.length + ' pages' : where.join(', ');

        console.log('  ' + what + '\n      ' + scope);
    }
}

console.log('\nWhat is left is what a picture shows and a measurement cannot:'
    + '\nread tools/screenshots/README.md, then open the files.');
