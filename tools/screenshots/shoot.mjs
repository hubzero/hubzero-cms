/**
 * Photograph a hub, page by page, as each of the people who use it.
 *
 * The documentation needs pictures, and pictures of a hub are only worth
 * having if they are the same pictures next time: a run that differs from the
 * last one in ways nobody chose is a run nobody can diff, and the whole point
 * of keeping them in the tree is to see what a change did.
 *
 * So everything that would drift is pinned - the clock, animations, the
 * caret, the debug bar - and the page is given until its webfonts have
 * arrived before anything is captured. Text measured before its font lands is
 * text of the wrong width, which this repository has already learned once.
 *
 *   node tools/screenshots/shoot.mjs [hub] [port] [--only=name,name]
 *
 * Writes docs/screenshots/<hub>/<viewport>/<name>.png and a manifest beside
 * them. What it cannot do is tell you whether the picture is any good: read
 * tools/screenshots/README.md for what to look for.
 */
import { chromium } from 'playwright';
import { readdirSync, existsSync, mkdirSync, writeFileSync } from 'node:fs';
import { pages } from './pages.mjs';

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

const args = process.argv.slice(2).filter(a => !a.startsWith('--'));
const only = (process.argv.find(a => a.startsWith('--only=')) || '').split('=')[1];

const hub  = args[0] || 'mesozoic';
const port = args[1] || '7600';
const base = `https://${hub}.${process.env.HUB_DOMAIN || 'example.com'}:${port}`;

// Who the hub is photographed as. A manager is also an instructor here, which
// keeps the count down without losing a view: every page either of them can
// reach, one of them can.
const people = {
    member:  { username: 'mokonkwo',  password: process.env.HUB_MEMBER_PASSWORD || 'MesozoicDemo2026' },
    manager: { username: 'sberglund', password: process.env.HUB_MEMBER_PASSWORD || 'MesozoicDemo2026' },
    admin:   { username: 'admin',     password: process.env.HUB_ADMIN_PASSWORD  || 'ClaudeDev2026' },
};

const viewports = {
    desktop: { width: 1440, height: 900 },
    phone:   { width: 390, height: 844 },
};

// Whatever a picture would otherwise disagree with itself about
const STILL = `
    *, *:before, *:after {
        animation-duration: 0s !important;
        animation-delay: 0s !important;
        transition-duration: 0s !important;
        transition-delay: 0s !important;
        caret-color: transparent !important;
        scroll-behavior: auto !important;
    }
    #system-debug, .profiler, #dbg-container { display: none !important; }
`;

const browser = await chromium.launch({ executablePath: chromiumPath() });

/**
 * Go somewhere, allowing for chromium changing its mind about the certificate
 *
 * The first navigation a fresh context makes sometimes fails with
 * ERR_CERT_VERIFIER_CHANGED: the verifier is being reconfigured underneath it
 * and the request is cancelled rather than refused. The second attempt is
 * always fine, and treating it as a failed page would lose a picture for a
 * reason that has nothing to do with the page.
 *
 * @param   object  page  Where to do it
 * @param   string  url   Where to go
 * @return  object  The response
 */
async function visit(page, url) {
    try {
        return await page.goto(url, { waitUntil: 'load', timeout: 45000 });
    } catch (e) {
        if (!/ERR_CERT_VERIFIER_CHANGED|ERR_NETWORK_CHANGED|ERR_ABORTED/.test(e.message)) {
            throw e;
        }

        return await page.goto(url, { waitUntil: 'load', timeout: 45000 });
    }
}

/**
 * A browser context for one persona, signed in and ready
 *
 * @param   string  who  A key of people, or 'guest'
 * @return  object  The context
 */
async function contextFor(who) {
    const context = await browser.newContext({
        ignoreHTTPSErrors: true,
        // One clock for every run, so anything the page works out for itself
        // says the same thing each time
        timezoneId: 'UTC',
        locale: 'en-GB',
        reducedMotion: 'reduce',
        colorScheme: 'light',
    });

    await context.addInitScript(`{
        const fixed = new Date('2026-09-01T12:00:00Z').valueOf();
        const Real = Date;
        Date = class extends Real {
            constructor(...a) { return a.length ? new Real(...a) : new Real(fixed); }
            static now() { return fixed; }
        };
        Date.parse = Real.parse;
        Date.UTC = Real.UTC;
    }`);

    if (who === 'guest') {
        return context;
    }

    const person = people[who];

    if (!person) {
        throw new Error(`there is nobody called "${who}"`);
    }

    const page = await context.newPage();

    // The admin signs in at its own door
    const door = (who === 'admin') ? `${base}/administrator/` : `${base}/login`;

    await visit(page, door);
    await page.fill('input[name="username"]', person.username);
    await page.fill('input[name="passwd"]', person.password);

    // Not Promise.all with waitForLoadState: the page is already loaded, so
    // that resolves at once and the content is read mid-navigation - which
    // reads as a failed sign-in on a sign-in that worked
    await page.click('input.login-submit, input[type="submit"], button[type="submit"]');

    // Wait for the form to actually go. waitForLoadState can return while a
    // redirect chain is still running, and navigating again during one aborts
    // it - which arrives as ERR_ABORTED on a page that was never at fault.
    await page.waitForSelector('input[name="passwd"]', { state: 'detached', timeout: 20000 })
        .catch(() => {});
    await page.waitForLoadState('load');

    // Asked rather than inferred: a page that still offers the form is a
    // sign-in that did not happen, whatever it looks like
    await visit(page, (who === 'admin') ? `${base}/administrator/` : `${base}/members/myaccount`);

    if (await page.$('input[name="passwd"]')) {
        throw new Error(`could not sign in as ${person.username}`);
    }

    await page.close();

    return context;
}

const wanted = only ? pages.filter(p => only.split(',').includes(p.name)) : pages;
const contexts = {};
const manifest = [];

let taken = 0;
let failed = 0;

for (const [label, size] of Object.entries(viewports)) {
    const out = `docs/screenshots/${hub}/${label}`;

    mkdirSync(out, { recursive: true });

    for (const entry of wanted) {
        if (entry.at && entry.at !== label) {
            continue;
        }

        const who = entry.as || 'guest';

        if (!contexts[who]) {
            contexts[who] = await contextFor(who);
        }

        const page = await contexts[who].newPage();

        await page.setViewportSize(size);
        await page.addStyleTag({ content: STILL }).catch(() => {});

        let response;

        try {
            response = await visit(page, base + entry.url);
        } catch (e) {
            console.log(`  FAILED  ${label}/${entry.name}  ${e.message.split('\n')[0]}`);
            failed++;
            await page.close();
            continue;
        }

        const status = response ? response.status() : 0;

        if (status >= 400) {
            console.log(`  FAILED  ${label}/${entry.name}  answered ${status}`);
            failed++;
            await page.close();
            continue;
        }

        // Animations are killed by a stylesheet the page has to have parsed,
        // and webfonts change the width of everything they touch
        await page.addStyleTag({ content: STILL }).catch(() => {});
        await page.evaluate(() => document.fonts && document.fonts.ready);
        await page.evaluate(() => window.scrollTo(0, 0));

        const file = `${out}/${entry.name}.png`;

        await page.screenshot({ path: file, fullPage: true });

        manifest.push({ name: entry.name, url: entry.url, as: who, viewport: label, status });
        taken++;

        await page.close();
    }
}

writeFileSync(
    `docs/screenshots/${hub}/manifest.json`,
    JSON.stringify({ hub, base, taken: manifest }, null, 4) + '\n'
);

for (const context of Object.values(contexts)) {
    await context.close();
}

await browser.close();

console.log(`\n${taken} pictures of ${hub}, ${failed} that could not be taken.`);
console.log('Now look at them: tools/screenshots/README.md says what for.');

process.exit(failed ? 1 : 0);
