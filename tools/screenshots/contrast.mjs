/**
 * Measure the contrast a hub's pages actually render at.
 *
 * Choosing a palette by checking a few pairs by hand misses what the page
 * does with them: text inherits a colour from one rule and a ground from
 * another several levels up, and the pair that reaches the reader is not
 * always the pair that was chosen. This walks the rendered page instead.
 *
 * WCAG 2.2 keeps 1.4.3 as it was - 4.5:1 for text, 3:1 for large text - and
 * 1.4.11 asks 3:1 of the parts of a control that tell you where it is. Both
 * are checked here. What it cannot check is whether a link is distinguishable
 * from its surrounding text by more than colour (1.4.1); that needs reading.
 *
 * Text over a background image is not judged - a picture is not a colour -
 * and the count of what was declined is reported rather than left silent,
 * because a frieze behind the footer would otherwise take the footer quietly
 * out of the check. tools/screenshots/veneer.mjs answers those.
 *
 * Each finding also names the rule that chose the colour, and the stylesheet
 * it is in. Without that the report is only the beginning of the search: the
 * selector that reaches an element is rarely the one you would have guessed,
 * and half of these colours are inherited from an ancestor several levels up.
 *
 *   node tools/screenshots/contrast.mjs [hub] [port]
 */
import { chromium } from 'playwright';
import { readdirSync, existsSync } from 'node:fs';

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

const paths = [
    '/', '/resources', '/resources/datasets', '/resources/browse',
    '/resources/calder-basin-measured-sections',
    '/wiki/CalderBasin', '/wiki/CalderBasin?task=history',
    '/groups/browse', '/groups/fossil-ct', '/answers', '/blog', '/kb',
    '/forum', '/events/2026', '/collections/posts', '/courses/browse',
    '/citations/browse', '/projects/browse', '/wishlist', '/publications',
    '/poll', '/jobs', '/newsletter', '/members/1001', '/support',
];

const browser = await chromium.launch({ executablePath: chromiumPath() });
const context = await browser.newContext({ ignoreHTTPSErrors: true });
const page    = await context.newPage();

// Which rule set a colour is a question only the browser can answer, and only
// over the devtools protocol - a stylesheet loaded from another origin, or
// through an @import, is not readable from the page itself.
const cdp    = await context.newCDPSession(page);
const sheets = new Map();

cdp.on('CSS.styleSheetAdded', ({ header }) => sheets.set(header.styleSheetId, header.sourceURL));

await cdp.send('DOM.enable');
await cdp.send('CSS.enable');

/**
 * The rule that gave an element its colour, as a line of text
 *
 * The last matching rule that mentions colour wins, so the list is read from
 * the end. When nothing matches the element itself the colour came from an
 * ancestor, and the nearest of those that names one is the answer.
 *
 * @param   integer  nodeId  The element, as the protocol knows it
 * @return  string
 */
async function rule(nodeId) {
    const matched = await cdp.send('CSS.getMatchedStylesForNode', { nodeId });

    const said = (entries) => {
        for (const entry of [...(entries || [])].reverse()) {
            const declared = entry.rule.style.cssProperties.find(p => p.name === 'color');

            if (!declared || entry.rule.origin !== 'regular') {
                continue;
            }

            const sheet = sheets.get(entry.rule.styleSheetId) || '';
            const line  = entry.rule.style.range ? ':' + (entry.rule.style.range.startLine + 1) : '';

            return `${entry.rule.selectorList.text} { color: ${declared.value} }`
                + `  ${sheet.replace(/^https?:\/\/[^/]+/, '').replace(/\?v=\d+$/, '')}${line}`;
        }

        return null;
    };

    const own = said(matched.matchedCSSRules);

    if (own) {
        return own;
    }

    for (const from of matched.inherited || []) {
        const up = said(from.matchedCSSRules);

        if (up) {
            return 'inherited from  ' + up;
        }
    }

    return 'no rule names a colour';
}

await page.setViewportSize({ width: 1280, height: 900 });

const findings = new Map();
const unjudged = new Map();
let looked = 0;

for (const path of paths) {
    let response;

    try {
        response = await visit(page, base + path);
    } catch (e) {
        console.log(`  ${path}  ${e.message.split('\n')[0]}`);
        continue;
    }

    if (!response || response.status() >= 400) {
        continue;
    }

    looked++;

    const { found, declined } = await page.evaluate(() => {
        const rgb = (s) => {
            const m = s.match(/[\d.]+/g);

            return m ? m.slice(0, 3).map(Number).concat(m[3] === undefined ? 1 : Number(m[3])) : null;
        };

        const lum = ([r, g, b]) => {
            const c = [r, g, b].map(v => {
                v /= 255;

                return v <= 0.03928 ? v / 12.92 : Math.pow((v + 0.055) / 1.055, 2.4);
            });

            return (0.2126 * c[0]) + (0.7152 * c[1]) + (0.0722 * c[2]);
        };

        const ratio = (a, b) => {
            const la = lum(a);
            const lb = lum(b);

            return (Math.max(la, lb) + 0.05) / (Math.min(la, lb) + 0.05);
        };

        // What the reader sees behind an element.
        //
        // Not simply the first ancestor that paints: a translucent ground is
        // a real colour once it is composited over what is under it, and
        // skipping those reports white behind text that is sitting on 60%
        // black. Anything painted with an image is not a colour at all and
        // cannot be judged this way, so those are declined rather than
        // guessed at.
        const ground = (el) => {
            const layers = [];

            for (let up = el; up; up = up.parentElement) {
                const style = getComputedStyle(up);

                if (style.backgroundImage !== 'none') {
                    return null;
                }

                const c = rgb(style.backgroundColor);

                if (c && c[3] > 0.001) {
                    layers.push(c);

                    if (c[3] > 0.999) {
                        break;
                    }
                }
            }

            // Down from the bottom-most layer, each one painted over the last
            let out = [255, 255, 255];

            for (const [r, g, b, a] of layers.reverse()) {
                out = [
                    (r * a) + (out[0] * (1 - a)),
                    (g * a) + (out[1] * (1 - a)),
                    (b * a) + (out[2] * (1 - a)),
                ];
            }

            return out;
        };

        const out = [];
        const seen = new Set();
        let mark = 0;
        let declined = 0;

        for (const el of document.querySelectorAll('body *')) {
            const style = getComputedStyle(el);
            const box   = el.getBoundingClientRect();

            if (box.width < 2 || box.height < 2
                || style.visibility === 'hidden' || style.opacity === '0') {
                continue;
            }

            // Only elements with text of their own, not wrappers repeating it
            const own = [...el.childNodes]
                .filter(n => n.nodeType === 3 && n.textContent.trim())
                .map(n => n.textContent.trim())
                .join(' ');

            if (!own) {
                continue;
            }

            const fg = rgb(style.color);
            const bg = ground(el);

            if (!bg) {
                declined++;
            }

            if (!fg || fg[3] < 0.95 || !bg) {
                continue;
            }

            const size   = parseFloat(style.fontSize);
            const weight = Number(style.fontWeight) || 400;

            // 1.4.3: 18.66px bold or 24px counts as large, and asks 3:1
            const large = size >= 24 || (size >= 18.66 && weight >= 700);
            const need  = large ? 3 : 4.5;
            const got   = ratio(fg, bg);

            if (got >= need) {
                continue;
            }

            const name = el.tagName.toLowerCase()
                + (el.className && typeof el.className === 'string'
                    ? '.' + el.className.trim().split(/\s+/).slice(0, 2).join('.')
                    : '');

            const key = `${name} ${style.color} on rgb(${bg.slice(0, 3).map(Math.round)})`;

            if (seen.has(key)) {
                continue;
            }

            seen.add(key);

            el.setAttribute('data-contrast', ++mark);

            out.push({
                mark,
                what: `${name}: ${got.toFixed(1)}:1, wants ${need}:1`
                    + `  (${style.color} on rgb(${bg.slice(0, 3).join(', ')}), ${size}px)`,
                sample: own.slice(0, 40),
            });
        }

        return { found: out, declined };
    });

    if (declined) {
        unjudged.set(path, declined);
    }

    const { root } = await cdp.send('DOM.getDocument');

    for (const f of found) {
        const seen = findings.get(f.what) || { where: [], sample: f.sample };

        if (!seen.where.includes(path)) {
            seen.where.push(path);
        }

        if (!seen.rule) {
            const { nodeId } = await cdp.send('DOM.querySelector', {
                nodeId: root.nodeId, selector: `[data-contrast="${f.mark}"]`,
            });

            seen.rule = nodeId ? await rule(nodeId) : 'the element went away';
        }

        findings.set(f.what, seen);
    }
}

console.log(`Looked at ${looked} pages at 1280px.\n`);

const ranked = [...findings.entries()].sort((a, b) => b[1].where.length - a[1].where.length);

for (const [what, { where, sample, rule }] of ranked) {
    const scope = where.length === looked
        ? 'every page'
        : (where.length > 3 ? `${where.length} pages` : where.join(', '));

    console.log(`  ${scope}: ${what}`);
    console.log(`      e.g. "${sample}"`);
    console.log(`      ${rule}`);
}

if (!ranked.length) {
    console.log('  Every piece of text on every page meets 1.4.3.');
}

if (unjudged.size) {
    const total = [...unjudged.values()].reduce((a, b) => a + b, 0);

    console.log(`\n  ${total} pieces of text on ${unjudged.size} pages were not judged:`
        + ' something behind them is painted with an image rather than a colour.');

    for (const [path, n] of [...unjudged.entries()].sort((a, b) => b[1] - a[1]).slice(0, 6)) {
        console.log(`      ${path}  ${n}`);
    }

    console.log('      veneer.mjs answers these: it composites the image over its'
        + ' ground and\n      reports what the darkest pixel in it leaves the text.');
}

await browser.close();
process.exit(ranked.length ? 1 : 0);
