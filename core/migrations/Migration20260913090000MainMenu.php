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
    protected $routes = array(
        'resources'    => array('Resources', 'com_resources'),
        'tools'        => array('Tools', 'com_tools'),
        'publications' => array('Publications', 'com_publications'),
        'citations'    => array('Citations', 'com_citations'),
        'collections'  => array('Collections', 'com_collections'),
        'tags'         => array('Tags', 'com_tags'),
        'whatsnew'     => array('What\'s New', 'com_whatsnew'),
        'groups'       => array('Groups', 'com_groups'),
        'members'      => array('Members', 'com_members'),
        'forum'        => array('Forum', 'com_forum'),
        'answers'      => array('Questions &amp; Answers', 'com_answers'),
        'blog'         => array('Blog', 'com_blog'),
        'projects'     => array('Projects', 'com_projects'),
        'courses'      => array('Courses', 'com_courses'),
        'kb'           => array('Knowledge Base', 'com_kb'),
        'wiki'         => array('Wiki', 'com_wiki'),
        'events'       => array('Events', 'com_events'),
        'support'      => array('Support', 'com_support'),
        'feedback'     => array('Feedback', 'com_feedback'),
        'wishlist'     => array('Wish List', 'com_wishlist'),
        'newsletter'   => array('Newsletter', 'com_newsletter'),
        'poll'         => array('Polls', 'com_poll'),
        'jobs'         => array('Jobs', 'com_jobs'),
        'usage'        => array('Usage', 'com_usage'),
        'developer'    => array('Developers', 'com_developer'),

        // Not in the navigation - the header offers these - but a hub
        // advertises /register everywhere and without a route it answers 404.
        'login'        => array('Login', 'com_users', 'index.php?option=com_users&view=login'),
        'logout'       => array('Logout', 'com_users', 'index.php?option=com_users&view=logout'),
        'register'     => array(
            'Register',
            'com_members',
            'index.php?option=com_members&view=register&layout=create'
        ),
    );

    /**
     * The navigation: each group, and which routes hang under it.
     *
     * @var  array
     **/
    protected $groups = array(
        'discover' => array(
            'title' => 'Discover',
            'items' => array(
                'resources', 'tools', 'publications', 'citations',
                'collections', 'tags', 'whatsnew',
            ),
        ),
        'community' => array(
            'title' => 'Community',
            'items' => array(
                'groups', 'members', 'forum', 'answers', 'blog', 'projects',
            ),
        ),
        'learn' => array(
            'title' => 'Learn',
            'items' => array('courses', 'kb', 'wiki', 'events'),
        ),
        'support' => array(
            'title' => 'Support',
            'items' => array('support', 'feedback', 'wishlist'),
        ),
        'about' => array(
            'title' => 'About',
            'items' => array('newsletter', 'poll', 'jobs', 'usage', 'developer'),
        ),
    );

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

        $ids = $this->addRoutes((int)$root);

        $this->addNavigation((int)$root, $ids);

        $this->rebuild((int)$root);

        $this->placeModule();

        $this->openForum();

        $this->log(
            'This hub had no navigation, so it has been given one: five groups'
            . ' covering every part of the front end.'
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
        $rules['core.access'] = array('1' => 1);

        $this->db->getQuery(true)
            ->update('#__assets')
            ->set(array('rules' => json_encode($rules)))
            ->where('id', '=', (int)$asset->id)
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

        $wanted = array(
            'mainmenu' => 'Main Menu',
            'default'  => 'Default',
        );

        foreach ($wanted as $type => $title) {
            $exists = $this->db->getQuery(true)
                ->select('id')
                ->from('#__menu_types')
                ->where('menutype', '=', $type)
                ->value('id');

            if ($exists) {
                continue;
            }

            $this->db->getQuery(true)
                ->insert('#__menu_types')
                ->values(array(
                    'menutype'    => $type,
                    'title'       => $title,
                    'description' => '',
                ))
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
        $ids = array();
        $ordering = 1;

        foreach ($this->routes as $alias => $route) {
            $link = isset($route[2]) ? $route[2] : 'index.php?option=' . $route[1];

            $ids[$alias] = $this->addItem(array(
                'menutype'     => 'default',
                'title'        => $route[0],
                'alias'        => $alias,
                'path'         => $alias,
                'link'         => $link,
                'type'         => 'component',
                'parent_id'    => $root,
                'level'        => 1,
                'ordering'     => $ordering++,
                'component_id' => $this->componentId($route[1]),
            ));
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
            $parent = $this->addItem(array(
                'menutype'  => 'mainmenu',
                'title'     => $group['title'],
                'alias'     => 'nav-' . $alias,
                'path'      => 'nav-' . $alias,
                'link'      => '',
                'type'      => 'url',
                'parent_id' => $root,
                'level'     => 1,
                'ordering'  => $ordering++,
            ));

            $under = 1;

            foreach ($group['items'] as $item) {
                if (!isset($ids[$item])) {
                    continue;
                }

                $this->addItem(array(
                    'menutype'  => 'mainmenu',
                    'title'     => $this->routes[$item][0],
                    'alias'     => $item,
                    'path'      => 'nav-' . $alias . '/' . $item,
                    'link'      => 'index.php?Itemid=',
                    'type'      => 'alias',
                    'parent_id' => $parent,
                    'level'     => 2,
                    'ordering'  => $under++,
                    'params'    => json_encode(array('aliasoptions' => (string)$ids[$item])),
                ));
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
        $row = array_merge(array(
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
        ), $item);

        $this->db->getQuery(true)
            ->insert('#__menu')
            ->values($row)
            ->execute();

        return (int)$this->db->insertid();
    }

    /**
     * The extension id a menu item points at, or zero
     *
     * @param   string  $option
     * @return  integer
     **/
    protected function componentId($option)
    {
        return (int)$this->db->getQuery(true)
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
            ->where('parent_id', '=', (int)$parent)
            ->order('ordering', 'asc')
            ->order('id', 'asc')
            ->fetch();

        $right = $left + 1;

        foreach ($children as $child) {
            $right = $this->rebuild((int)$child->id, $right);
        }

        $this->db->getQuery(true)
            ->update('#__menu')
            ->set(array('lft' => (int)$left, 'rgt' => (int)$right))
            ->where('id', '=', (int)$parent)
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
        $params = json_encode(array(
            'menutype'        => 'mainmenu',
            'startLevel'      => '1',
            'endLevel'        => '0',
            'showAllChildren' => '1',
            'disclosureMenu'  => '1',
            'toplevelLinks'   => '1',
            'layout'          => '',
            'cache'           => '0',
        ));

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
                ->set(array(
                    'position'  => 'user3',
                    'published' => 1,
                    'showtitle' => 0,
                    'params'    => $params,
                ))
                ->where('id', '=', (int)$id)
                ->execute();

            return;
        }

        // No menu module at all, which the base data does ship - but a hub
        // that deleted it should still come out of this with a navigation.
        $this->db->getQuery(true)
            ->insert('#__modules')
            ->values(array(
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
            ))
            ->execute();

        // A module shows on the pages named here, and nought means all of
        // them. Without a row the module is published and drawn nowhere.
        $this->db->getQuery(true)
            ->insert('#__modules_menu')
            ->values(array(
                'moduleid' => (int)$this->db->insertid(),
                'menuid'   => 0,
            ))
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
                ->where('parent_id', '=', (int)$id)
                ->execute();

            $this->db->getQuery(true)
                ->delete('#__menu')
                ->where('id', '=', (int)$id)
                ->execute();
        }

        foreach (array_keys($this->routes) as $alias) {
            $this->db->getQuery(true)
                ->delete('#__menu')
                ->where('menutype', '=', 'default')
                ->where('client_id', '=', 0)
                ->where('alias', '=', $alias)
                ->execute();
        }
    }
}
