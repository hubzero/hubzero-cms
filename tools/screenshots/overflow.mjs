/**
 * Ask a hub's pages whether they fit the screen they are given.
 *
 * A page that scrolls sideways on a phone is the commonest thing a template
 * sweep is looking for, and it is invisible in a screenshot: the part that
 * does not fit is simply not in the picture. This measures instead, and names
 * the element that is too wide, which is the part that takes the time.
 *
 *   node tools/screenshots/overflow.mjs [hub] [port]
 */
import { chromium } from 'playwright';
import { readdirSync, existsSync } from 'node:fs';

/**
 * The newest chromium already on this machine.
 *
 * Playwright wants the exact build it shipped with and offers to download it.
 * There is one here already, and a template sweep does not need a particular
 * build - it needs a browser. Set HUB_CHROMIUM to override.
 */
function chromiumPath() {
    if (process.env.HUB_CHROMIUM) {
        return process.env.HUB_CHROMIUM;
    }

    const root = process.env.PLAYWRIGHT_BROWSERS_PATH
        || `${process.env.HOME}/.cache/ms-playwright`;

    if (!existsSync(root)) {
        return undefined;
    }

    const builds = readdirSync(root)
        .filter(d => d.startsWith('chromium-'))
        .sort((a, b) => Number(b.split('-')[1]) - Number(a.split('-')[1]));

    for (const build of builds) {
        for (const rel of ['chrome-linux64/chrome', 'chrome-linux/chrome']) {
            const at = `${root}/${build}/${rel}`;

            if (existsSync(at)) {
                return at;
            }
        }
    }

    return undefined;
}

/**
 * Go somewhere, allowing for the network changing under the request
 *
 * A transient ERR_NETWORK_CHANGED or ERR_CERT_VERIFIER_CHANGED cancels the
 * navigation, and every goto issued while one is unwinding is reported as
 * "interrupted by another navigation" - so one blip takes the rest of the run
 * with it and the report reads as a broken hub. The second attempt is always
 * fine.
 *
 * @param   object  page  Where to do it
 * @param   string  url   Where to go
 * @return  object  The response
 */
async function visit(page, url) {
    for (let attempt = 0; attempt < 3; attempt++) {
        try {
            return await page.goto(url, { waitUntil: 'load', timeout: 30000 });
        } catch (e) {
            const transient = /ERR_NETWORK_CHANGED|ERR_CERT_VERIFIER_CHANGED|ERR_ABORTED|interrupted by another navigation/.test(e.message);

            if (!transient || attempt === 2) {
                throw e;
            }

            await page.waitForTimeout(500);
        }
    }
}

const hub  = process.argv[2] || 'mesozoic';
const port = process.argv[3] || '7600';
const base = `https://${hub}.${process.env.HUB_DOMAIN || 'example.com'}:${port}`;

// The widths worth caring about: a phone, and a small laptop
const widths = [
    { name: 'phone',  width: 390,  height: 844 },
    { name: 'laptop', width: 1280, height: 800 },
];

const paths = [
    '/', '/resources', '/resources/datasets', '/resources/browse',
    '/wiki/Special:AllPages',
    '/wiki/CalderBasin',
    '/wiki/CalderBasin?task=history', '/wiki/FieldNumbering?task=comments',
    '/groups/browse', '/groups/fossil-ct', '/groups/fossil-ct/wiki',
    '/groups/fossil-ct/forum', '/groups/fossil-ct/calendar',
    '/answers', '/blog', '/kb', '/forum', '/events/2026', '/collections/posts',
    '/courses/browse', '/courses/field-stratigraphy',
    '/citations/browse', '/projects/browse', '/wishlist', '/publications',
    '/poll', '/jobs', '/newsletter', '/members/1001', '/support',
    '/resources/calder-basin-measured-sections',
];

const browser = await chromium.launch({ executablePath: chromiumPath() });
const context = await browser.newContext({ ignoreHTTPSErrors: true });

let bad = 0;

for (const size of widths) {
    console.log(`\n=== ${size.name} (${size.width}px) ===`);

    const findings = new Map();
    const page = await context.newPage();
    await page.setViewportSize({ width: size.width, height: size.height });

    for (const path of paths) {
        let result;

        try {
            const response = await visit(page, base + path);

            // Web fonts change the width of everything they touch, and a
            // page measured before they arrive reports overflow that is gone
            // a moment later. Reported 5 findings on one run and 1 on the
            // next until this was here.
            await page.evaluate(() => document.fonts && document.fonts.ready);

            if (!response || response.status() >= 400) {
                console.log(`  ${path}  answered ${response ? response.status() : 'nothing'}`);
                continue;
            }

            // Content the reader cannot reach.
            //
            // Measuring an element's own scrollWidth does not answer this: a
            // wrapper counts its invisible children too, so a nav standing
            // aside for a hamburger looks like 471px of lost content on every
            // page. What matters is a thing somebody can see whose box runs
            // past the edge of an ancestor that hides what runs past it.
            result = await page.evaluate(() => {
                const clips = (style) =>
                    style.overflowX === 'hidden' || style.overflowX === 'clip';

                const invisible = (el) => {
                    const style = getComputedStyle(el);

                    return style.visibility === 'hidden'
                        || style.opacity === '0'
                        || style.clip !== 'auto'
                        || style.clipPath !== 'none';
                };

                const found = new Map();

                for (const el of document.querySelectorAll('body *')) {
                    const box = el.getBoundingClientRect();

                    if (box.width <= 1 || box.height <= 1) {
                        continue;
                    }

                    // Nothing about something nobody can see, at any depth
                    let hidden = false;

                    for (let up = el; up && up !== document.body; up = up.parentElement) {
                        if (invisible(up)) {
                            hidden = true;
                            break;
                        }
                    }

                    if (hidden || el.closest('[aria-hidden="true"]')) {
                        continue;
                    }

                    // The nearest ancestor that would cut it off
                    let cutter = null;

                    for (let up = el.parentElement; up; up = up.parentElement) {
                        const style = getComputedStyle(up);

                        if (style.overflowX === 'auto' || style.overflowX === 'scroll') {
                            break;
                        }

                        if (clips(style)) {
                            cutter = up;
                            break;
                        }
                    }

                    if (!cutter) {
                        continue;
                    }

                    const edge = cutter.getBoundingClientRect().left + cutter.clientWidth;
                    const lost = Math.round(box.right - edge);

                    if (lost < 24) {
                        continue;
                    }

                    const name = (node) => node.tagName.toLowerCase()
                        + (node.id ? '#' + node.id : '')
                        + (node.className && typeof node.className === 'string'
                            ? '.' + node.className.trim().split(/\s+/).slice(0, 2).join('.')
                            : '');

                    // Keyed on the pair, and kept at its worst, so a table of
                    // fifty cut cells is one finding rather than fifty
                    const key = `${name(el)} cut off by ${name(cutter)}`;

                    if (!found.has(key) || found.get(key) < lost) {
                        found.set(key, lost);
                    }
                }

                return {
                    over: document.documentElement.scrollWidth - document.documentElement.clientWidth,
                    clipped: [...found.entries()]
                        .sort((a, b) => b[1] - a[1])
                        .slice(0, 4)
                        .map(([what, lost]) => `${what}: ${lost}px lost`),
                };
            }, size.width);
        } catch (e) {
            console.log(`  ${path}  ${e.message.split('\n')[0]}`);
            continue;
        }

        if (result.over > 1) {
            findings.set(`the page scrolls sideways`, (findings.get('the page scrolls sideways') || []).concat(path));
        }

        for (const c of new Set(result.clipped)) {
            const seen = findings.get(c) || [];

            if (!seen.includes(path)) {
                findings.set(c, seen.concat(path));
            }
        }
    }

    await page.close();

    // Said once each, with how far it reaches: a finding on every page is the
    // template, a finding on one is that page
    const ranked = [...findings.entries()].sort((a, b) => b[1].length - a[1].length);

    for (const [what, where] of ranked) {
        bad++;

        if (where.length === paths.length) {
            console.log(`  every page: ${what}`);
        } else if (where.length > 3) {
            console.log(`  ${where.length} pages: ${what}`);
            console.log(`      ${where.slice(0, 3).join(', ')}, ...`);
        } else {
            console.log(`  ${where.join(', ')}`);
            console.log(`      ${what}`);
        }
    }

    if (!ranked.length) {
        console.log('  nothing is clipped or spilling.');
    }
}

await browser.close();

console.log(`\n${bad} page/width combinations scroll sideways.`);
process.exit(bad > 0 ? 1 : 0);
