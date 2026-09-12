/**
 * What to photograph, as whom, and what to call it.
 *
 * One entry a picture. `as` names a persona from shoot.mjs; leaving it out
 * means a signed-out visitor, which is what most of a hub is for. `at` limits
 * an entry to one viewport where the other would say nothing.
 *
 * The names are what the documentation will reference, so they are written
 * for a reader rather than derived from the URL: a file called
 * group-calendar.png is findable and one called groups-fossil-ct-calendar.png
 * is not.
 */
export const pages = [
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
