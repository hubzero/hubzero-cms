<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for setting the com_update asset rules to only allow supers
 *
*/
class Migration20141105142938ComUpdate extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $rules = [
            'core.admin' => [
                'Super Users' => 1,
            ],
        ];

        $this->setAssetRules('com_update', $rules);
    }
}
