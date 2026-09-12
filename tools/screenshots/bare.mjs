/**
 * Ask a hub's pages whether anything is labelled that is not there.
 *
 *   node tools/screenshots/bare.mjs [hub] [port]
 *
 * A heading is a promise that something follows it. Components keep that
 * promise by guarding the whole block - but several of them guarded on the
 * list they were given and then rejected every row inside the loop, which is
 * the same as not guarding at all. The result is a word with a blank
 * half-screen under it, and it only happens on a hub with nothing in it - so
 * it survived for as long as nobody photographed one.
 *
 * Run it against the bare hub, which is what the bare hub is for. Run it
 * against a populated one too: a heading that disappears when the hub fills up
 * is a different bug, and this would find that as well.
 */
import { chromium } from 'playwright';
import { readdirSync, existsSync } from 'node:fs';
import { hubFor, addressFor } from './pages.mjs';

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

const hub = process.argv[2] || 'lucent';
const catalogue = hubFor(hub);

if (!catalogue) {
    console.error(`There is no catalogue for "${hub}".`);
    process.exit(2);
}

const { url } = addressFor(hub, process.argv[3]);

const browser = await chromium.launch({ executablePath: chromiumPath() });
const context = await browser.newContext({ ignoreHTTPSErrors: true });
const page = await context.newPage();

const findings = new Map();
let looked = 0;
let headings = 0;

for (const entry of catalogue.pages) {
    if (entry.as || entry.expect) {
        continue;
    }

    let response;

    try {
        response = await visit(page, url(entry.url));
    } catch (e) {
        console.log(`  ${entry.url}: could not be reached - ${e.message}`);
        continue;
    }

    if (!response || !response.ok()) {
        continue;
    }

    looked++;

    const found = await page.evaluate(() => {
        const out = [];
        let seen = 0;

        // A container that opens with a heading is the next section, not the
        // contents of this one
        const opensASection = (el) => {
            if (/^H[1-6]$/.test(el.tagName)) {
                return true;
            }

            const first = el.firstElementChild;

            return !!first && /^H[1-6]$/.test(first.tagName);
        };

        for (const h of document.querySelectorAll(
            '.page h2, .page h3, .contentpane h2, .contentpane h3')) {
            seen++;

            // Where this heading's section starts. Usually the heading's own
            // following siblings - but a two-column layout puts the heading
            // alone in one column and what it labels in the next, so when the
            // heading is the only thing in its box, climb out of it first.
            let from = h;

            while (from.parentElement
                && from.parentElement.children.length === 1
                && !/^(BODY|MAIN|SECTION|ASIDE)$/.test(from.parentElement.tagName)) {
                from = from.parentElement;
            }

            let n = from.nextElementSibling;
            let text = '';
            let promised = false;
            let unreadable = false;

            // Boxes that were going to hold something. A bare decorative span
            // between two headings is not a section: #page_header puts a name
            // in an h2 and "Profile" in an h3 with an empty separator between
            // them, and reading that as a heading over nothing is wrong.
            const holder = /^(UL|OL|DL|TABLE|DIV|SECTION|NAV|FORM|P|ARTICLE|FIGURE)$/;

            // Things whose content this document cannot read. A newsletter is
            // shown in an iframe, and innerText of an iframe from outside it
            // is empty however much is in there.
            const opaque = 'iframe, img, canvas, video, svg, object, embed';

            // Controls, whose label may be an icon. A calendar heads its grid
            // with the month and puts icon-only prev/next buttons beside it;
            // that row is a control group, not a section with nothing in it.
            const control = 'button, input, select, textarea, [role="button"]';

            while (n && !opensASection(n)) {
                if (holder.test(n.tagName)) {
                    promised = true;
                }

                if (n.matches(opaque) || n.querySelector(opaque)) {
                    unreadable = true;
                }

                if (n.matches(control) || n.querySelector(control)) {
                    unreadable = true;
                }

                text += n.innerText || '';
                n = n.nextElementSibling;
            }

            if (promised && !unreadable && !text.trim()) {
                out.push(h.innerText.trim().slice(0, 60));
            }
        }

        return { out, seen };
    });

    headings += found.seen;

    for (const what of found.out) {
        const where = findings.get(what) || [];

        if (!where.includes(entry.url)) {
            where.push(entry.url);
        }

        findings.set(what, where);
    }
}

await browser.close();

console.log(`Looked at ${headings} headings on ${looked} pages of ${hub}.\n`);

if (!findings.size) {
    console.log('  Every heading has something under it.');
} else {
    for (const [what, where] of [...findings.entries()]
        .sort((a, b) => b[1].length - a[1].length)) {
        console.log(`  "${what}" labels nothing: ${where.join(', ')}`);
    }
}
