<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Demo\Site;

use Hubzero\Framework\Component\AbstractComponent;

class Demo extends AbstractComponent
{
    protected function execute(): void
    {
        $controllerName = app('hubzero.request')->getCmd('controller', 'demo');

        $namespace = 'Components\\Demo\\Site\\Controllers\\' . ucfirst(strtolower($controllerName));

        if (class_exists($namespace)) {
            $controller = new $namespace(['base_path' => __DIR__]);
            $controller->execute();
        }
    }
}
