<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Cart\Admin\Helpers;

use Hubzero\Base\Obj;
use Hubzero\Facades\User;

class Permissions
{
    /**
     * Name of the component
     *
     * @var  string
     */
    public static $extension = 'com_cart';

    /**
     * Gets a list of the actions that can be performed.
     *
     * @param   string   $assetType  The extension.
     * @param   integer  $assetId    The category ID.
     * @return  \Hubzero\Base\Obj
     */
    public static function getActions($assetType = 'component', $assetId = 0)
    {
        $assetName  = self::$extension;
        $assetName .= '.' . $assetType;
        if ($assetId) {
            $assetName .= '.' . (int) $assetId;
        }

        $user = User::getInstance();
        $result = new Obj();

        $actions = [
            'admin',
            'manage',
            'create',
            'edit',
            'edit.state',
            'delete',
        ];

        foreach ($actions as $action) {
            $result->set('core.' . $action, $user->authorise('core.' . $action, $assetName));
        }

        return $result;
    }
}
