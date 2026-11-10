<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2025 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Give a hub that has no navigation a navigation
 *
 * A hub installed without the sample data has one site menu item - Home - and
 * no menu module in any position a template renders. Every component is
 * installed and every route works, and none of them can be reached by
 * clicking: the hub is a set of pages with no way in.
 *
 * That is because the menu has always come from the sample data. This is the
 * same idea without the content.
 *
 * It is built the way the sample data builds it, in two parts, and the reason
 * is the shape of the URLs. A menu item's path is its ancestors' aliases
 * joined up, and the router uses that path - so a Resources item sitting under
 * a Discover group gives /discover/resources, while the component's own links
 * to its inner pages stay at /resources/browse. One page, two addresses, and
 * the odd one on the way in.
 *
 * So the routes are flat and live in a menutype of their own that nothing
 * renders, and the navigation is a second menutype of groups whose children
 * are aliases pointing at those routes. The address stays /resources wherever
 * the item is hung in the menu.
 *
 * A hub that already has a menu - because it took the sample data, or because
 * somebody built one - is left alone.
 **/
class Migration20260913090000MainMenu extends Base
{
    /**
     * Every front-end address this hub should answer, flat.
     *
     * Nothing renders this menutype. It exists so that each component has one
     * menu item to be routed through, at the top level, where its path is just
     * its own alias.
     *
     * Every one of these was opened as a guest on a hub with nothing in it
     * before being put in the list.
     *
     * @var  array
     **/
    protected $routes = [
        'resources'    => ['Resources', 'com_resources'],
        'tools'        => ['Tools', 'com_tools'],
        'publications' => ['Publications', 'com_publications'],
        'citations'    => ['Citations', 'com_citations'],
        'collections'  => ['Collections', 'com_collections'],
        'tags'         => ['Tags', 'com_tags'],
        'whatsnew'     => ['What\'s New', 'com_whatsnew'],
        'groups'       => ['Groups', 'com_groups'],
        'members'      => ['Members', 'com_members'],
        'forum'        => ['Forum', 'com_forum'],
        'answers'      => ['Questions &amp; Answers', 'com_answers'],
        'blog'         => ['Blog', 'com_blog'],
        'projects'     => ['Projects', 'com_projects'],
        'courses'      => ['Courses', 'com_courses'],
        'kb'           => ['Knowledge Base', 'com_kb'],
        'wiki'         => ['Wiki', 'com_wiki'],
        'events'       => ['Events', 'com_events'],
        'support'      => ['Support', 'com_support'],
        'feedback'     => ['Feedback', 'com_feedback'],
        'wishlist'     => ['Wish List', 'com_wishlist'],
        'newsletter'   => ['Newsletter', 'com_newsletter'],
        'poll'         => ['Polls', 'com_poll'],
        'jobs'         => ['Jobs', 'com_jobs'],
        'usage'        => ['Usage', 'com_usage'],
        'developer'    => ['Developers', 'com_developer'],

    ];

    /**
     * The navigation: each group, and which routes hang under it.
     *
     * @var  array
     **/
    protected $groups = [
        'discover' => [
            'title' => 'Discover',
            'items' => [
                'resources',
                'tools',
                'publications',
                'citations',
                'collections',
                'tags',
                'whatsnew',
            ],
        ],
        'community' => [
            'title' => 'Community',
            'items' => [
                'groups',
                'members',
                'forum',
                'answers',
                'blog',
                'projects',
            ],
        ],
        'learn' => [
            'title' => 'Learn',
            'items' => ['courses', 'kb', 'wiki', 'events'],
        ],
        'support' => [
            'title' => 'Support',
            'items' => ['support', 'feedback', 'wishlist'],
        ],
        'about' => [
            'title' => 'About',
            'items' => ['newsletter', 'poll', 'jobs', 'usage', 'developer'],
        ],
    ];

    /**
     * Up
     **/
    public function up()
    {
        if (!$this->db->tableExists('#__menu') || !$this->db->tableExists('#__modules')) {
            return;
        }

        if ($this->hasNavigation()) {
            return;
        }

        $root = $this->db->getQuery(true)
            ->select('id')
            ->from('#__menu')
            ->where('level', '=', 0)
            ->value('id');

        if (!$root) {
            return;
        }

        $this->addMenuTypes();

        $ids = $this->addRoutes((int) $root);

        $this->addNavigation((int) $root, $ids);

        $this->rebuild((int) $root);

        $this->placeModule();

        $this->openForum();

        $this->log(
            'This hub had no navigation, so it has been given one: five groups'
            . ' covering every part of the front end.',
        );
    }

    /**
     * Let a visitor read the forum
     *
     * com_forum is the only component that gates its whole front end on
     * core.access, and nothing grants that: not the base data, not the sample
     * data, not the component's own installation. So a hub's forum answers
     * every visitor with a redirect to the login form, and a menu item
     * pointing at it is a menu item pointing at a locked door.
     *
     * Only on a hub this migration has just given a navigation to, and only if
     * nobody has set core.access already. Turning it on everywhere would be
     * changing who can read what on hubs that have been running for years, and
     * that is not a migration's decision to make.
     **/
    protected function openForum()
    {
        if (!$this->db->tableExists('#__assets')) {
            return;
        }

        $asset = $this->db->getQuery(true)
            ->select('id')
            ->select('rules')
            ->from('#__assets')
            ->where('name', '=', 'com_forum')
            ->fetch('row');

        if (!$asset) {
            return;
        }

        $rules = json_decode($asset->rules, true);

        if (!is_array($rules) || array_key_exists('core.access', $rules)) {
            return;
        }

        // 1 is Public, and it is Public by id because a hub may have renamed
        // the group but cannot renumber it.
        $rules['core.access'] = ['1' => 1];

        $this->db->getQuery(true)
            ->update('#__assets')
            ->set(['rules' => json_encode($rules)])
            ->where('id', '=', (int) $asset->id)
            ->execute();

        $this->log('The forum is readable by visitors, which nothing had granted.');
    }

    /**
     * Whether this hub already has a way in
     *
     * Home does not count. It is created by the installer whatever else the
     * hub declined, and a menu of one item that is the page you are already
     * on is not navigation.
     *
     * @return  bool
     **/
    protected function hasNavigation()
    {
        $items = $this->db->getQuery(true)
            ->select('COUNT(*)')
            ->from('#__menu')
            ->where('client_id', '=', 0)
            ->where('level', '>', 0)
            ->where('home', '=', 0)
            ->value('COUNT(*)');

        return $items > 0;
    }

    /**
     * The two menutypes, if they are not there already
     **/
    protected function addMenuTypes()
    {
        if (!$this->db->tableExists('#__menu_types')) {
            return;
        }

        $wanted = [
            'mainmenu' => [
                'Main Menu',
                'display',
                'The main menu for the site',
            ],
            'components' => [
                'Components',
                'component',
                'One entry per component, so every component has an address and a page'
                . ' of its own. Generated; not edited here.',
            ],
        ];

        $typed = $this->db->tableHasField('#__menu_types', 'type');

        foreach ($wanted as $type => $about) {
            list($title, $kind, $description) = $about;

            $exists = $this->db->getQuery(true)
                ->select('id')
                ->from('#__menu_types')
                ->where('menutype', '=', $type)
                ->value('id');

            if ($exists) {
                continue;
            }

            $values = [
                'menutype'    => $type,
                'title'       => $title,
                'description' => $description,
            ];

            if ($typed) {
                $values['type'] = $kind;
            }

            $this->db->getQuery(true)
                ->insert('#__menu_types')
                ->values($values)
                ->execute();
        }
    }

    /**
     * The flat route for each component
     *
     * @param   integer  $root
     * @return  array    Route alias to the menu item id that answers it
     **/
    protected function addRoutes($root)
    {
        $ids = [];
        $ordering = 1;

        foreach ($this->routes as $alias => $route) {
            $link = isset($route[2]) ? $route[2] : 'index.php?option=' . $route[1];

            // No extensions row means the component is not installed here,
            // and a route row with component_id 0 would pass the menu's
            // extension check as though it named no component at all
            if (!($componentId = $this->componentId($route[1]))) {
                continue;
            }

            $ids[$alias] = $this->addItem([
                'menutype'     => 'components',
                'title'        => $route[0],
                'alias'        => $alias,
                'path'         => $alias,
                'link'         => $link,
                'type'         => 'component',
                'parent_id'    => $root,
                'level'        => 1,
                'ordering'     => $ordering++,
                'component_id' => $componentId,
            ]);
        }

        return $ids;
    }

    /**
     * The groups, and an alias under each pointing at a route
     *
     * @param   integer  $root
     * @param   array    $ids
     **/
    protected function addNavigation($root, $ids)
    {
        $ordering = 1;

        foreach ($this->groups as $alias => $group) {
            // The group itself. A url item rather than a component one: with
            // the module's disclosure menu it is drawn as the button that
            // opens its own submenu, so it names a set of pages rather than
            // pretending to be one of them.
            // The alias is prefixed because both menutypes hang off the same
            // root and the table is unique on (client_id, parent_id, alias,
            // language): a Support group and a support route are two rows with
            // one alias under one parent. A group's alias appears in no URL -
            // its children are aliases and route through their target - so the
            // prefix costs nothing.
            $parent = $this->addItem([
                'menutype'  => 'mainmenu',
                'title'     => $group['title'],
                'alias'     => 'nav-' . $alias,
                'path'      => 'nav-' . $alias,
                'link'      => '',
                'type'      => 'url',
                'parent_id' => $root,
                'level'     => 1,
                'ordering'  => $ordering++,
            ]);

            $under = 1;

            foreach ($group['items'] as $item) {
                if (!isset($ids[$item])) {
                    continue;
                }

                $this->addItem([
                    'menutype'  => 'mainmenu',
                    'title'     => $this->routes[$item][0],
                    'alias'     => $item,
                    'path'      => 'nav-' . $alias . '/' . $item,
                    'link'      => 'index.php?Itemid=',
                    'type'      => 'alias',
                    'parent_id' => $parent,
                    'level'     => 2,
                    'ordering'  => $under++,
                    'params'    => json_encode(['aliasoptions' => (string) $ids[$item]]),
                ]);
            }
        }
    }

    /**
     * One menu item, at lft and rgt of nothing until the tree is computed
     *
     * @param   array  $item
     * @return  integer  Its id
     **/
    protected function addItem($item)
    {
        $row = array_merge([
            'menutype'          => 'mainmenu',
            'note'              => '',
            'published'         => 1,
            'component_id'      => 0,
            'checked_out'       => 0,
            'checked_out_time'  => '0000-00-00 00:00:00',
            'browserNav'        => 0,
            'access'            => 1,
            'img'               => '',
            'template_style_id' => 0,
            'params'            => '{}',
            'lft'               => 0,
            'rgt'               => 0,
            'home'              => 0,
            'language'          => '*',
            'client_id'         => 0,
        ], $item);

        $this->db->getQuery(true)
            ->insert('#__menu')
            ->values($row)
            ->execute();

        return (int) $this->db->insertid();
    }

    /**
     * The extension id a menu item points at, or zero
     *
     * @param   string  $option
     * @return  integer
     **/
    protected function componentId($option)
    {
        return (int) $this->db->getQuery(true)
            ->select('extension_id')
            ->from('#__extensions')
            ->where('type', '=', 'component')
            ->where('element', '=', $option)
            ->value('extension_id');
    }

    /**
     * Number the tree from the parent links, depth first
     *
     * The rows go in knowing only who their parent is. This walks them and
     * writes lft and rgt, which is what everything that reads a menu actually
     * orders by - done here rather than by hand in the rows, because a nested
     * set written out by hand is a nested set that is wrong.
     *
     * @param   integer  $parent
     * @param   integer  $left
     * @return  integer  The right-hand number after this subtree
     **/
    protected function rebuild($parent, $left = 0)
    {
        $children = $this->db->getQuery(true)
            ->select('id')
            ->from('#__menu')
            ->where('parent_id', '=', (int) $parent)
            ->order('ordering', 'asc')
            ->order('id', 'asc')
            ->fetch();

        $right = $left + 1;

        foreach ($children as $child) {
            $right = $this->rebuild((int) $child->id, $right);
        }

        $this->db->getQuery(true)
            ->update('#__menu')
            ->set(['lft' => (int) $left, 'rgt' => (int) $right])
            ->where('id', '=', (int) $parent)
            ->execute();

        return $right + 1;
    }

    /**
     * Put a menu module where the templates look for it
     *
     * user3 is the position every HUBzero site template renders its primary
     * navigation into. The module the base data ships sits in position-7,
     * which is Joomla's own and which no template here has ever drawn.
     **/
    protected function placeModule()
    {
        // toplevelLinks draws a top-level item that has pages under it as the
        // button that opens them rather than as a link. Its own label says the
        // opposite; the behaviour is what the templates are written against,
        // so it is the label that is wrong and not this.
        $params = json_encode([
            'menutype'        => 'mainmenu',
            'startLevel'      => '1',
            'endLevel'        => '0',
            'showAllChildren' => '1',
            'disclosureMenu'  => '1',
            'toplevelLinks'   => '1',
            'layout'          => '',
            'cache'           => '0',
        ]);

        $id = $this->db->getQuery(true)
            ->select('id')
            ->from('#__modules')
            ->where('module', '=', 'mod_menu')
            ->where('client_id', '=', 0)
            ->order('id', 'asc')
            ->value('id');

        if ($id) {
            $this->db->getQuery(true)
                ->update('#__modules')
                ->set([
                    'position'  => 'user3',
                    'published' => 1,
                    'showtitle' => 0,
                    'params'    => $params,
                ])
                ->where('id', '=', (int) $id)
                ->execute();

            return;
        }

        // No menu module at all, which the base data does ship - but a hub
        // that deleted it should still come out of this with a navigation.
        $this->db->getQuery(true)
            ->insert('#__modules')
            ->values([
                'title'            => 'Main Menu',
                'note'             => '',
                'content'          => '',
                'ordering'         => 1,
                'position'         => 'user3',
                'checked_out'      => 0,
                'checked_out_time' => '0000-00-00 00:00:00',
                'publish_up'       => '0000-00-00 00:00:00',
                'publish_down'     => '0000-00-00 00:00:00',
                'published'        => 1,
                'module'           => 'mod_menu',
                'access'           => 1,
                'showtitle'        => 0,
                'params'           => $params,
                'client_id'        => 0,
                'language'         => '*',
            ])
            ->execute();

        // A module shows on the pages named here, and nought means all of
        // them. Without a row the module is published and drawn nowhere.
        $this->db->getQuery(true)
            ->insert('#__modules_menu')
            ->values([
                'moduleid' => (int) $this->db->insertid(),
                'menuid'   => 0,
            ])
            ->execute();
    }

    /**
     * Down
     *
     * Only what up() wrote.
     **/
    public function down()
    {
        if (!$this->db->tableExists('#__menu')) {
            return;
        }

        foreach (array_keys($this->groups) as $alias) {
            $id = $this->db->getQuery(true)
                ->select('id')
                ->from('#__menu')
                ->where('menutype', '=', 'mainmenu')
                ->where('client_id', '=', 0)
                ->where('level', '=', 1)
                ->where('alias', '=', 'nav-' . $alias)
                ->value('id');

            if (!$id) {
                continue;
            }

            $this->db->getQuery(true)
                ->delete('#__menu')
                ->where('parent_id', '=', (int) $id)
                ->execute();

            $this->db->getQuery(true)
                ->delete('#__menu')
                ->where('id', '=', (int) $id)
                ->execute();
        }

        foreach (array_keys($this->routes) as $alias) {
            $this->db->getQuery(true)
                ->delete('#__menu')
                ->where('menutype', '=', 'components')
                ->where('client_id', '=', 0)
                ->where('alias', '=', $alias)
                ->execute();
        }
    }
}
