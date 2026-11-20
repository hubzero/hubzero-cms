<?php

namespace Plugins\Search\Weightcontributor;

use Hubzero\Plugin\Plugin;

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/**
 * Short description for 'plgSearchWeightContributor'
 *
 * Long description (if any) ...
 */
/**
 */
class Weightcontributor extends Plugin
{
    /**
     * Short description for 'onSearchWeightAll'
     *
     * Long description (if any) ...
     *
     * @param      \Components\Search\Models\Basic\Terms $terms Parameter description (if any) ...
     * @param      \Components\Search\Models\Basic\Result $res Parameter description (if any) ...
     * @return     float Return description (if any) ...
     */
    public static function onSearchWeightAll($terms, $res)
    {
        $pos_terms = $terms->getPositiveChunks();

        foreach (array_map('strtolower', $res->getContributors()) as $contributor) {
            foreach ($pos_terms as $term) {
                if (str_contains($contributor, $term)) {
                    return 1.0;
                }
            }
        }
        return 0.5;
    }
}
