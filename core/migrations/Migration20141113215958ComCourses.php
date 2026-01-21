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
 * Migration script for purging courses form start/end times from form deployments table
 *
 * @phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
 */
class Migration20141113215958ComCourses extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        if ($this->db->tableExists('#__courses_form_deployments')) {
            $query = "UPDATE `#__courses_form_deployments` SET `start_time` = NULL, `end_time` = NULL";
            $this->db->setQuery($query);
            $this->db->query();
        }
    }
}
