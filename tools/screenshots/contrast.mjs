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
import { hubFor, addressFor } from './pages.mjs';

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

// The port is the hub's own, from the catalogue: naming a hub and getting
// another hub's port back is how a whole run of this once came back clean
// against twenty-four blank pages. An argument still overrides it.
const { base, url } = addressFor(hub, process.argv[3]);

// The hub's own catalogue, which is where the pages are described once. Only
// the ones a stranger can reach: this walks a page as it is served and does
// not sign in, and a page that redirects to a login form is a login form.
const catalogue = hubFor(hub);

if (!catalogue) {
    console.error(`there is no catalogue for "${hub}" in pages.mjs`);
    process.exit(2);
}

const paths = catalogue.pages.filter(p => !p.as && !p.expect).map(p => p.url);

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

/**
 * What a picture actually leaves under the text laid over it
 *
 * The walk above declines any text whose ground is painted with an image,
 * because an image is not a colour and guessing would be worse than saying so.
 * This is the rest of that answer, asked of the rendered page rather than of
 * the stylesheet: hide the text, photograph what is behind it, and take the
 * worst pixel under each run.
 *
 * The worst pixel and not the average. A frieze is mostly its ground with a
 * few dark shapes in it, so an average says the text is fine and a reader
 * whose word falls across a dinosaur cannot read that word.
 *
 * The screenshot is decoded by the browser that took it - drawn to a canvas in
 * a blank page - so this needs no image library.
 *
 * @param   object  page  The page, already on the right URL
 * @param   array   over  The runs the walk declined
 * @return  array   One finding per run that does not clear 1.4.3
 */
async function behind(page, over) {
    if (!over.length) {
        return [];
    }

    // Take the ink out, leaving the ground exactly as the reader sees it
    await page.evaluate(() => {
        for (const el of document.querySelectorAll('[data-veneer]')) {
            el.style.setProperty('color', 'transparent', 'important');
            el.style.setProperty('text-shadow', 'none', 'important');
        }
    });

    const shot = await page.screenshot({ fullPage: true });

    await page.evaluate(() => {
        for (const el of document.querySelectorAll('[data-veneer]')) {
            el.style.removeProperty('color');
            el.style.removeProperty('text-shadow');
        }
    });

    const worst = await canvas.evaluate(async ([png, runs]) => {
        const img = new Image();

        await new Promise((ok, no) => {
            img.onload = ok;
            img.onerror = no;
            img.src = 'data:image/png;base64,' + png;
        });

        const board = document.createElement('canvas');
        board.width = img.width;
        board.height = img.height;

        const ctx = board.getContext('2d', { willReadFrequently: true });
        ctx.drawImage(img, 0, 0);

        const lum = (r, g, b) => {
            const f = (v) => {
                v /= 255;

                return v <= 0.03928 ? v / 12.92 : Math.pow((v + 0.055) / 1.055, 2.4);
            };

            return 0.2126 * f(r) + 0.7152 * f(g) + 0.0722 * f(b);
        };

        const out = [];

        for (const run of runs) {
            let darkest = null;
            let lightest = null;

            for (const rect of run.rects) {
                const x = Math.max(0, Math.floor(rect.x));
                const y = Math.max(0, Math.floor(rect.y));
                const w = Math.min(Math.ceil(rect.w), img.width - x);
                const h = Math.min(Math.ceil(rect.h), img.height - y);

                if (w <= 0 || h <= 0) {
                    continue;
                }

                const data = ctx.getImageData(x, y, w, h).data;

                for (let i = 0; i < data.length; i += 4) {
                    const l = lum(data[i], data[i + 1], data[i + 2]);

                    if (darkest === null || l < darkest[3]) {
                        darkest = [data[i], data[i + 1], data[i + 2], l];
                    }

                    if (lightest === null || l > lightest[3]) {
                        lightest = [data[i], data[i + 1], data[i + 2], l];
                    }
                }
            }

            if (darkest) {
                out.push({ veneer: run.veneer, darkest, lightest });
            }
        }

        return out;
    }, [shot.toString('base64'), over]);

    const ratio = (a, b) => (Math.max(a, b) + 0.05) / (Math.min(a, b) + 0.05);

    const luminance = (c) => {
        const f = (v) => {
            v /= 255;

            return v <= 0.03928 ? v / 12.92 : Math.pow((v + 0.055) / 1.055, 2.4);
        };

        return 0.2126 * f(c[0]) + 0.7152 * f(c[1]) + 0.0722 * f(c[2]);
    };

    const out = [];

    for (const run of over) {
        const pixels = worst.find(w => w.veneer === run.veneer);

        if (!pixels) {
            continue;
        }

        const ink = luminance(run.fg);

        // Both ends: dark text loses against the darkest pixel and light text
        // against the lightest, and a frieze has both in it.
        const got = Math.min(
            ratio(ink, pixels.darkest[3]),
            ratio(ink, pixels.lightest[3])
        );

        const large = run.size >= 24 || (run.size >= 18.66 && run.weight >= 700);
        const need = large ? 3 : 4.5;

        if (got >= need) {
            continue;
        }

        const against = ratio(ink, pixels.darkest[3]) < ratio(ink, pixels.lightest[3])
            ? pixels.darkest
            : pixels.lightest;

        out.push({
            sample: run.sample,
            what: `${run.name}: ${got.toFixed(1)}:1 over its picture, wants ${need}:1`
                + `  (rgb(${run.fg.join(', ')}) on its worst pixel`
                + ` rgb(${against.slice(0, 3).join(', ')}), ${run.size}px)`,
        });
    }

    return out;
}

// A blank page, used only to decode the screenshots the other one takes: the
// browser is already here and it knows how to read a PNG, so nothing has to be
// added to package.json to find out what colour a pixel is.
const canvas = await context.newPage();
await canvas.setContent('<!doctype html><title>decoder</title>');

await page.setViewportSize({ width: 1280, height: 900 });

const findings = new Map();
const unjudged = new Map();

// Pages that turned out to have no text on them at all. A page with nothing to
// measure passes every rule, so a run against the wrong port once reported a
// hub as flawless on twenty-four blank responses. Say so instead.
const empty = [];

// What the pictures turned out to leave under the words
const veneer = new Map();
let looked = 0;

for (const path of paths) {
    let response;

    try {
        response = await visit(page, url(path));
    } catch (e) {
        console.log(`  ${path}  ${e.message.split('\n')[0]}`);
        continue;
    }

    if (!response || response.status() >= 400) {
        continue;
    }

    looked++;

    const { found, declined, judged, over } = await page.evaluate(() => {
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

        // Where the text actually lands, and whether any of it survives the
        // boxes it is inside. A run can be pushed out of sight without being
        // hidden: com_publications labels its ranking bar with real words and
        // then sets text-indent: 55em on the bar, which has overflow: hidden.
        // Measuring that text's colour is measuring something nobody sees, so
        // clip its rectangles to every box that crops - the element's own
        // included, since an element clips its own content - and require that
        // something is left.
        const painted = (el, nodes) => {
            const clips = [];

            for (let n = el; n && n !== document.documentElement; n = n.parentElement) {
                const s = getComputedStyle(n);

                if (s.overflowX !== 'visible' || s.overflowY !== 'visible') {
                    clips.push(n.getBoundingClientRect());
                }
            }

            if (!clips.length) {
                return true;
            }

            const range = document.createRange();

            for (const node of nodes) {
                range.selectNodeContents(node);

                for (const r of range.getClientRects()) {
                    if (r.width < 1 || r.height < 1) {
                        continue;
                    }

                    const lives = clips.every(c =>
                        r.right > c.left + 1 && r.left < c.right - 1
                        && r.bottom > c.top + 1 && r.top < c.bottom - 1);

                    if (lives) {
                        return true;
                    }
                }
            }

            return false;
        };

        const out = [];
        const seen = new Set();
        let mark = 0;
        let declined = 0;
        let judged = 0;
        let veneered = 0;
        const over = [];

        for (const el of document.querySelectorAll('body *')) {
            const style = getComputedStyle(el);
            const box   = el.getBoundingClientRect();

            if (box.width < 2 || box.height < 2
                || style.visibility === 'hidden' || style.opacity === '0') {
                continue;
            }

            // Only elements with text of their own, not wrappers repeating it
            const nodes = [...el.childNodes]
                .filter(n => n.nodeType === 3 && n.textContent.trim());

            const own = nodes.map(n => n.textContent.trim()).join(' ');

            if (!own || !painted(el, nodes)) {
                continue;
            }

            const fg = rgb(style.color);
            const bg = ground(el);

            if (!bg) {
                declined++;

                // Keep it for the second pass. A picture is not a colour, so
                // this walk cannot judge it - but the rendered page can be
                // asked what the picture actually leaves under the words.
                if (fg && fg[3] >= 0.95) {
                    const range = document.createRange();
                    const rects = [];

                    for (const node of nodes) {
                        range.selectNodeContents(node);

                        for (const r of range.getClientRects()) {
                            if (r.width >= 1 && r.height >= 1) {
                                rects.push({
                                    x: r.left + scrollX,
                                    y: r.top + scrollY,
                                    w: r.width,
                                    h: r.height,
                                });
                            }
                        }
                    }

                    if (rects.length) {
                        el.setAttribute('data-veneer', ++veneered);

                        over.push({
                            veneer: veneered,
                            fg: fg.slice(0, 3),
                            size: parseFloat(style.fontSize),
                            weight: Number(style.fontWeight) || 400,
                            name: el.tagName.toLowerCase()
                                + (el.className && typeof el.className === 'string'
                                    ? '.' + el.className.trim().split(/\s+/)[0] : ''),
                            sample: own.slice(0, 40),
                            rects,
                        });
                    }
                }
            }

            if (!fg || fg[3] < 0.95 || !bg) {
                continue;
            }

            judged++;

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

        return { found: out, declined, judged, over };
    });

    if (declined) {
        unjudged.set(path, declined);
    }

    for (const finding of await behind(page, over)) {
        const key = finding.what;
        const seen = veneer.get(key) || { where: [], sample: finding.sample };

        if (!seen.where.includes(path)) {
            seen.where.push(path);
        }

        veneer.set(key, seen);
    }

    if (!judged && !declined) {
        empty.push(path);
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

if (empty.length) {
    console.log(`\n  ${empty.length} of those pages had no text on them at all,`
        + ' so they were not a pass:');

    for (const path of empty.slice(0, 8)) {
        console.log(`      ${path}`);
    }
}

if (veneer.size) {
    console.log(`\n  Text over a picture, judged on the picture's worst pixel:`);

    for (const [what, { where, sample }] of [...veneer.entries()]
        .sort((a, b) => b[1].where.length - a[1].where.length)) {
        const scope = where.length > 3 ? `${where.length} pages` : where.join(', ');

        console.log(`  ${scope}: ${what}`);
        console.log(`      e.g. "${sample}"`);
    }
}

if (unjudged.size) {
    const total = [...unjudged.values()].reduce((a, b) => a + b, 0);

    console.log(`\n  ${total} pieces of text on ${unjudged.size} pages sit on a`
        + ' picture rather than a colour. The stylesheet cannot say what that'
        + ' leaves them,\n  so each was judged on the worst pixel actually'
        + ` painted under it: ${veneer.size ? veneer.size + ' did not clear 1.4.3' : 'all of them cleared 1.4.3'}.`);
}


await browser.close();
process.exit(ranked.length ? 1 : 0);
