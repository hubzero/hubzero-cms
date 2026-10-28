<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Massmail\Admin\Helpers;

use Hubzero\Base\Obj;
use User;

/**
 * Permissions helper -- resolves the current user's mass-mail ACL actions.
 */
class Permissions
{
    /**
     * Name of the component
     *
     * @var  string
     */
    public static $extension = 'com_massmail';

    /**
     * Gets a list of the actions that can be performed.
     *
     * @param   string   $assetType  The asset type.
     * @param   integer  $assetId    The asset ID.
     * @return  object
     */
    public static function getActions($assetType = 'component', $assetId = 0)
    {
        $assetName = self::$extension;
        if ($assetId) {
            $assetName .= '.' . $assetType;
            $assetName .= '.' . (int) $assetId;
        }

        $result = new Obj();

        $actions = array(
            'core.admin',
            'core.manage',
            'core.create',
            'core.edit',
            'core.edit.own',
            'core.edit.state',
            'core.delete',
            'core.send',
            'core.override',
            'core.approve',
            'core.release'
        );

        foreach ($actions as $action) {
            $result->set($action, User::authorise($action, $assetName));
        }

        return $result;
    }
}
