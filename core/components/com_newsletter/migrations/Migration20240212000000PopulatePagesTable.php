<?php

namespace Components\Newsletter\Migrations;

use Hubzero\Content\Migration\Base;
use Hubzero\Facades\Date;

class Migration20240212000000PopulatePagesTable extends Base
{
    /**
     * @var  string
     */
    public static $tableName = '#__reply_pages';

    /**
     * Up
     *
     * @return  void
     */
    public function up()
    {
        $tableName = self::$tableName;
        $now = Date::toSql();
        $auto = $this->db->getAutoIncrement($tableName);

        // create records only if they do not already exist:
        if ($auto == 0) {
            $insertRecords = "INSERT INTO $tableName
				(`description`, `created`)
				VALUES
				('email subscriptions', '$now'),
				('how do you use the Hub?', '$now');";

            if ($this->db->tableExists($tableName)) {
                $this->db->setQuery($insertRecords);
                $this->db->query();
            }
        }
    }

    /**
     * Down
     *
     * @return  void
     */
    public function down()
    {
        // manually delete records
    }
}
