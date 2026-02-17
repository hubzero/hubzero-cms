<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Cart\Helpers;

use Components\Storefront\Models\Warehouse;

/**
 * Product handler. Handles purchased products/items. Runs a proper handler on each purchased item.
 */
class ProductHandler
{
    // Item info
    public $item;
    public $crtId;
    public $tId;

    /**
     * Constructor
     *
     * @param   object  $item   item info
     * @param   int     $crtId  cart ID
     * @param   int     $tId    transaction ID
     * @return  void
     */
    public function __construct($item, $crtId, $tId)
    {
        $this->item = $item;
        $this->crtId = $crtId;
        $this->tId = $tId;
    }

    /**
     * Process item
     *
     * @return  void
     */
    public function handle()
    {
        // Get product type info
        $ptId = $this->item['info']->ptId;

        $warehouse = new Warehouse();

        $ptIdTypeInfo = $warehouse->getProductTypeInfo($ptId);

        // Run both product model handler and type handler if needed.
        // Model handlers must go first for type handlers to potentially use their updates

        // MODEL HANDLER
        $modelHandlerClass = str_replace(' ', '', ucwords(strtolower($ptIdTypeInfo['ptModel'])))
            . 'ModelHandler';
        $modelHandlerFqcn = '\\Components\\Cart\\Lib\\Handlers\\Model\\' . $modelHandlerClass;
        if (class_exists($modelHandlerFqcn)) {
            $modelHandler = new $modelHandlerFqcn($this->item, $this->crtId, $this->tId);
            $modelHandler->handle();
        }

        // TYPE HANDLER
        $typeHandlerClass = str_replace(' ', '', ucwords(strtolower($ptIdTypeInfo['ptName'])))
            . 'TypeHandler';
        $typeHandlerFqcn = '\\Components\\Cart\\Lib\\Handlers\\Type\\' . $typeHandlerClass;
        if (class_exists($typeHandlerFqcn)) {
            $typeHandler = new $typeHandlerFqcn($this->item, $this->crtId);
            $typeHandler->handle();
        }

        // CUSTOM HANDLERS (if any)
        if (!empty($this->item['meta']['customHandler'])) {
            $customHandler = $this->item['meta']['customHandler'];
            $customHandlerClass = str_replace(' ', '', ucwords(strtolower($customHandler)))
                . 'CustomHandler';
            $customHandlerFqcn = '\\Components\\Cart\\Lib\\Handlers\\Custom\\' . $customHandlerClass;

            if (class_exists($customHandlerFqcn)) {
                $handler = new $customHandlerFqcn($this->item, $this->crtId);
                $handler->handle();
            }
        }
    }
}
