<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script to drop misnamed field that can get left behind during upgrades
 *
*/
class Migration20160907102400ComCourses extends Base
{
    public function up()
    {
        if ($this->db->tableHasField('#__courses_form_respondents', 'attempts')) {
            $query = "ALTER TABLE `#__courses_form_respondents` DROP `attempts`;";
            $this->db->setQuery($query);
            $this->db->query();
        }
    }

    public function down()
    {
    }
}
