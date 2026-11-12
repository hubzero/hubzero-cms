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
     * @param   object  $pInfo  Product info
     * @param   int     $crtId  Cart ID
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
        $className = self::auditorClass($model);
        if (class_exists($className)) {
            return new $className($type, $pId, $crtId);
        } else {
            return new \Components\Cart\Lib\Auditors\BaseAuditor($type);
        }
    }

    /**
     * Name the class that audits products of a model
     *
     * The class need not exist: most models have no auditor of their own.
     *
     * @param   string  $model  Product model, as the product type records it
     * @return  string  Fully qualified class name
     */
    public static function auditorClass($model)
    {
        $auditorClass = str_replace(' ', '', ucwords(strtolower($model))) . 'Auditor';

        return "\\Components\\Cart\\Lib\\Auditors\\" . $auditorClass;
    }
}
