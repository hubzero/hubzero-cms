<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2025 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Menu;

/**
 * The address a component answers at
 *
 * Every site component has one entry in the component menu, and that entry is
 * what gives it an address and an Itemid - and with the Itemid, the modules
 * assigned to that page, its template style, and its place in a breadcrumb.
 *
 * Three things make one: the install macro when a component is installed, the
 * "muse routes fix" command when a hub has fallen out of step, and the router
 * when a developer has just written a component and has not written a migration
 * for it yet. They agree because they are all this class.
 */
class ComponentRoute
{
    /**
     * Components with nothing on the site to answer with
     *
     * Not a judgement about which components are interesting - that kind of
     * list ages badly, and a component that is machinery today may grow a page
     * tomorrow with nobody remembering to take it off. Only the ones with no
     * site code to run at all:
     *
     *   com_messages   a language file in site/, and nothing else
     *   com_media      one class and a helper
     *   com_system     a router and a class; routing machinery
     *
     * Everything else with a site directory gets an address, including the
     * endpoints something talks to rather than a person - com_cron answering
     * JSON, com_oaipmh answering XML, com_oauth and com_saml. An address costs
     * nothing there: the router already resolves /cron by component name, so
     * the entry adds an Itemid and takes a name that nothing else wanted, and
     * the day one of them grows a page it already has somewhere to put it.
     *
     * Note that having no site/views directory is not the test. com_dataviewer
     * has none and is a full site component - Controller.php, View/Gallery.php,
     * View/Spreadsheet.php, its own router and a tree of assets - written in a
     * namespaced layout rather than the old one.
     *
     * @var  array
     */
    protected static $notPages = array(
        'com_media',
        'com_messages',
        'com_system',
    );

    /**
     * Database connection
     *
     * @var  object
     */
    protected $db;

    /**
     * Somewhere to say what happened, or null to say nothing
     *
     * @var  callable|null
     */
    protected $log;

    /**
     * Constructor
     *
     * @param   object    $db   Database connection
     * @param   callable  $log  Called with one string per thing done
     * @return  void
     */
    public function __construct($db, $log = null)
    {
        $this->db  = $db;
        $this->log = $log;
    }

    /**
     * The menu the component routes live in, making it if it is not there
     *
     * A hub upgraded from before menus had a type has no column to look in, so
     * the name is the fallback. A hub that has neither gets one made.
     *
     * @return  string|false
     */
    public function menutype()
    {
        if (!$this->db->tableExists('#__menu_types')) {
            return false;
        }

        $typed = $this->db->tableHasField('#__menu_types', 'type');

        if ($typed) {
            $found = $this->db->getQuery(true)
                ->select('menutype')
                ->from('#__menu_types')
                ->whereEquals('type', 'component')
                ->value('menutype');

            if ($found) {
                return $found;
            }
        }

        $found = $this->db->getQuery(true)
            ->select('menutype')
            ->from('#__menu_types')
            ->whereEquals('menutype', 'components')
            ->value('menutype');

        if ($found) {
            return $found;
        }

        $values = array(
            'menutype'    => 'components',
            'title'       => 'Components',
            'description' => 'One entry per component, so every component has an address'
                . ' and a page of its own. Generated; not edited here.',
        );

        if ($typed) {
            $values['type'] = 'component';
        }

        $this->db->getQuery()
            ->insert('#__menu_types')
            ->values($values)
            ->execute();

        $this->say('Made the components menu');

        return 'components';
    }

    /**
     * The id of the menu root
     *
     * @return  integer
     */
    public function root()
    {
        $root = $this->db->getQuery(true)
            ->select('id')
            ->from('#__menu')
            ->whereEquals('parent_id', 0)
            ->value('id');

        return $root ? (int) $root : 1;
    }

    /**
     * Is anything already answering at that address
     *
     * By address rather than by alias and parent, because an item can now
     * declare the address it answers at regardless of where it sits - so a hub
     * that put a component in its own menu at /xyz has already given it one,
     * and "muse routes check" reads the same column.
     *
     * @param   string  $option  com_xyz
     * @return  mixed   The menu item id, or false
     */
    public function exists($option)
    {
        $found = $this->db->getQuery(true)
            ->select('id')
            ->from('#__menu')
            ->whereEquals('client_id', 0)
            ->whereEquals('path', substr($option, 4))
            ->value('id');

        return $found ? (int) $found : false;
    }

    /**
     * Whether a component is one that could have an address at all
     *
     * @param   string  $option  com_xyz
     * @return  boolean
     */
    public function routable($option)
    {
        if (!preg_match('/^com_[a-z0-9]+$/', (string) $option)) {
            return false;
        }

        if (in_array($option, self::$notPages, true)) {
            return false;
        }

        return is_dir(PATH_CORE . '/components/' . $option . '/site');
    }

    /**
     * The components that should never be given an address
     *
     * @return  array
     */
    public static function notPages()
    {
        return self::$notPages;
    }

    /**
     * Give a component its address
     *
     * The entry is published because the address exists. Whether it is used is
     * a different question, answered by whether the component is switched on,
     * and the menu reads that off #__extensions every time it loads. Mirroring
     * it here as well would only give the two a chance to disagree: an entry
     * made while the component was off would stay off after somebody turned the
     * component on, and the component would run with no Itemid and nothing to
     * say why.
     *
     * @param   string   $option       com_xyz
     * @param   integer  $componentId  Its row in #__extensions
     * @return  boolean  True if there is now an address, false if there cannot be
     */
    public function create($option, $componentId = 0)
    {
        if (!$this->db->tableExists('#__menu') || !$this->db->tableExists('#__menu_types')) {
            return false;
        }

        if ($this->exists($option)) {
            return true;
        }

        $menutype = $this->menutype();

        if (!$menutype) {
            return false;
        }

        $alias = substr($option, 4);

        $this->db->getQuery()
            ->insert('#__menu')
            ->values(array(
                'menutype'          => $menutype,
                'title'             => ucfirst($alias),
                'alias'             => $alias,
                'note'              => '',
                'path'              => $alias,
                'link'              => 'index.php?option=' . $option,
                'type'              => 'component',
                'published'         => 1,
                'parent_id'         => $this->root(),
                'level'             => 1,
                'component_id'      => (int) $componentId,
                'ordering'          => 0,
                'checked_out'       => 0,
                'browserNav'        => 0,
                'access'            => 1,
                'img'               => '',
                'template_style_id' => 0,
                'params'            => '',
                'lft'               => 0,
                'rgt'               => 0,
                'home'              => 0,
                'language'          => '*',
                'client_id'         => 0
            ))
            ->execute();

        $this->say('Added the site route /' . $alias);

        $this->rebuild();

        return true;
    }

    /**
     * The component's row in #__extensions, if it has one
     *
     * @param   string  $option  com_xyz
     * @return  mixed   The row, with extension_id and enabled, or false
     */
    public function extension($option)
    {
        $rows = $this->db->getQuery(true)
            ->select('extension_id')
            ->select('enabled')
            ->from('#__extensions')
            ->whereEquals('type', 'component')
            ->whereEquals('element', $option)
            ->fetch();

        foreach ($rows as $row) {
            return $row;
        }

        return false;
    }

    /**
     * Walk the menu putting lft, rgt, level and path back in order
     *
     * @param   integer  $parentId  Where to start, or null for the root
     * @param   integer  $leftId    The left value of that node
     * @param   integer  $level     Its depth
     * @param   string   $path      The path so far
     * @return  mixed    The right value of the node, or false on failure
     */
    public function rebuild($parentId = null, $leftId = 0, $level = 0, $path = '')
    {
        if ($parentId === null) {
            $parentId = $this->root();
        }

        $columns = $this->db->getQuery(true)
            ->select('id')
            ->select('alias');

        // A hub that has not run the migration yet has no such column
        $declares = $this->db->tableHasField('#__menu', 'route');

        if ($declares) {
            $columns->select('route');
        }

        $children = $columns
            ->from('#__menu')
            ->whereEquals('parent_id', (int) $parentId)
            ->order('parent_id', 'asc')
            ->order('ordering', 'asc')
            ->order('lft', 'asc')
            ->fetch();

        $rightId = $leftId + 1;

        foreach ($children as $node) {
            $id    = is_object($node) ? $node->id : $node['id'];
            $alias = is_object($node) ? $node->alias : $node['alias'];
            $route = $declares ? (is_object($node) ? $node->route : $node['route']) : '';

            // An item that declares its address keeps it through a rebuild, or
            // installing anything at all would quietly undo it.
            $childPath = $route
                ? trim(str_replace(chr(92), '/', $route), '/')
                : $path . (empty($path) ? '' : '/') . $alias;

            $rightId = $this->rebuild((int) $id, $rightId, $level + 1, $childPath);

            if ($rightId === false) {
                return false;
            }
        }

        $this->db->getQuery()
            ->update('#__menu')
            ->set(array(
                'lft'   => (int) $leftId,
                'rgt'   => (int) $rightId,
                'level' => (int) $level,
                'path'  => $path
            ))
            ->whereEquals('id', (int) $parentId)
            ->execute();

        return $rightId + 1;
    }

    /**
     * Say what happened, if anybody is listening
     *
     * @param   string  $message  What happened
     * @return  void
     */
    protected function say($message)
    {
        if (is_callable($this->log)) {
            call_user_func($this->log, $message);
        }
    }
}
