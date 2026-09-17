<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Flavor;

use Hubzero\Content\Migration\Base as Migration;
use Hubzero\Template\Style;

/**
 * Pulls a flavor's levers, and says which are not where the flavor puts them
 *
 * Every lever is a switch on rows that are already there: an extension's
 * enabled flag, a parameter, a published state, a column. Nothing is added
 * and nothing is removed, so applying one flavor and then another leaves the
 * hub as the second describes it, whatever the first did.
 **/
class Applier
{
    /**
     * The database
     *
     * @var  object
     */
    protected $db;

    /**
     * Where to say what is being done: fn($message, $type)
     *
     * @var  callable|null
     */
    protected $log;

    /**
     * The migration helpers, for enabling and disabling extensions
     *
     * @var  Migration
     */
    protected $migration;

    /**
     * What the last check() found nothing to pull on: a row a flavor names
     * that this hub does not have. Not a difference - the hub is not in the
     * wrong state, there is no state - but worth knowing, since a mistyped
     * path looks exactly like this.
     *
     * @var  array
     */
    protected $notes = array();

    /**
     * Constructor
     *
     * @param   object         $db
     * @param   callable|null  $log  Takes a message and a type (info, warning, success)
     */
    public function __construct($db, $log = null)
    {
        $this->db        = $db;
        $this->log       = $log;
        $this->migration = new Migration($db);
    }

    /**
     * Pull every lever the flavor names
     *
     * @param   Flavor  $flavor
     * @return  void
     */
    public function apply(Flavor $flavor)
    {
        $this->applySwitches('component', $flavor->get('components', array()));
        $this->applySwitches('module', $flavor->get('modules', array()));
        $this->applyModuleItems($flavor->get('modules', array(), 'items') ?: array());
        $this->applySwitches('plugin', $flavor->get('plugins', array()));
        $this->applyTemplate($flavor->get('template'));
        $this->applyTiles($flavor->get('dashboard', null, 'tiles'));
        $this->applyStates(
            '#__categories',
            'published',
            $flavor->get('kb', array(), 'categories'),
            "`extension` = 'com_kb'",
            'knowledge base category'
        );
        $this->applyStates('#__kb_articles', 'state', $flavor->get('kb', array(), 'articles'), '', 'knowledge base article');
        $this->applyStates('#__content', 'state', $flavor->get('content', array(), 'articles'), '', 'article');
        $this->applyStates('#__menu', 'published', $flavor->get('menu', array(), 'items'), '`client_id` = 0', 'menu item', 'path');
        $this->applyResourceTypes($flavor->get('resource_types', array()));
    }

    /**
     * Every way the hub differs from the flavor; nothing when it matches
     *
     * @param   Flavor  $flavor
     * @return  array   Sentences
     */
    public function check(Flavor $flavor)
    {
        $found       = array();
        $this->notes = array();

        $this->checkSwitches($found, 'component', $flavor->get('components', array()));
        $this->checkSwitches($found, 'module', $flavor->get('modules', array()));
        $this->checkModuleItems($found, $flavor->get('modules', array(), 'items') ?: array());
        $this->checkSwitches($found, 'plugin', $flavor->get('plugins', array()));
        $this->checkTemplate($found, $flavor->get('template'));
        $this->checkTiles($found, $flavor->get('dashboard', null, 'tiles'));
        $this->checkStates(
            $found,
            '#__categories',
            'published',
            $flavor->get('kb', array(), 'categories'),
            "`extension` = 'com_kb'",
            'knowledge base category'
        );
        $this->checkStates($found, '#__kb_articles', 'state', $flavor->get('kb', array(), 'articles'), '', 'knowledge base article');
        $this->checkStates($found, '#__content', 'state', $flavor->get('content', array(), 'articles'), '', 'article');
        $this->checkStates(
            $found,
            '#__menu',
            'published',
            $flavor->get('menu', array(), 'items'),
            '`client_id` = 0',
            'menu item',
            'path'
        );
        $this->checkResourceTypes($found, $flavor->get('resource_types', array()));

        return $found;
    }

    /**
     * The rows the last check() found the flavor naming but the hub lacking
     *
     * @return  array  Sentences
     */
    public function notes()
    {
        return $this->notes;
    }

    // ------------------------------------------------------------ extensions

    /**
     * Enable, disable and set parameters on one kind of extension
     *
     * @param   string  $type    component, module or plugin
     * @param   array   $lever   enable, disable and params lists
     * @return  void
     */
    protected function applySwitches($type, array $lever)
    {
        foreach (array('enable', 'disable') as $way) {
            foreach (isset($lever[$way]) ? $lever[$way] : array() as $name) {
                if ($type == 'plugin') {
                    list($folder, $element) = $this->pluginParts($name);
                    $way == 'enable'
                        ? $this->migration->enablePlugin($folder, $element)
                        : $this->migration->disablePlugin($folder, $element);
                } elseif ($type == 'module') {
                    $way == 'enable' ? $this->migration->enableModule($name) : $this->migration->disableModule($name);
                } else {
                    $way == 'enable'
                        ? $this->migration->enableComponent($name)
                        : $this->migration->disableComponent($name);
                }

                $this->say(($way == 'enable' ? 'Enabling ' : 'Disabling ') . $type . ' ' . $name);
            }
        }

        foreach (isset($lever['params']) ? $lever['params'] : array() as $name => $params) {
            $this->setParams($type, $name, $params);
            $this->say('Setting ' . implode(', ', array_keys($params)) . ' on ' . $type . ' ' . $name);
        }
    }

    /**
     * Compare one kind of extension with the flavor
     *
     * @param   array   &$found
     * @param   string  $type
     * @param   array   $lever
     * @return  void
     */
    protected function checkSwitches(array &$found, $type, array $lever)
    {
        foreach (array('enable', 'disable') as $way) {
            foreach (isset($lever[$way]) ? $lever[$way] : array() as $name) {
                $enabled = $this->enabled($type, $name);

                if ($enabled === null) {
                    if ($way == 'enable') {
                        $found[] = "{$type} {$name} is not installed";
                    }
                } elseif ($enabled != ($way == 'enable')) {
                    $found[] = "{$type} {$name} is " . ($enabled ? 'enabled' : 'disabled')
                        . " (the flavor {$way}s it)";
                }
            }
        }

        foreach (isset($lever['params']) ? $lever['params'] : array() as $name => $params) {
            foreach ($this->currentParams($type, $name) as $where => $current) {
                foreach ($params as $key => $value) {
                    $now = isset($current[$key]) ? $current[$key] : null;

                    if ((string) $now !== (string) $value) {
                        $found[] = "{$type} {$name}: {$key} is " . $this->show($now) . " (the flavor sets " . $this->show($value) . ")"
                            . ($where !== '' ? " [{$where}]" : '');
                    }
                }
            }
        }
    }

    /**
     * Whether an extension is enabled: 1, 0, or null when it is not installed
     *
     * @param   string  $type
     * @param   string  $name
     * @return  int|null
     */
    protected function enabled($type, $name)
    {
        if (!$this->db->tableExists('#__extensions')) {
            return null;
        }

        $this->db->setQuery("SELECT `enabled` FROM `#__extensions` WHERE " . $this->extensionWhere($type, $name));
        $enabled = $this->db->loadResult();

        return ($enabled === null) ? null : (int) $enabled;
    }

    /**
     * The params of an extension, as arrays: one for a component or plugin,
     * one per instance for a module (keyed by the instance's title)
     *
     * @param   string  $type
     * @param   string  $name
     * @return  array
     */
    protected function currentParams($type, $name)
    {
        $out = array();

        if ($type == 'module') {
            if (!$this->db->tableExists('#__modules')) {
                return $out;
            }

            $this->db->setQuery(
                "SELECT `id`, `title`, `params` FROM `#__modules` WHERE `module` = " . $this->db->quote($name)
            );

            foreach ((array) $this->db->loadObjectList() as $row) {
                $out[$row->title . ' #' . $row->id] = $this->decode($row->params);
            }

            return $out;
        }

        if (!$this->db->tableExists('#__extensions')) {
            return $out;
        }

        $this->db->setQuery("SELECT `params` FROM `#__extensions` WHERE " . $this->extensionWhere($type, $name));
        $params = $this->db->loadResult();

        if ($params !== null) {
            $out[''] = $this->decode($params);
        }

        return $out;
    }

    /**
     * Merge parameters into an extension's, or into each instance's for a module
     *
     * @param   string  $type
     * @param   string  $name
     * @param   array   $params
     * @return  void
     */
    protected function setParams($type, $name, array $params)
    {
        if ($type == 'module') {
            if (!$this->db->tableExists('#__modules')) {
                return;
            }

            $this->db->setQuery(
                "SELECT `id`, `params` FROM `#__modules` WHERE `module` = " . $this->db->quote($name)
            );

            foreach ((array) $this->db->loadObjectList() as $row) {
                $merged = array_merge($this->decode($row->params), $params);

                $this->db->setQuery(
                    "UPDATE `#__modules` SET `params` = " . $this->db->quote(json_encode($merged))
                    . " WHERE `id` = " . (int) $row->id
                );
                $this->db->query();
            }

            return;
        }

        if (!$this->db->tableExists('#__extensions')) {
            return;
        }

        $where = $this->extensionWhere($type, $name);

        $this->db->setQuery("SELECT `params` FROM `#__extensions` WHERE " . $where);
        $current = $this->db->loadResult();

        if ($current === null) {
            $this->say("No {$type} {$name} to set parameters on", 'warning');
            return;
        }

        $merged = array_merge($this->decode($current), $params);

        $this->db->setQuery(
            "UPDATE `#__extensions` SET `params` = " . $this->db->quote(json_encode($merged)) . " WHERE " . $where
        );
        $this->db->query();
    }

    /**
     * The WHERE that picks one extension row
     *
     * @param   string  $type
     * @param   string  $name  For a plugin, folder/element
     * @return  string
     */
    protected function extensionWhere($type, $name)
    {
        if ($type == 'plugin') {
            list($folder, $element) = $this->pluginParts($name);

            return "`type` = 'plugin' AND `folder` = " . $this->db->quote($folder)
                . " AND `element` = " . $this->db->quote($element);
        }

        return "`type` = " . $this->db->quote($type) . " AND `element` = " . $this->db->quote($name);
    }

    /**
     * A plugin's folder and element from folder/element
     *
     * @param   string  $name
     * @return  array
     */
    protected function pluginParts($name)
    {
        $parts = explode('/', $name, 2);

        return array($parts[0], isset($parts[1]) ? $parts[1] : '');
    }

    // ------------------------------------------------------- module instances

    /**
     * Publish or hide module instances, by id or by title
     *
     * A number names one instance; anything else is a title and names every
     * instance with it, since the shipped data places the same module in
     * more than one template's positions under one title.
     *
     * @param   array  $items  id-or-title => state
     * @return  void
     */
    protected function applyModuleItems(array $items)
    {
        if (!$items) {
            return;
        }

        if (!$this->db->tableExists('#__modules')) {
            $this->say('No modules table - skipping the module instance settings', 'warning');
            return;
        }

        foreach ($items as $which => $state) {
            $where = $this->moduleWhere($which);

            $this->db->setQuery("SELECT COUNT(*) FROM `#__modules` WHERE " . $where);

            if (!(int) $this->db->loadResult()) {
                $this->say("No module instance {$which} to set", 'warning');
                continue;
            }

            $this->db->setQuery("UPDATE `#__modules` SET `published` = " . (int) $state . " WHERE " . $where);
            $this->db->query();

            $this->say(((int) $state ? 'Publishing' : 'Unpublishing') . " module instance {$which}");
        }
    }

    /**
     * Compare module instances with the flavor's
     *
     * @param   array  &$found
     * @param   array  $items
     * @return  void
     */
    protected function checkModuleItems(array &$found, array $items)
    {
        if (!$items || !$this->db->tableExists('#__modules')) {
            return;
        }

        foreach ($items as $which => $state) {
            $this->db->setQuery("SELECT `published` FROM `#__modules` WHERE " . $this->moduleWhere($which));
            $rows = (array) $this->db->loadColumn();

            if (!$rows) {
                $this->notes[] = "no module instance {$which} to set";
                continue;
            }

            foreach ($rows as $now) {
                if ((int) $now !== (int) $state) {
                    $found[] = "module instance {$which} is " . ((int) $now ? 'published' : 'unpublished')
                        . " (the flavor " . ((int) $state ? 'publishes' : 'unpublishes') . " it)";
                    break;
                }
            }
        }
    }

    /**
     * The WHERE that picks module instances by id or title, on the site
     *
     * @param   mixed  $which
     * @return  string
     */
    protected function moduleWhere($which)
    {
        $pick = is_numeric($which) ? "`id` = " . (int) $which : "`title` = " . $this->db->quote($which);

        return $pick . " AND `client_id` = 0";
    }

    // -------------------------------------------------------------- template

    /**
     * Make a template's site style the default
     *
     * @param   string|null  $template
     * @return  void
     */
    protected function applyTemplate($template)
    {
        if (!$template) {
            return;
        }

        Style::makeDefault($this->db, $template, 0, $this->log);
    }

    /**
     * Compare the default template with the flavor's
     *
     * @param   array        &$found
     * @param   string|null  $template
     * @return  void
     */
    protected function checkTemplate(array &$found, $template)
    {
        if (!$template || !$this->db->tableExists('#__template_styles')) {
            return;
        }

        $home = Style::current($this->db, 0);

        if ($home !== (string) $template) {
            $found[] = "the default template is " . ($home !== '' ? $home : 'not set') . " (the flavor sets {$template})";
        }
    }

    // ------------------------------------------------------------- dashboard

    /**
     * Set the member dashboard's default tiles
     *
     * @param   array|null  $tiles  Each with a module and a col
     * @return  void
     */
    protected function applyTiles($tiles)
    {
        if (!is_array($tiles)) {
            return;
        }

        $this->setParams('plugin', 'members/dashboard', array('defaults' => json_encode(self::layout($tiles))));
        $this->say('Setting the default member dashboard (' . count($tiles) . ' tiles)');
    }

    /**
     * Compare the dashboard's default tiles with the flavor's
     *
     * @param   array       &$found
     * @param   array|null  $tiles
     * @return  void
     */
    protected function checkTiles(array &$found, $tiles)
    {
        if (!is_array($tiles)) {
            return;
        }

        $current = $this->currentParams('plugin', 'members/dashboard');

        if (!$current) {
            $found[] = 'plugin members/dashboard is not installed';
            return;
        }

        $now = $this->decode(isset($current['']['defaults']) ? $current['']['defaults'] : '');

        if ($this->tileKeys($now) !== $this->tileKeys(self::layout($tiles))) {
            $found[] = 'the member dashboard has ' . count($now) . ' default tiles'
                . ' (the flavor lays out ' . count($tiles) . ')';
        }
    }

    /**
     * Give each tile its row: two rows high, stacked in its column in the
     * order given, then listed column by column as the shipped data is
     *
     * @param   array  $tiles  Each with a module and a col
     * @return  array  Each with row, size_x and size_y as well
     */
    public static function layout(array $tiles)
    {
        $next = array();
        $laid = array();

        foreach ($tiles as $tile) {
            $col = (int) $tile['col'];
            $row = isset($next[$col]) ? $next[$col] : 1;
            $next[$col] = $row + 2;

            $laid[] = array(
                'module' => (int) $tile['module'],
                'col'    => $col,
                'row'    => $row,
                'size_x' => 1,
                'size_y' => 2,
            );
        }

        usort($laid, function ($a, $b) {
            return ($a['col'] - $b['col']) ?: ($a['row'] - $b['row']);
        });

        return $laid;
    }

    /**
     * Tiles reduced to what places them, for comparing
     *
     * @param   array  $tiles
     * @return  array
     */
    protected function tileKeys(array $tiles)
    {
        $keys = array();

        foreach ($tiles as $tile) {
            if (is_array($tile) && isset($tile['module'], $tile['col'], $tile['row'])) {
                $keys[] = (int) $tile['module'] . '@' . (int) $tile['col'] . ',' . (int) $tile['row'];
            }
        }

        sort($keys);

        return $keys;
    }

    // ------------------------------------------------------- published states

    /**
     * Set a state column on rows picked by alias (or another key column)
     *
     * A menu item is picked by its path - the address the router matches -
     * which is unique among a client's items where an alias is not.
     *
     * @param   string  $table
     * @param   string  $column
     * @param   array   $states  key => state
     * @param   string  $also    An extra condition, or ''
     * @param   string  $what    For messages
     * @param   string  $key     The column the keys name
     * @return  void
     */
    protected function applyStates($table, $column, array $states, $also, $what, $key = 'alias')
    {
        if (!$states) {
            return;
        }

        if (!$this->db->tableExists($table)) {
            $this->say("No {$table} table - skipping the {$what} settings", 'warning');
            return;
        }

        foreach ($states as $name => $state) {
            $where = "`{$key}` = " . $this->db->quote($name) . ($also ? " AND {$also}" : '');

            $this->db->setQuery("SELECT COUNT(*) FROM `{$table}` WHERE " . $where);

            if (!(int) $this->db->loadResult()) {
                $this->say("No {$what} {$name} to set", 'warning');
                continue;
            }

            $this->db->setQuery("UPDATE `{$table}` SET `{$column}` = " . (int) $state . " WHERE " . $where);
            $this->db->query();

            $this->say(((int) $state ? 'Publishing ' : 'Unpublishing ') . $what . ' ' . $name);
        }
    }

    /**
     * Compare state columns with the flavor's
     *
     * @param   array   &$found
     * @param   string  $table
     * @param   string  $column
     * @param   array   $states
     * @param   string  $also
     * @param   string  $what
     * @param   string  $key
     * @return  void
     */
    protected function checkStates(array &$found, $table, $column, array $states, $also, $what, $key = 'alias')
    {
        if (!$states || !$this->db->tableExists($table)) {
            return;
        }

        foreach ($states as $alias => $state) {
            $this->db->setQuery(
                "SELECT `{$column}` FROM `{$table}` WHERE `{$key}` = " . $this->db->quote($alias)
                . ($also ? " AND {$also}" : '')
            );
            $now = $this->db->loadResult();

            if ($now === null) {
                $this->notes[] = "no {$what} {$alias} to set";
                continue;
            }

            if ((int) $now !== (int) $state) {
                $found[] = "{$what} {$alias} is " . ((int) $now ? 'published' : 'unpublished')
                    . " (the flavor " . ((int) $state ? 'publishes' : 'unpublishes') . " it)";
            }
        }
    }

    // --------------------------------------------------------- resource types

    /**
     * Set columns on resource types picked by alias
     *
     * @param   array  $types  alias => (column => value)
     * @return  void
     */
    protected function applyResourceTypes(array $types)
    {
        if (!$types) {
            return;
        }

        if (!$this->db->tableExists('#__resource_types')) {
            $this->say('No resource types table - skipping those settings', 'warning');
            return;
        }

        foreach ($types as $alias => $columns) {
            $set = array();

            foreach ($columns as $column => $value) {
                $set[] = $this->db->quoteName($column) . ' = ' . $this->db->quote($value);
            }

            if (!$set) {
                continue;
            }

            $this->db->setQuery(
                "UPDATE `#__resource_types` SET " . implode(', ', $set) . " WHERE `alias` = " . $this->db->quote($alias)
            );
            $this->db->query();

            $this->say('Setting ' . implode(', ', array_keys($columns)) . ' on resource type ' . $alias);
        }
    }

    /**
     * Compare resource type columns with the flavor's
     *
     * @param   array  &$found
     * @param   array  $types
     * @return  void
     */
    protected function checkResourceTypes(array &$found, array $types)
    {
        if (!$types || !$this->db->tableExists('#__resource_types')) {
            return;
        }

        foreach ($types as $alias => $columns) {
            $this->db->setQuery("SELECT * FROM `#__resource_types` WHERE `alias` = " . $this->db->quote($alias));
            $row = $this->db->loadAssoc();

            if (!$row) {
                $this->notes[] = "no resource type {$alias} to set";
                continue;
            }

            foreach ($columns as $column => $value) {
                $now = array_key_exists($column, $row) ? $row[$column] : null;

                if ((string) $now !== (string) $value) {
                    $found[] = "resource type {$alias}: {$column} is " . $this->show($now)
                        . " (the flavor sets " . $this->show($value) . ")";
                }
            }
        }
    }

    // ---------------------------------------------------------------- helpers

    /**
     * Parameters as an array, from whatever form they were stored in
     *
     * @param   mixed  $params
     * @return  array
     */
    protected function decode($params)
    {
        if (is_array($params)) {
            return $params;
        }

        $decoded = json_decode((string) $params, true);

        return is_array($decoded) ? $decoded : array();
    }

    /**
     * A value as it reads in a message
     *
     * @param   mixed  $value
     * @return  string
     */
    protected function show($value)
    {
        if ($value === null) {
            return 'unset';
        }

        return is_scalar($value) ? "'" . $value . "'" : json_encode($value);
    }

    /**
     * Say what is being done, if anyone is listening
     *
     * @param   string  $message
     * @param   string  $type
     * @return  void
     */
    protected function say($message, $type = 'info')
    {
        if ($this->log) {
            call_user_func($this->log, $message, $type);
        }
    }
}
