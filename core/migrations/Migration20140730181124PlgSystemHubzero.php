<?php

// phpcs:disable PSR1.Files.SideEffects

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for updating system search preference
 *
 * @phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
 */
class Migration20140730181124PlgSystemHubzero extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $params = $this->getParams('plg_system_hubzero');
        $search = $params->get('search');

        if ($search && $search == 'ysearch') {
            $params->set('search', 'search');
            $this->saveParams('plg_system_hubzero', $params);
        }
    }
}
