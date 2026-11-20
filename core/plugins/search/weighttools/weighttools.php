<?php

namespace Plugins\Search\Weighttools;

use Hubzero\Plugin\Plugin;

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/**
 * Search pluginf or weighting tools
 */
/**
 */
class Weighttools extends Plugin
{
    /**
     * Short description for 'onSearchWeightResources'
     *
     * Long description (if any) ...
     *
     * @param   mixed  $_terms  Parameter description (if any) ...
     * @param   \Components\Search\Models\Basic\Result   $res     Parameter description (if any) ...
     * @return  mixed    Return description (if any) ...
     */
    public static function onSearchWeightResources($_terms, $res)
    {
        return $res->getPlugin() == 'resources' && $res->getSection() == 'Tools' ? 1 : 0.5;
    }
}
