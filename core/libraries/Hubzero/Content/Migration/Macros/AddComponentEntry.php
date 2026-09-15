<?php

/**
 * @package    framework
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Content\Migration\Macros;

use Hubzero\Content\Migration\Macro;
use Hubzero\Access\Asset;

/**
 * Migration macro to add a component entry
 **/
class AddComponentEntry extends Macro
{
    /**
     * Add, as needed, the component to the appropriate table, depending on the CMS version
     *
     * @param   string  $name            Component name
     * @param   string  $option          String) com_xyz
     * @param   int     $enabled         Whether or not the component should be enabled
     * @param   string  $params          Component params (if already known)
     * @param   bool    $createMenuItem  Create an admin menu item for this component
     * @param   bool    $createRoute     Create the site route for this component. A
     *                                   component with no site/ directory has nothing
     *                                   to route to and is left out by default.
     * @return  bool
     **/
    public function __invoke(
        $name,
        $option = null,
        $enabled = 1,
        $params = '',
        $createMenuItem = true,
        $createRoute = null
    )
    {
        if (!$this->db->tableExists('#__extensions')) {
            $this->log(sprintf('Required table not found for adding component "%s"', $name), 'warning');

            return false;
        }

        if (is_null($option)) {
            $option = 'com_' . strtolower($name);
        }
        $name = $option;

        // First, make sure it isn't already there
        $query = $this->db->getQuery()
            ->select('extension_id')
            ->from('#__extensions')
            ->whereEquals('name', $option)
            ->toString();

        $this->db->setQuery($query);
        if ($this->db->loadResult()) {
            $component_id = $this->db->loadResult();

            $this->log(sprintf('Extension entry already exists for component "%s"', $name));
        } else {
            $ordering = 0;

            if (!empty($params) && is_array($params)) {
                $params = json_encode($params);
            }

            $query = $this->db->getQuery()
                ->insert('#__extensions')
                ->values(array(
                    'name'           => $name,
                    'type'           => 'component',
                    'element'        => $option,
                    'folder'         => '',
                    'client_id'      => 1,
                    'enabled'        => $enabled,
                    'access'         => 1,
                    'protected'      => 0,
                    'manifest_cache' => '',
                    'params'         => $params,
                    'custom_data'    => '',
                    'system_data'    => '',
                    'checked_out'    => 0,
                    'ordering'       => $ordering,
                    'state'          => 0
                ))
                ->toString();

            $this->db->setQuery($query);
            $this->db->query();

            $component_id = $this->db->insertId();

            $this->log(sprintf('Added extension entry for component "%s"', $name));
        }

        if ($this->db->tableExists('#__assets')) {
            // Secondly, add asset entry if not yet created
            $query = $this->db->getQuery()
                ->select('id')
                ->from('#__assets')
                ->whereEquals('name', $option)
                ->toString();
            $this->db->setQuery($query);
            if (!$this->db->loadResult()) {
                // Build default ruleset
                $defaulRules = array(
                    "core.admin"      => array(
                        "7" => 1
                    ),
                    "core.manage"     => array(
                        "6" => 1
                    ),
                    "core.create"     => array(),
                    "core.delete"     => array(),
                    "core.edit"       => array(),
                    "core.edit.state" => array()
                );

                // Register the component container just under root in the assets table
                $asset = Asset::blank();
                $asset->set('name', $option);
                $asset->set('parent_id', 1);
                $asset->set('rules', json_encode($defaulRules));
                $asset->set('title', $option);
                $asset->saveAsChildOf(1);

                $this->log(sprintf('Added asset entry for component "%s"', $name));
            }
        }

        if ($createMenuItem && $this->db->tableExists('#__menu')) {
            $alias = substr($option, 4);

            // Check for an admin menu entry...if it's not there, create it.
            // A menu item is unique on its alias within a client, parent and
            // language, and that is what to look for: the same entry has been
            // filed under more than one menutype and title over the years.
            $existing = $this->db->getQuery(true)
                ->select('id')
                ->from('#__menu')
                ->whereEquals('client_id', 1)
                ->whereEquals('parent_id', 1)
                ->whereEquals('alias', $alias)
                ->value('id');

            if ($existing) {
                return true;
            }

            $query = $this->db->getQuery()
                ->insert('#__menu')
                ->values(array(
                    'menutype'          => 'main',
                    'title'             => $option,
                    'alias'             => $alias,
                    'note'              => '',
                    'path'              => $alias,
                    'link'              => 'index.php?option=' . $option,
                    'type'              => 'component',
                    'published'         => $enabled,
                    'parent_id'         => 1,
                    'level'             => 1,
                    'component_id'      => $component_id,
                    'ordering'          => 0,
                    'checked_out'       => 0,
                    'browserNav'        => 0,
                    'access'            => 0,
                    'img'               => '',
                    'template_style_id' => 0,
                    'params'            => '',
                    'lft'               => 0,
                    'rgt'               => 0,
                    'home'              => 0,
                    'language'          => '*',
                    'client_id'         => 1
                ))
                ->toString();
            $this->db->setQuery($query);
            $this->db->query();

            $this->log(sprintf('Added menu entry for component "%s"', $name));

            // Rebuild lft/rgt
            $this->rebuildMenu();
        }

        $this->addSiteRoute($option, $component_id, $enabled, $createRoute);

        return true;
    }

    /**
     * Give the component an address on the site, and an Itemid with it
     *
     * The router matches an incoming URL against #__menu.path, so a component
     * with no entry either does not route at all or routes by name with no
     * Itemid - and an Itemid is what modules and a template style are assigned
     * to. One flat entry per component in the component menu is what makes
     * /resources a page rather than a fallback.
     *
     * The alias is the option without its com_ prefix, which is also the rule
     * the router's own fallback uses, so the two agree by construction.
     *
     * @param   string    $option        com_xyz
     * @param   int       $component_id  Its extension id
     * @param   int       $enabled       Whether the component is on
     * @param   bool|null $wanted        Force it on or off; decided by the files if null
     * @return  bool
     */
    protected function addSiteRoute($option, $component_id, $enabled, $wanted = null)
    {
        if (!$this->db->tableExists('#__menu') || !$this->db->tableExists('#__menu_types')) {
            return false;
        }

        $alias = substr($option, 4);

        if ($wanted === null) {
            // Nothing to route to without a site half
            $wanted = is_dir(PATH_CORE . '/components/' . $option . '/site');
        }

        if (!$wanted) {
            return false;
        }

        $menutype = $this->componentMenu();

        if (!$menutype) {
            return false;
        }

        $root = $this->db->getQuery(true)
            ->select('id')
            ->from('#__menu')
            ->whereEquals('parent_id', 0)
            ->value('id');

        $root = $root ? (int) $root : 1;

        // Unique on client, parent, alias and language, which is what to look
        // for: the same route may have been filed under another menutype.
        $existing = $this->db->getQuery(true)
            ->select('id')
            ->from('#__menu')
            ->whereEquals('client_id', 0)
            ->whereEquals('parent_id', $root)
            ->whereEquals('alias', $alias)
            ->value('id');

        if ($existing) {
            return true;
        }

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
                'published'         => $enabled,
                'parent_id'         => $root,
                'level'             => 1,
                'component_id'      => $component_id,
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

        $this->log(sprintf('Added the site route /%s', $alias));

        $this->rebuildMenu();

        return true;
    }

    /**
     * The menu the component routes live in, made if it is not there yet
     *
     * @return  string|null  Its menutype
     */
    protected function componentMenu()
    {
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

        $this->log('Made the components menu');

        return 'components';
    }

    /**
     * Method to recursively rebuild the whole nested set tree.
     *
     * @param   integer  $parentId  The root of the tree to rebuild.
     * @param   integer  $leftId    The left id to start with in building the tree.
     * @param   integer  $level     The level to assign to the current nodes.
     * @param   string   $path      The path to the current nodes.
     * @return  integer  1 + value of root rgt on success, false on failure
     */
    private function rebuildMenu($parentId = null, $leftId = 0, $level = 0, $path = '')
    {
        // If no parent is provided, try to find it.
        if ($parentId === null) {
            // Get the root item.
            $query = $this->db->getQuery()
                ->select('id')
                ->from('#__menu')
                ->whereEquals('parent_id', 0)
                ->toString();

            $this->db->setQuery($query);
            $parentId = $this->db->loadResult();

            if ($parentId === false) {
                return false;
            }
        }

        // Build the structure of the recursive query.
        $rebuild = $this->db->getQuery()
            ->select('id')
            ->select('alias')
            ->from('#__menu')
            ->whereEquals('parent_id', (int) $parentId)
            ->order('parent_id', 'asc')
            ->order('ordering', 'asc')
            ->order('lft', 'asc')
            ->toString();

        // Assemble the query to find all children of this node.
        $this->db->setQuery($rebuild);
        $children = $this->db->loadObjectList();

        // The right value of this node is the left value + 1
        $rightId = $leftId + 1;

        // execute this function recursively over all children
        foreach ($children as $node) {
            // $rightId is the current right value, which is incremented on recursion return.
            // Increment the level for the children.
            // Add this item's alias to the path (but avoid a leading /)
            $rightId = $this->rebuildMenu($node->id, $rightId, $level + 1, $path .
                (empty($path) ? '' : '/') .
                $node->alias);

            // If there is an update failure, return false to break out of the recursion.
            if ($rightId === false) {
                return false;
            }
        }

        // We've got the left value, and now that we've processed
        // the children of this node we also know the right value.
        $query = $this->db->getQuery()
            ->update('#__menu')
            ->set(array(
                'lft'   => (int) $leftId,
                'rgt'   => (int) $rightId,
                'level' => (int) $level,
                'path'  => $path
            ))
            ->whereEquals('id', (int) $parentId)
            ->toString();
        $this->db->setQuery($query);

        // If there is an update failure, return false to break out of the recursion.
        if (!$this->db->execute()) {
            return false;
        }

        // Return the right value of this node + 1.
        return $rightId + 1;
    }
}
