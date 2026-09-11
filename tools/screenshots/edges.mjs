/**
 * Where things line up, at several widths.
 *
 * Aligning a panel with the content under it by eye is guesswork; the gutter
 * comes from a scale that changes at each breakpoint.
 *
 *   node tools/screenshots/edges.mjs <url> '<selector>' ['<selector>' ...]
 */
import { chromium } from 'playwright';
import { readdirSync, existsSync } from 'node:fs';

function chromiumPath() {
    if (process.env.HUB_CHROMIUM) {
        return process.env.HUB_CHROMIUM;
    }

    const root = process.env.PLAYWRIGHT_BROWSERS_PATH
        || `${process.env.HOME}/.cache/ms-playwright`;

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

const url       = process.argv[2];
const selectors = process.argv.slice(3);

const browser = await chromium.launch({ executablePath: chromiumPath() });
const page    = await (await browser.newContext({ ignoreHTTPSErrors: true })).newPage();

for (const width of [1600, 1440, 1280, 1024, 700, 390]) {
    await page.setViewportSize({ width, height: 900 });
    await page.goto(url, { waitUntil: 'domcontentloaded', timeout: 30000 });

    const edges = await page.evaluate((sels) => sels.map((s) => {
        const el = document.querySelector(s);

        return el ? Math.round(el.getBoundingClientRect().left) : null;
    }), selectors);

    console.log(`  ${String(width).padStart(5)}px  `
        + selectors.map((s, i) => `${s} = ${edges[i]}`).join('   '));
}

await browser.close();
