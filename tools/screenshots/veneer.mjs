/**
 * What does a band of silhouettes do to the text laid over it?
 *
 * contrast.mjs walks the rendered page and declines any element with a
 * background image behind it, because a picture is not a colour and it will
 * not guess. That is the right answer there and it leaves a hole here: put a
 * frieze behind the footer and the footer stops being checked.
 *
 * So this asks the narrower question the friezes actually pose. Composite the
 * band over the ground it sits on, find the darkest pixel in it - which is
 * where two faint shapes crossed, not where one is - and report what each
 * colour of text set over it is left with.
 *
 * The test is one-sided, and worth knowing which side. A pass is conclusive:
 * if the darkest pixel is light enough then every pixel is, wherever the text
 * falls. A failure is not - it says the image has somewhere dark enough to
 * matter, which only matters if text can actually reach it. A band drawn to
 * sit behind text is the first case. A decoration in one corner is the
 * second, and wants looking at rather than believing.
 *
 *   node tools/screenshots/veneer.mjs [--opacity=N] <svg> <ground> <ink:px[:weight]> ...
 *
 * --opacity is what the page draws the band at, where that is set in CSS
 * rather than baked into the file. Measuring a band at full strength when the
 * page shows it at a tenth of that answers a question nobody asked.
 *
 *   node tools/screenshots/veneer.mjs \
 *       core/templates/mesozoic/images/frieze.svg '#F2EDE4' '#635A50:15.2'
 */
import { chromium } from 'playwright';
import { readFileSync, readdirSync, existsSync } from 'node:fs';
import { basename, extname } from 'node:path';

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

const argv = process.argv.slice(2);
const alpha = (() => {
    const at = argv.findIndex(a => a.startsWith('--opacity='));

    if (at < 0) {
        return 1;
    }

    const value = Number(argv.splice(at, 1)[0].split('=')[1]);

    return (value > 0 && value <= 1) ? value : 1;
})();

const [svgPath, ground, ...inks] = argv;

if (!svgPath || !ground || !inks.length) {
    console.error('usage: veneer.mjs <svg> <ground> <ink:px[:weight]> ...');
    process.exit(2);
}

// An SVG goes over as text and is drawn at its viewBox size: these carry no
// width or height of their own - they are scaled by whatever CSS asks of them
// - and an <img> with no intrinsic size renders at 300x150, which samples a
// shrunk copy and antialiases away the very pixel this is looking for.
// Anything else goes over as bytes and is drawn at whatever size it is.
const type = {
    '.svg': 'image/svg+xml',
    '.png': 'image/png',
    '.gif': 'image/gif',
    '.jpg': 'image/jpeg',
    '.jpeg': 'image/jpeg',
    '.webp': 'image/webp',
}[extname(svgPath).toLowerCase()];

if (!type) {
    console.error(`${basename(svgPath)} is not an image this can read`);
    process.exit(2);
}

let size = null;

if (type === 'image/svg+xml') {
    const box = readFileSync(svgPath, 'utf8').match(/viewBox="0 0 ([\d.]+) ([\d.]+)"/);

    if (!box) {
        console.error(`${svgPath} has no viewBox, so there is no size to draw it at`);
        process.exit(2);
    }

    size = [Math.round(Number(box[1])), Math.round(Number(box[2]))];
}

const data = 'data:' + type + ';base64,' + readFileSync(svgPath).toString('base64');

const browser = await chromium.launch({ executablePath: chromiumPath() });
const context = await browser.newContext();
const page    = await context.newPage();

const result = await page.evaluate(async ([data, ground, size, alpha]) => {
    const hex = (s) => {
        const m = s.replace('#', '');
        const n = m.length === 3 ? m.split('').map(c => c + c).join('') : m;

        return [0, 2, 4].map(i => parseInt(n.slice(i, i + 2), 16));
    };

    // The band at its own size, over the ground, with nothing else on it
    const img = new Image();

    await new Promise((ok, no) => {
        img.onload = ok;
        img.onerror = () => no(new Error('the image would not load'));
        img.src = data;
    });

    const canvas = document.createElement('canvas');

    canvas.width  = size ? size[0] : img.naturalWidth;
    canvas.height = size ? size[1] : img.naturalHeight;

    const ctx = canvas.getContext('2d');
    const bg  = hex(ground);

    ctx.fillStyle = `rgb(${bg.join(',')})`;
    ctx.fillRect(0, 0, canvas.width, canvas.height);
    ctx.globalAlpha = alpha;
    ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
    ctx.globalAlpha = 1;

    const lum = ([r, g, b]) => {
        const c = [r, g, b].map(v => {
            v /= 255;

            return v <= 0.03928 ? v / 12.92 : Math.pow((v + 0.055) / 1.055, 2.4);
        });

        return (0.2126 * c[0]) + (0.7152 * c[1]) + (0.0722 * c[2]);
    };

    const px = ctx.getImageData(0, 0, canvas.width, canvas.height).data;

    let darkest = null;
    let lowest  = Infinity;
    let painted = 0;

    for (let i = 0; i < px.length; i += 4) {
        const at = [px[i], px[i + 1], px[i + 2]];
        const l  = lum(at);

        if (at[0] !== bg[0] || at[1] !== bg[1] || at[2] !== bg[2]) {
            painted++;
        }

        if (l < lowest) {
            lowest  = l;
            darkest = at;
        }
    }

    return {
        size: [canvas.width, canvas.height],
        darkest,
        lowest,
        covered: painted / (px.length / 4),
    };
}, [data, ground, size, alpha]);

const lum = ([r, g, b]) => {
    const c = [r, g, b].map(v => {
        v /= 255;

        return v <= 0.03928 ? v / 12.92 : Math.pow((v + 0.055) / 1.055, 2.4);
    });

    return (0.2126 * c[0]) + (0.7152 * c[1]) + (0.0722 * c[2]);
};

const hex = (s) => {
    const m = s.replace('#', '');
    const n = m.length === 3 ? m.split('').map(c => c + c).join('') : m;

    return [0, 2, 4].map(i => parseInt(n.slice(i, i + 2), 16));
};

console.log(`${svgPath}  ${result.size[0]}x${result.size[1]}`);
console.log(`  on ${ground}${alpha < 1 ? ` at ${alpha} opacity` : ''},`
    + ` ${(result.covered * 100).toFixed(1)}% of it is painted at all`);
console.log(`  darkest pixel rgb(${result.darkest.join(', ')})\n`);

let failed = 0;

for (const spec of inks) {
    const [colour, px = '16', weight = '400'] = spec.split(':');
    const size  = parseFloat(px);
    const large = size >= 24 || (size >= 18.66 && Number(weight) >= 700);
    const need  = large ? 3 : 4.5;

    const la = lum(hex(colour));
    const lb = result.lowest;
    const got = (Math.max(la, lb) + 0.05) / (Math.min(la, lb) + 0.05);

    const ok = got >= need;

    if (!ok) {
        failed++;
    }

    console.log(`  ${ok ? 'ok  ' : 'dark'}  ${colour} at ${size}px`
        + `  ${got.toFixed(2)}:1, wants ${need}:1`);
}

console.log();

if (failed) {
    console.log(`  ${failed} of ${inks.length} would not be read over the darkest part of`
        + ' it.\n  Whether that is a failure depends on whether text falls there:'
        + ' look at the page.');
} else {
    console.log('  Every colour given still reads over the darkest part of it,'
        + ' so it reads\n  everywhere on it.');
}

await browser.close();
process.exit(failed ? 1 : 0);
