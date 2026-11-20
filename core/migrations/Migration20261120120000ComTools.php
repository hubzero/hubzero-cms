<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Remove the Windows tools options from com_tools
 *
 * Windows tools (simulation tools streamed from AWS) are no longer supported:
 * their admin screens and options are gone, and the resources plugin went
 * before them. A hub that used them still holds the options, an AWS secret
 * key among them, in the component's parameters, where nothing reads or shows
 * them any more. They are removed rather than left behind.
 *
 * There is no down(): the values are not kept anywhere to restore them from,
 * and the screens that used them are gone.
 **/
class Migration20261120120000ComTools extends Base
{
    /**
     * The options the Windows screens and the fileshare API used
     *
     * @var  array<int, string>
     */
    private const OPTIONS = [
        'windows_key_id',
        'windows_secret_key',
        'windows_type',
        'windows_monthly_max_hours',
    ];

    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $raw = $this->getParams('com_tools', true);
        $params = is_string($raw) ? json_decode($raw, true) : null;
        if (!is_array($params)) {
            return;
        }

        $kept = array_diff_key($params, array_flip(self::OPTIONS));
        if (count($kept) === count($params)) {
            return;
        }

        $this->saveParams('com_tools', $kept);
    }
}
