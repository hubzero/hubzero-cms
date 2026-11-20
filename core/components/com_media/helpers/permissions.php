<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Media\Helpers;

use Hubzero\Base\Obj;
use Hubzero\Facades\User;

/**
 * Permissions helper
 */
class Permissions
{
    /**
     * Name of the component
     *
     * @var  string
     */
    public static $extension = 'com_media';

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

        $result = new Obj();

        $actions = [
            'core.admin',
            'core.manage',
            'core.create',
            'core.edit',
            'core.edit.state',
            'core.delete',
        ];

        foreach ($actions as $action) {
            $result->set($action, User::authorise($action, $assetName));
        }

        return $result;
    }
}
