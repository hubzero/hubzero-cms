<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Html\Builder;

use Hubzero\Error\Exception\Exception;
use Hubzero\Facades\Lang;
use Hubzero\Facades\App;
use Hubzero\Facades\Document;

/**
 * Extended Utility class for all HTML drawing classes.
 */
class Access
{
    /**
     * A cached array of the asset groups
     *
     * @public array
     */
    protected static $asset_groups = null;

    /**
     * Escape a string for safe HTML output.
     *
     * @param   string  $value
     * @return  string
     */
    protected static function esc($value)
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8', true);
    }

    /**
     * Displays a list of the available access view levels
     *
     * @param   string  $name      The form field name.
     * @param   string  $selected  The name of the selected section.
     * @param   string  $attribs   Additional attributes to add to the select field.
     * @param   mixed   $params    True to add "All Sections" option or and array of options
     * @param   string  $id        The form field id
     * @return  string  The required HTML for the SELECT tag.
     */
    public static function level($name, $selected, $attribs = '', $params = true, $id = false)
    {
        $db = App::get('db');

        $query = $db->getQuery()
            ->select('a.id', 'value')
            ->select('a.title', 'text')
            ->from('#__viewlevels', 'a')
            ->group('a.id')
            ->group('a.title')
            ->group('a.ordering')
            ->order('a.ordering', 'asc')
            ->order('title', 'asc');

        // Get the options.
        $db->setQuery($query->toString());
        $options = $db->loadObjectList();

        // Check for a database error.
        if ($db->getErrorNum()) {
            throw new \Exception($db->getErrorMsg(), 500, E_WARNING);
            return null;
        }

        // If params is an array, push these options to the array
        if (is_array($params)) {
            $options = array_merge($params, $options);
        } elseif ($params) {
        // If all levels is allowed, push it into the array.
            array_unshift($options, Select::option('', Lang::txt('JOPTION_ACCESS_SHOW_ALL_LEVELS')));
        }

        if (Document::getCssFramework() === 'daisyui') {
            $idAttr  = $id ? ' id="' . static::esc($id) . '"' : '';
            $html  = '<select name="' . static::esc($name) . '"' . $idAttr
                   . ' class="select select-sm select-bordered w-full">';
            foreach ($options as $opt) {
                $v = is_object($opt) ? $opt->value : ($opt['value'] ?? '');
                $t = is_object($opt) ? $opt->text  : ($opt['text'] ?? '');
                $sel = ((string) $v === (string) $selected) ? ' selected' : '';
                $html .= '<option value="' . static::esc($v) . '"' . $sel . '>'
                       . static::esc($t) . '</option>';
            }
            $html .= '</select>';
            return $html;
        }

        return Select::genericlist(
            $options,
            $name,
            array(
                'list.attr' => $attribs,
                'list.select' => $selected,
                'id' => $id
            )
        );
    }

    /**
     * Displays a list of the available user groups.
     *
     * @param   string   $name      The form field name.
     * @param   string   $selected  The name of the selected section.
     * @param   string   $attribs   Additional attributes to add to the select field.
     * @param   boolean  $allowAll  True to add "All Groups" option.
     * @return  string   The required HTML for the SELECT tag.
     */
    public static function usergroup($name, $selected, $attribs = '', $allowAll = true, $id = null)
    {
        $db = App::get('db');
        $query = $db->getQuery()
            ->select('a.id', 'value')
            ->select('a.title', 'text')
            ->select('COUNT(DISTINCT b.id)', 'level')
            ->from('#__usergroups', 'a')
            ->joinRaw('#__usergroups AS b', 'a.lft > b.lft AND a.rgt < b.rgt', 'left')
            ->group('a.id')
            ->group('a.title')
            ->group('a.lft')
            ->group('a.rgt')
            ->order('a.lft', 'asc');
        $db->setQuery($query->toString());
        $options = $db->loadObjectList();

        // Check for a database error.
        if ($db->getErrorNum()) {
            throw new \Exception($db->getErrorMsg(), 500, E_WARNING);
            return null;
        }

        for ($i = 0, $n = count($options); $i < $n; $i++) {
            $options[$i]->text = str_repeat('- ', $options[$i]->level) . $options[$i]->text;
        }

        // If all usergroups is allowed, push it into the array.
        if ($allowAll) {
            array_unshift($options, Select::option('', Lang::txt('JOPTION_ACCESS_SHOW_ALL_GROUPS')));
        }

        if (Document::getCssFramework() === 'daisyui') {
            $idAttr = $id ? ' id="' . static::esc($id) . '"' : '';
            $html  = '<select name="' . static::esc($name) . '"'
                   . $idAttr
                   . ' class="select select-sm select-bordered w-full">';
            foreach ($options as $opt) {
                $v = is_object($opt) ? $opt->value : ($opt['value'] ?? '');
                $t = is_object($opt) ? $opt->text  : ($opt['text'] ?? '');
                $sel = ((string) $v === (string) $selected) ? ' selected' : '';
                $html .= '<option value="' . static::esc($v) . '"' . $sel . '>'
                       . static::esc($t) . '</option>';
            }
            $html .= '</select>';
            return $html;
        }

        return Select::genericlist($options, $name, array('list.attr' => $attribs, 'list.select' => $selected));
    }

    /**
     * Returns a UL list of user groups with check boxes
     *
     * @param   string   $name             The name of the checkbox controls array
     * @param   array    $selected         An array of the checked boxes
     * @param   boolean  $checkSuperAdmin  If false only super admins can add to super admin groups
     * @return  string
     */
    public static function usergroups($name, $selected, $checkSuperAdmin = false)
    {
        static $count;

        $count++;

        $isSuperAdmin = \Hubzero\Facades\User::authorise('core.admin');

        $db = App::get('db');
        $query = $db->getQuery()
            ->select('a.*')
            ->select('COUNT(DISTINCT b.id)', 'level')
            ->from('#__usergroups', 'a')
            ->joinRaw('#__usergroups AS b', 'a.lft > b.lft AND a.rgt < b.rgt', 'left')
            ->group('a.id')
            ->group('a.title')
            ->group('a.lft')
            ->group('a.rgt')
            ->group('a.parent_id')
            ->order('a.lft', 'asc');
        $db->setQuery($query->toString());
        $groups = $db->loadObjectList();

        // Check for a database error.
        if ($db->getErrorNum()) {
            throw new \Exception($db->getErrorMsg(), 500, E_WARNING);
            return null;
        }

        $html = array();

        if (Document::getCssFramework() === 'daisyui') {
            $html[] = '<ul class="space-y-1">';

            for ($i = 0, $n = count($groups); $i < $n; $i++) {
                $item = &$groups[$i];

                if (
                    (!$checkSuperAdmin) ||
                    $isSuperAdmin ||
                    (!\Hubzero\Access\Access::checkGroup($item->id, 'core.admin'))
                ) {
                    $eid     = $count . 'group_' . $item->id;
                    $checked = ($selected && in_array($item->id, $selected)) ? ' checked' : '';
                    $rel     = ($item->parent_id > 0)
                        ? ' data-parent="' . $count . 'group_' . $item->parent_id . '"'
                        : '';
                    $indent  = str_repeat(
                        '<span class="text-faint-foreground select-none">|&mdash;</span>',
                        $item->level
                    );

                    $html[] = '<li class="flex items-center gap-2">';
                    $html[] = '<input type="checkbox" class="checkbox checkbox-sm" name="'
                        . static::esc($name) . '[]" value="' . static::esc($item->id)
                        . '" id="' . static::esc($eid) . '"' . $checked . $rel . ' />';
                    $html[] = '<label for="' . static::esc($eid)
                        . '" class="label label-text cursor-pointer text-sm">'
                        . $indent . static::esc($item->title) . '</label>';
                    $html[] = '</li>';
                }
            }

            $html[] = '</ul>';
        } else {
            $html[] = '<ul class="checklist usergroups">';

            for ($i = 0, $n = count($groups); $i < $n; $i++) {
                $item = &$groups[$i];

                if (
                    (!$checkSuperAdmin) ||
                    $isSuperAdmin ||
                    (!\Hubzero\Access\Access::checkGroup($item->id, 'core.admin'))
                ) {
                    $eid = $count . 'group_' . $item->id;
                    $checked = '';
                    if ($selected) {
                        $checked = in_array($item->id, $selected) ? ' checked="checked"' : '';
                    }
                    $rel = ($item->parent_id > 0) ? ' rel="' . $count . 'group_' . $item->parent_id . '"' : '';

                    $html[] = '	<li>';
                    $html[] = '		<input type="checkbox" name="' .
                        $name .
                        '[]" value="' .
                        $item->id .
                        '" id="' .
                        $eid .
                        '"' .
                        $checked .
                        $rel .
                        ' />';
                    $html[] = '		<label for="' . $eid . '">';
                    $html[] = '		' . str_repeat('<span class="gi">|&mdash;</span>', $item->level) . $item->title;
                    $html[] = '		</label>';
                    $html[] = '	</li>';
                }
            }

            $html[] = '</ul>';
        }

        return implode("\n", $html);
    }

    /**
     * Returns a UL list of actions with check boxes
     *
     * @param   string  $name       The name of the checkbox controls array
     * @param   array   $selected   An array of the checked boxes
     * @param   string  $component  The component the permissions apply to
     * @param   string  $section    The section (within a component) the permissions apply to
     * @return  string
     */
    public static function actions($name, $selected, $component, $section = 'global')
    {
        static $count;

        $count++;

        $path = PATH_APP . '/components/' . $component . '/config/access.xml';
        if (!file_exists($path)) {
            $path = PATH_CORE . '/components/' . $component . '/config/access.xml';
        }
        $actions = \Hubzero\Access\Access::getActionsFromFile(
            $path,
            "/access/section[@name='" . $section . "']/"
        );

        if (empty($actions)) {
            $actions = array();
        }

        $html = array();

        if (Document::getCssFramework() === 'daisyui') {
            $html[] = '<ul class="space-y-1">';

            for ($i = 0, $n = count($actions); $i < $n; $i++) {
                $item    = &$actions[$i];
                $eid     = $count . 'action_' . $item->id;
                $checked = in_array($item->id, $selected) ? ' checked' : '';

                $html[] = '<li class="flex items-center gap-2">';
                $html[] = '<input type="checkbox" class="checkbox checkbox-sm" name="'
                    . static::esc($name) . '[]" value="' . static::esc($item->id)
                    . '" id="' . static::esc($eid) . '"' . $checked . ' />';
                $html[] = '<label for="' . static::esc($eid)
                    . '" class="label label-text cursor-pointer text-sm">'
                    . static::esc(Lang::txt($item->title)) . '</label>';
                $html[] = '</li>';
            }

            $html[] = '</ul>';
        } else {
            $html[] = '<ul class="checklist access-actions">';

            for ($i = 0, $n = count($actions); $i < $n; $i++) {
                $item    = &$actions[$i];
                $eid     = $count . 'action_' . $item->id;
                $checked = in_array($item->id, $selected) ? ' checked="checked"' : '';

                $html[] = '	<li>';
                $html[] = '		<input type="checkbox" name="' .
                    $name .
                    '[]" value="' .
                    $item->id .
                    '" id="' .
                    $eid .
                    '"' .
                    $checked .
                    ' />';
                $html[] = '		<label for="' . $eid . '">' . Lang::txt($item->title) . '</label>';
                $html[] = '	</li>';
            }

            $html[] = '</ul>';
        }

        return implode("\n", $html);
    }

    /**
     * Gets a list of the asset groups as an array of options.
     *
     * @param   array  $config  An array of options for the options
     * @return  mixed  An array or false if an error occurs
     */
    public static function assetgroups($config = array())
    {
        if (empty(self::$asset_groups)) {
            $db = App::get('db');

            $query = $db->getQuery()
                ->select('a.id', 'value')
                ->select('a.title', 'text')
                ->from('#__viewlevels', 'a')
                ->group('a.id')
                ->group('a.title')
                ->group('a.ordering')
                ->order('a.ordering', 'asc');

            $db->setQuery($query->toString());
            self::$asset_groups = $db->loadObjectList();

            // Check for a database error.
            if ($db->getErrorNum()) {
                throw new \Exception($db->getErrorMsg(), 500, E_WARNING);
                return false;
            }
        }

        return self::$asset_groups;
    }

    /**
     * Displays a Select list of the available asset groups
     *
     * @param   string  $name      The name of the select element
     * @param   mixed   $selected  The selected asset group id
     * @param   string  $attribs   Optional attributes for the select field
     * @param   array   $config    An array of options for the control
     * @return  mixed   An HTML string or null if an error occurs
     */
    public static function assetgrouplist($name, $selected, $attribs = null, $config = array())
    {
        static $count;

        $options = self::assetgroups();
        if (isset($config['title'])) {
            array_unshift($options, Select::option('', $config['title']));
        }

        $id = isset($config['id']) ? $config['id'] : 'assetgroups_' . ++$count;

        if (Document::getCssFramework() === 'daisyui') {
            $html  = '<select name="' . static::esc($name) . '" id="' . static::esc($id) . '"'
                   . ' class="select select-sm select-bordered w-full">';
            foreach ($options as $opt) {
                $v = is_object($opt) ? $opt->value : ($opt['value'] ?? '');
                $t = is_object($opt) ? $opt->text  : ($opt['text'] ?? '');
                $sel = ((string) $v === (string) $selected) ? ' selected' : '';
                $html .= '<option value="' . static::esc($v) . '"' . $sel . '>'
                       . static::esc($t) . '</option>';
            }
            $html .= '</select>';
            return $html;
        }

        return Select::genericlist(
            $options,
            $name,
            array(
                'id' => $id,
                'list.attr' => (is_null($attribs) ? 'class="inputbox" size="3"' : $attribs),
                'list.select' => (int) $selected
            )
        );
    }
}
