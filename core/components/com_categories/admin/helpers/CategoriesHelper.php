<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Categories\Admin\Helpers;

use Hubzero\Base\Obj;
use Hubzero\Facades\Filesystem;
use Hubzero\Facades\Component;
use Hubzero\Facades\User;
use Hubzero\Facades\Lang;

/**
 * Categories helper
 */
class CategoriesHelper
{
    /**
     * Configure the Submenu links.
     *
     * @param   string  $extension  The extension being used for the categories
     * @return  void
     */
    public static function addSubmenu($extension)
    {
        // Avoid nonsense situation.
        if ($extension == 'com_categories') {
            return;
        }

        $parts = explode('.', $extension == null ? '' : $extension);
        $component = $parts[0];

        if (count($parts) > 1) {
            $section = $parts[1];
        }

        // Try to find the component helper.
        $eName = str_replace('com_', '', $component);
        $file  = Filesystem::cleanPath(Component::path($component) . '/admin/helpers/' . $eName . '.php');

        if (file_exists($file)) {
            require_once $file;

            $prefix = ucfirst(str_replace('com_', '', $component));
            $cName  = $prefix . 'Helper';

            if (class_exists($cName)) {
                if (is_callable(array($cName, 'addSubmenu'))) {
                    // loading language file from the components/*extension*/admin/language directory
                    $langPath = Filesystem::cleanPath(Component::path($component) . '/admin');
                    Lang::load($component, $langPath, null, false, true);

                    $suffix = isset($section) ? '.' . $section : '';
                    call_user_func(array($cName, 'addSubmenu'), 'categories' . $suffix);
                }
            }
        }
    }

    /**
     * Gets a list of the actions that can be performed.
     *
     * @param   string   $extension  The extension.
     * @param   integer  $assetId    The category ID.
     * @return  object
     */
    public static function getActions($extension = 'com_categories', $assetType = 'component', $assetId = 0)
    {
        $assetName  = $extension;
        $assetName .= '.' . $assetType;
        if ($assetId) {
            $assetName .= '.' . (int) $assetId;
        }

        $result = new Obj();

        $actions = array(
            'core.admin',
            'core.manage',
            'core.create',
            'core.edit',
            'core.edit.state',
            'core.delete'
        );

        foreach ($actions as $action) {
            $result->set($action, User::authorise($action, $assetName));
        }

        return $result;
    }
}
