const PAGES_MESOZOIC = [
    // The hub as a stranger finds it
    { name: 'home', url: '/' },
    { name: 'search', url: '/search?terms=calder' },
    { name: 'register', url: '/register' },
    { name: 'sign-in', url: '/login' },

    // Resources: the landing page, the two browsers, a record
    { name: 'resources-landing', url: '/resources' },
    { name: 'resources-tags', url: '/resources/datasets' },
    { name: 'resources-list', url: '/resources/browse' },
    { name: 'resource', url: '/resources/calder-basin-measured-sections' },
    { name: 'resource-supporting', url: '/resources/calder-quarry-map-2025/supportingdocs' },

    // The wiki, including the two views that are only reachable by argument
    { name: 'wiki-page', url: '/wiki/CalderBasin' },
    { name: 'wiki-index', url: '/wiki/Special:AllPages' },
    { name: 'wiki-history', url: '/wiki/CalderBasin?task=history' },
    { name: 'wiki-diff', url: '/wiki/CalderBasin?task=compare&oldid=1&diff=4' },
    { name: 'wiki-comments', url: '/wiki/FieldNumbering?task=comments' },

    // Groups, outside and in
    { name: 'groups-list', url: '/groups/browse' },
    { name: 'group-overview', url: '/groups/fossil-ct' },
    { name: 'group-wiki', url: '/groups/fossil-ct/wiki' },
    { name: 'group-forum', url: '/groups/fossil-ct/forum' },
    { name: 'group-calendar', url: '/groups/fossil-ct/calendar' },
    { name: 'group-members', url: '/groups/fossil-ct/members' },
    { name: 'group-closed-to-a-stranger', url: '/groups/calder-basin/forum', as: 'member' },
    { name: 'group-closed-to-a-member', url: '/groups/calder-basin/forum', as: 'manager' },

    // Everything else the pack fills
    { name: 'answers', url: '/answers' },
    { name: 'answers-question', url: '/answers/question/1' },
    { name: 'blog', url: '/blog' },
    { name: 'knowledge-base', url: '/kb' },
    { name: 'forum', url: '/forum' },
    { name: 'events', url: '/events/2026' },
    { name: 'event', url: '/events/details/2' },
    { name: 'collections', url: '/collections/posts' },
    { name: 'courses', url: '/courses/browse' },
    { name: 'course', url: '/courses/field-stratigraphy' },
    { name: 'citations', url: '/citations/browse' },
    { name: 'projects', url: '/projects/browse' },
    { name: 'wish-list', url: '/wishlist' },
    { name: 'wish', url: '/wishlist/1' },
    { name: 'publications', url: '/publications' },
    { name: 'publication', url: '/publications/1' },
    { name: 'polls', url: '/poll' },
    { name: 'jobs', url: '/jobs' },
    { name: 'newsletters', url: '/newsletter' },
    { name: 'members-directory', url: '/members', as: 'member' },
    { name: 'member-profile', url: '/members/1001' },
    { name: 'support', url: '/support' },
    { name: 'tags', url: '/tags' },

    // What only a member sees
    { name: 'my-account', url: '/members/myaccount', as: 'member' },
    { name: 'project-files', url: '/projects/calder-2026/files', as: 'manager' },
    { name: 'course-progress', url: '/courses/field-stratigraphy/summer-2026/progress', as: 'manager' },

    // And the back of the house
    { name: 'admin-home', url: '/administrator/', as: 'admin' },
    { name: 'admin-resources', url: '/administrator/index.php?option=com_resources', as: 'admin' },
    { name: 'admin-template-styles', url: '/administrator/index.php?option=com_templates&view=styles', as: 'admin' },
];

/**
 * What to photograph on each hub, as whom, and what to call it.
 *
 * One entry a picture. `as` names one of that hub's people; leaving it out
 * means a signed-out visitor, which is what most of a hub is for. `at` limits
 * an entry to one viewport where the other would say nothing.
 *
 * The names are what the documentation will reference, so they are written
 * for a reader rather than derived from the URL: a file called
 * group-calendar.png is findable and one called groups-fossil-ct-calendar.png
 * is not.
 *
 * Who visits lives here too, beside what they visit, because the two only
 * make sense together - a page listed as `as: manager` needs a hub that has a
 * manager, and the welcome hub has one account and nothing else.
 */

const demoPassword = process.env.HUB_MEMBER_PASSWORD || 'MesozoicDemo2026';
const adminPassword = process.env.HUB_ADMIN_PASSWORD || 'ClaudeDev2026';

/**
 * Colours every hub has, whatever its palette
 *
 * The notice tints are in here because they are nearly neutral by design - a
 * wash of a hue rather than the hue itself - and a diff marks an added line in
 * green because that is what a diff does. Both would otherwise be reported
 * every run, and a list with known entries in it stops being read.
 */
const COMMON = [
    'rgb(255, 255, 255)', 'rgb(0, 0, 0)',

    // Notices, tinted by kind
    'rgb(234, 243, 244)', 'rgb(249, 242, 227)',
    'rgb(251, 237, 235)', 'rgb(233, 244, 240)',

    // A difference between two revisions
    'rgb(242, 255, 242)', 'rgb(255, 242, 242)',
];

/**
 * Mesozoic: weathered sandstone, and the browns over it
 */
const PALETTE_MESOZOIC = COMMON.concat([
    'rgb(250, 247, 242)', 'rgb(242, 237, 228)',
    'rgb(230, 223, 212)', 'rgb(248, 244, 238)', 'rgb(251, 248, 244)',
    'rgb(253, 251, 248)', 'rgb(244, 239, 231)', 'rgb(239, 233, 223)',
    'rgb(240, 230, 216)', 'rgb(138, 90, 43)', 'rgb(107, 68, 32)',
    'rgb(168, 112, 56)', 'rgb(43, 38, 34)', 'rgb(92, 83, 73)',
    'rgb(99, 90, 80)',

    // The tabs and controls that are dark on purpose
    'rgb(78, 70, 61)',
]);

/**
 * Meridian: cool neutrals, and the accent's own near-neutral washes
 *
 * The accent itself is not listed. It is a knob - a hub sets it in the admin -
 * so there is no colour to list, and there does not need to be: anything with
 * a hue is not what this report is looking for.
 */
const PALETTE_MERIDIAN = COMMON.concat([
    'rgb(232, 238, 242)', 'rgb(246, 248, 250)', 'rgb(251, 252, 253)',
    'rgb(227, 231, 236)', 'rgb(205, 213, 222)', 'rgb(233, 237, 241)',
    'rgb(241, 244, 247)', 'rgb(215, 221, 228)', 'rgb(221, 226, 232)',
    'rgb(210, 218, 226)', 'rgb(31, 35, 40)', 'rgb(91, 100, 113)',

    // The band at the head of a component, and its edge, on the default teal
    'rgb(236, 243, 246)', 'rgb(184, 210, 220)',
]);

/**
 * Lucent: the core's own neutrals, which this template keeps
 *
 * Lucent is a CSS-override-only template and does not retint the shared
 * variables, so its greys are the core's greys and belong in the list. What
 * this report can still say about it is whether a colour turns up that is not
 * one of them; what is worth reading on a bare hub is the other two reports,
 * which do not depend on a palette at all.
 */
const PALETTE_LUCENT = COMMON.concat([
    'rgb(37, 37, 37)', 'rgb(118, 118, 118)', 'rgb(231, 231, 231)',
    'rgb(251, 251, 251)', 'rgb(226, 226, 226)', 'rgb(244, 246, 248)',

    // The core's own quiet surfaces, which lucent does not correct
    'rgb(238, 238, 238)', 'rgb(249, 249, 249)', 'rgb(225, 225, 225)',
    'rgb(241, 241, 241)', 'rgb(247, 247, 247)', 'rgb(212, 212, 205)',
    'rgb(245, 245, 245)', 'rgb(246, 246, 246)', 'rgb(242, 242, 242)',

    // ...and the ones that arrive as composites rather than as a value
    // anybody wrote: an input's ground is rgba(0,0,0,0.039), so what it
    // actually paints depends on what is behind it.
    'rgb(240, 240, 240)', 'rgb(239, 239, 239)', 'rgb(246, 246, 247)',
]);

export const hubs = {

    mesozoic: {
        port: '7600',
        palette: PALETTE_MESOZOIC,

        // A manager is also an instructor here, which keeps the count down
        // without losing a view: every page either of them can reach, one of
        // them can.
        people: {
            member:  { username: 'mokonkwo',  password: demoPassword },
            manager: { username: 'sberglund', password: demoPassword },
            admin:   { username: 'admin',     password: adminPassword },
        },
        pages: PAGES_MESOZOIC,
    },

    // The third hub: what somebody setting up a new one gets if they elect
    // the sample data. Not an empty hub - it ships articles, a knowledge base
    // and a few groups - and not a populated one either, so it is the state
    // most hub owners actually see first and the one a template is least
    // likely to have been designed against.
    //
    // One account, which is the administrator's. A page here is what a
    // stranger sees.
    // Lucent, judged where there is something to judge it on.
    //
    // The hub lucent is delivered on is bare on purpose, and a template shows
    // almost nothing of itself against nothing: no listing rows, no tabs, no
    // tables, no pagination, no comments. So the template is measured here
    // instead, against mesozoic's content and mesozoic's whole page list, by
    // asking that hub to render each page in lucent for the length of one
    // request. Nothing about the mesozoic hub changes; its own default is
    // still its own.
    //
    // Photographs land in docs/screenshots/lucent-on-mesozoic/, beside rather
    // than on top of the bare hub's.
    'lucent-on-mesozoic': {
        host: 'mesozoic',
        port: '7600',
        style: 4,
        palette: PALETTE_LUCENT,

        people: {
            member:  { username: 'mokonkwo',  password: demoPassword },
            manager: { username: 'sberglund', password: demoPassword },
            admin:   { username: 'admin',     password: adminPassword },
        },
        pages: PAGES_MESOZOIC,
    },

    // The fourth hub: one with nothing in it.
    //
    // Not the state a hub owner chooses - it is what the installer leaves when
    // sample data is declined - but it is the state every area of every hub
    // passes through, and the one a template is never designed against. A
    // listing with no entries, a calendar with no events, a directory with one
    // member: each of those should say so and still look like a page. That is
    // the half of a template nobody looks at, so this catalogue is nearly all
    // of it.
    //
    // Every page here is an empty one on purpose. Where a page is not empty
    // the hub is telling us something too - /members has the administrator in
    // it, and /tags has whatever the installer tagged.
    lucent: {
        port: '7700',
        palette: PALETTE_LUCENT,

        people: {
            admin: { username: 'admin', password: adminPassword },
        },

        pages: [
            { name: 'home', url: '/' },

            // Every area the navigation offers, with nothing in any of them
            { name: 'resources', url: '/resources' },
            { name: 'groups', url: '/groups' },
            { name: 'events', url: '/events' },
            { name: 'knowledge-base', url: '/kb' },
            { name: 'answers', url: '/answers' },
            { name: 'blog', url: '/blog' },
            { name: 'courses', url: '/courses' },
            { name: 'citations', url: '/citations' },
            { name: 'collections', url: '/collections' },
            { name: 'publications', url: '/publications' },
            { name: 'projects', url: '/projects' },
            { name: 'wish-list', url: '/wishlist' },
            { name: 'polls', url: '/poll' },
            { name: 'jobs', url: '/jobs' },
            { name: 'newsletters', url: '/newsletter' },
            { name: 'wiki', url: '/wiki' },
            { name: 'whats-new', url: '/whatsnew' },
            { name: 'tags', url: '/tags' },
            { name: 'members-directory', url: '/members' },
            { name: 'support', url: '/support' },
            { name: 'feedback', url: '/feedback' },

            { name: 'sign-in', url: '/login' },
            { name: 'search-empty', url: '/search?terms=nothing' },

            // The error page is a page too, and the one a template is least
            // likely to have been looked at on. 404 is the right answer here.
            { name: 'not-found', url: '/this-page-does-not-exist', expect: 404 },

            { name: 'register', url: '/register' },

            { name: 'admin-home', url: '/administrator/', as: 'admin' },
        ],
    },

    welcome: {
        port: '7500',
        palette: PALETTE_MERIDIAN,

        people: {
            admin: { username: 'admin', password: adminPassword },
        },
        pages: [
            { name: 'home', url: '/' },
            { name: 'about', url: '/about' },
            { name: 'cyberinfrastructure', url: '/aboutus/hubzero' },
            { name: 'contact', url: '/about/contact' },
            { name: 'terms', url: '/legal/terms' },
            { name: 'privacy', url: '/legal/privacy' },
            { name: 'copyright', url: '/aboutus/dmcapolicy' },

            // What the hub's own navigation offers, which is every area it
            // ships whether or not the sample data put anything in it. An
            // area with nothing in it should say so rather than look broken,
            // and that is the half of a template nobody designs.
            { name: 'resources', url: '/resources' },
            { name: 'groups', url: '/groups' },
            { name: 'events', url: '/events' },
            { name: 'knowledge-base', url: '/kb' },
            { name: 'answers', url: '/answers' },
            { name: 'blog', url: '/blog' },
            { name: 'forum', url: '/forum' },
            { name: 'courses', url: '/courses' },
            { name: 'citations', url: '/citations' },
            { name: 'collections', url: '/collections' },
            { name: 'whats-new', url: '/whatsnew' },
            { name: 'tags', url: '/tags' },
            { name: 'support', url: '/support' },
            { name: 'feedback', url: '/feedback' },

            { name: 'sign-in', url: '/login' },
            { name: 'register', url: '/register' },
            { name: 'search-empty', url: '/search?terms=nothing' },

            // The error page is a page too, and the one a template is least
            // likely to have been looked at on. 404 is the right answer here.
            { name: 'not-found', url: '/this-page-does-not-exist', expect: 404 },

            { name: 'admin-home', url: '/administrator/', as: 'admin' },
            { name: 'admin-templates', url: '/administrator/index.php?option=com_templates&view=styles', as: 'admin' },
        ],
    },
};

/**
 * One hub's catalogue, or nothing if it has none
 *
 * @param   string  hub  Which hub
 * @return  object  Its people and its pages
 */
export function hubFor(hub) {
    return hubs[hub] || null;
}

/**
 * Where a catalogue's pages actually are
 *
 * A catalogue is usually its own hub, and then this is the hub's own address.
 * It need not be: a template has to be judged against a hub with something in
 * it, and the hub a template is delivered on may have nothing. So an entry can
 * name another hub's host and port and ask for a template by style id, and the
 * site will render that one page in that template without the hub's own
 * default changing - the switch lasts one request and is not remembered.
 *
 * @param   string  hub   Which catalogue
 * @param   string  port  A port from the command line, which wins
 * @return  object  Its base address, and a way to address one of its pages
 */
export function addressFor(hub, port) {
    const catalogue = hubs[hub] || {};
    const host = catalogue.host || hub;
    const domain = process.env.HUB_DOMAIN || 'example.com';
    const base = `https://${host}.${domain}:${port || catalogue.port || '7600'}`;
    const style = catalogue.style;

    return {
        base,

        /**
         * One page's address, with the template asked for if there is one
         *
         * @param   string  path  A path, which may carry a query of its own
         * @return  string
         */
        url(path) {
            if (!style) {
                return base + path;
            }

            return base + path + (path.includes('?') ? '&' : '?')
                + 'templateStyle=' + style;
        },
    };
}

