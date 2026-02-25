<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Cart\Helpers;

use Components\Storefront\Models\Warehouse;

class Audit
{
    /**
     * Constructor
     * @param   object  Product info
     * @param   int     Cart ID
     * @param   int     User ID
     * @return  object
     */
    public static function getAuditor($pInfo, $crtId)
    {
        $pId = $pInfo->pId;

        $warehouse = new Warehouse();
        // Get product type
        $pType = $warehouse->getProductTypeInfo($pInfo->ptId);

        $type = $pType['ptName'];
        $model = $pType['ptModel'];

        // Find if there are auditors for this product's type and model
        $auditorClass = str_replace(' ', '_', ucwords(strtolower($model))) . '_Auditor';
        $className = "\\Components\\Cart\\Lib\\Auditors\\" . $auditorClass;
        if (class_exists($className)) {
            return new $className($type, $pId, $crtId);
        } else {
            return new \Components\Cart\Lib\Auditors\BaseAuditor($type);
        }
    }
}
