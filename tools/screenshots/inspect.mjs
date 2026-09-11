/**
 * Look at one element on one page, at a given width.
 *
 * overflow.mjs says a thing is cut off; this says why. Three questions come up
 * every time and this answers all three:
 *
 *   chain   what is between this element and the page, and how wide each is
 *   style   the properties that decide a width, computed
 *   widest  everything on the page wider than the viewport
 *
 *   node tools/screenshots/inspect.mjs chain  <url> '<selector>' [width]
 *   node tools/screenshots/inspect.mjs style  <url> '<selector>' [width]
 *   node tools/screenshots/inspect.mjs widest <url> [width]
 */
import { chromium } from 'playwright';
import { readdirSync, existsSync } from 'node:fs';

/**
 * The newest chromium already on this machine, rather than the exact build
 * Playwright would like to download. Set HUB_CHROMIUM to override.
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

const mode = process.argv[2];
const url  = process.argv[3];

if (!mode || !url) {
    console.error('usage: inspect.mjs chain|style|widest <url> [selector] [width]');
    process.exit(2);
}

const selector = (mode === 'widest') ? null : process.argv[4];
const width    = Number((mode === 'widest' ? process.argv[4] : process.argv[5]) || 390);

const browser = await chromium.launch({ executablePath: chromiumPath() });
const context = await browser.newContext({ ignoreHTTPSErrors: true });
const page    = await context.newPage();

await page.setViewportSize({ width, height: 844 });
await page.goto(url, { waitUntil: 'domcontentloaded', timeout: 30000 });

const name = (node) => node.tagName.toLowerCase()
    + (node.id ? '#' + node.id : '')
    + (node.className && typeof node.className === 'string'
        ? '.' + node.className.trim().split(/\s+/).slice(0, 3).join('.')
        : '');

if (mode === 'chain') {
    console.log(await page.evaluate(([sel, nameSrc]) => {
        const label = eval(nameSrc);
        let el = document.querySelector(sel);

        if (!el) {
            return `nothing matches ${sel}`;
        }

        const out = [];

        for (; el && el !== document.documentElement; el = el.parentElement) {
            const style = getComputedStyle(el);
            const box   = el.getBoundingClientRect();

            out.push(`${label(el)}   w=${Math.round(box.width)}`
                + ` client=${el.clientWidth} overflow-x=${style.overflowX}`
                + ` display=${style.display}`);
        }

        return out.join('\n');
    }, [selector, name.toString()]));
} else if (mode === 'style') {
    console.log(await page.evaluate((sel) => {
        const el = document.querySelector(sel);

        if (!el) {
            return `nothing matches ${sel}`;
        }

        const style = getComputedStyle(el);
        const want  = ['width', 'minWidth', 'maxWidth', 'whiteSpace', 'display',
            'float', 'flex', 'position', 'visibility', 'overflowX',
            'paddingLeft', 'paddingRight', 'boxSizing', 'overflowWrap'];

        return want.map(k => `  ${k}: ${style[k]}`).join('\n');
    }, selector));
} else if (mode === 'widest') {
    console.log(await page.evaluate(([viewport, nameSrc]) => {
        const label = eval(nameSrc);
        const out   = [];

        for (const el of document.querySelectorAll('body *')) {
            const box = el.getBoundingClientRect();

            if (box.width > viewport) {
                out.push(`${label(el)}  ${Math.round(box.width)}px`
                    + `  (left ${Math.round(box.left)})`);
            }
        }

        return out.slice(0, 20).join('\n') || 'nothing is wider than the viewport';
    }, [width, name.toString()]));
} else {
    console.error(`unknown mode "${mode}"`);
    process.exitCode = 2;
}

await browser.close();
