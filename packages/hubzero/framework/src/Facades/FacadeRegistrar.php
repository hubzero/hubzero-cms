<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Framework\Facades;

/**
 * Registers class aliases so legacy HubZero code can use bare
 * facade names (Document::, Pathway::, etc.) and namespaced
 * imports (use Hubzero\Facades\Request).
 */
class FacadeRegistrar
{
    private static bool $registered = false;

    public static function register(): void
    {
        if (static::$registered) {
            return;
        }

        // Global aliases — safe names that don't conflict with Laravel
        $globalAliases = [
            'Document'  => DocumentFacade::class,
            'Pathway'   => PathwayFacade::class,
            'Lang'      => LangFacade::class,
            'Component' => ComponentFacade::class,
            'User'      => UserFacade::class,
            'Config'    => ConfigFacade::class,
            'Html'      => HtmlFacade::class,
            'Notify'    => NotifyFacade::class,
            'Date'      => DateFacade::class,
        ];

        foreach ($globalAliases as $alias => $class) {
            if (!class_exists($alias, false)) {
                class_alias($class, $alias);
            }
        }

        // Hubzero\Facades\ namespace aliases — for legacy "use" imports
        $namespacedAliases = [
            'Hubzero\\Facades\\Document'  => DocumentFacade::class,
            'Hubzero\\Facades\\Pathway'   => PathwayFacade::class,
            'Hubzero\\Facades\\Request'   => RequestFacade::class,
            'Hubzero\\Facades\\App'       => AppFacade::class,
            'Hubzero\\Facades\\Lang'      => LangFacade::class,
            'Hubzero\\Facades\\Component' => ComponentFacade::class,
            'Hubzero\\Facades\\User'      => UserFacade::class,
            'Hubzero\\Facades\\Config'    => ConfigFacade::class,
            'Hubzero\\Facades\\Html'      => HtmlFacade::class,
            'Hubzero\\Facades\\Notify'    => NotifyFacade::class,
            'Hubzero\\Facades\\Date'      => DateFacade::class,
        ];

        foreach ($namespacedAliases as $alias => $class) {
            if (!class_exists($alias, false)) {
                class_alias($class, $alias);
            }
        }

        static::$registered = true;
    }
}
