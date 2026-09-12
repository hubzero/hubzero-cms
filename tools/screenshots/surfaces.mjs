/**
 * Which surfaces on a hub are not in its palette, and what shape they are.
 *
 * A template is judged on its colours and then the page is assembled out of a
 * dozen component stylesheets that each reach for a neutral grey, and the
 * result is half one palette and half another. Reading the stylesheets does
 * not find them - a token three files away resolves to #eee somewhere you
 * were not looking - so this reads the rendered page instead: every element
 * painting a surface, what it paints it, and what radius it has.
 *
 * Greyish only, since a palette's own colours are not what is being looked
 * for: anything whose channels are within 14 of each other and which is not
 * one of the colours given below.
 *
 *   node tools/screenshots/surfaces.mjs [hub] [port]
 */
import { chromium } from 'playwright';
import { readdirSync, existsSync } from 'node:fs';
function cp(){const r=process.env.HOME+'/.cache/ms-playwright';for(const b of readdirSync(r).filter(d=>d.startsWith('chromium-')).sort((a,b)=>Number(b.split('-')[1])-Number(a.split('-')[1])))for(const x of ['chrome-linux64/chrome','chrome-linux/chrome'])if(existsSync(`${r}/${b}/${x}`))return `${r}/${b}/${x}`;}
const hub  = process.argv[2] || 'mesozoic';
const port = process.argv[3] || '7600';
const base = `https://${hub}.${process.env.HUB_DOMAIN || 'example.com'}:${port}`;

const paths = [
 '/', '/resources', '/resources/browse', '/resources/calder-basin-measured-sections',
 '/wiki/CalderBasin', '/wiki/Special:AllPages', '/wiki/CalderBasin?task=history',
 '/groups', '/groups/browse', '/groups/fossil-ct', '/groups/fossil-ct/wiki',
 '/groups/fossil-ct/forum', '/groups/fossil-ct/calendar', '/groups/fossil-ct/members',
 '/answers', '/answers/question/1', '/blog', '/kb', '/forum', '/forum/general-discussion',
 '/events/2026', '/events/details/2', '/collections/posts', '/courses/browse',
 '/courses/field-stratigraphy', '/citations/browse', '/projects/browse',
 '/wishlist', '/wishlist/1', '/publications', '/publications/1', '/poll', '/jobs',
 '/newsletter', '/members', '/members/1001', '/support', '/support/tickets/new',
 '/tags', '/whatsnew', '/search?terms=calder', '/login', '/register',
];
const br = await chromium.launch({ executablePath: cp() });
const ctx = await br.newContext({ ignoreHTTPSErrors: true });
const p = await ctx.newPage();
await p.setViewportSize({ width: 1280, height: 900 });

// Anything in the palette is fine; anything else that paints a surface is not
const known = new Set(['rgb(250, 247, 242)','rgb(242, 237, 228)','rgb(255, 255, 255)',
  'rgb(230, 223, 212)','rgb(138, 90, 43)','rgb(107, 68, 32)','rgb(168, 112, 56)',
  'rgb(43, 38, 34)','rgb(92, 83, 73)','rgb(99, 90, 80)']);

const seen = new Map();
let looked = 0;

for (const path of paths) {
  let r; try { r = await p.goto(base + path, { waitUntil:'load', timeout: 30000 }); }
  catch { continue; }
  if (!r || r.status() >= 400) continue;
  looked++;
  const found = await p.evaluate(([known]) => {
    const out = [];
    for (const el of document.querySelectorAll('body *')) {
      const s = getComputedStyle(el), b = el.getBoundingClientRect();
      if (b.width < 24 || b.height < 12) continue;
      const bg = s.backgroundColor;
      const m = bg.match(/[\d.]+/g);
      if (!m || (m[3] !== undefined && Number(m[3]) < 0.02)) continue;
      const key = `rgb(${m[0]}, ${m[1]}, ${m[2]})`;
      if (known.includes(key)) continue;
      // greyish: channels close together
      const [rr,gg,bb] = [Number(m[0]),Number(m[1]),Number(m[2])];
      const spread = Math.max(rr,gg,bb) - Math.min(rr,gg,bb);
      if (spread > 14) continue;
      const name = el.tagName.toLowerCase() + (el.id ? '#'+el.id : '')
        + (el.className && typeof el.className === 'string' ? '.'+el.className.trim().split(/\s+/).slice(0,2).join('.') : '');
      out.push(`${bg}  ${name}  r${parseInt(s.borderTopLeftRadius)||0}`);
    }
    return out;
  }, [[...known]]);
  for (const f of found) {
    const e = seen.get(f) || new Set();
    e.add(path); seen.set(f, e);
  }
}
console.log(`Looked at ${looked} pages.\n`);
for (const [what, where] of [...seen.entries()].sort((a,b)=>b[1].size-a[1].size).slice(0, 30)) {
  console.log(`  ${String(where.size).padStart(3)} pages  ${what}`);
  if (where.size <= 3) console.log(`            ${[...where].join(', ')}`);
}
await br.close();
