<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script to update 'file' column for migration entries.
 * Files were renamed as their timestamps were incorrect (too many characters)
  *
**/
class Migration20150827120311Core extends Base
{
    /**
     * List of files that were renamed
     *
     * @var  array
     **/
    public $files = array(
        'Migration201508281131531Core.php'           => 'Migration20150828113153Core.php',
        'Migration201508281528321Core.php'           => 'Migration20150828152832Core.php',
        'Migration201603091905401ComNewsletter.php'  => 'Migration20160309190540ComNewsletter.php',
        'Migration201609151104002ComMembers.php'     => 'Migration20160915110400ComMembers.php',
        'Migration2016090510300000Core.php'          => 'Migration20160905103000Core.php',
        'Migration2016090511300000Core.php'          => 'Migration20160905113000Core.php',
        'Migration2016090610030000ComTools.php'      => 'Migration20160906100300ComTools.php',
        'Migration2016090610110000Core.php'          => 'Migration20160906101100Core.php',
        'Migration2016090710240000ComCourses.php'    => 'Migration20160907102400ComCourses.php',
        'Migration2016090710280000ComCategories.php' => 'Migration20160907102800ComCategories.php',
        'Migration2016090710350000ComBillboards.php' => 'Migration20160907103500ComBillboards.php',
        'Migration2016090710430000ComCart.php'       => 'Migration20160907104300ComCart.php',
        'Migration2016090710490000Core.php'          => 'Migration20160907104900Core.php',
        'Migration2016090710530000ComUsers.php'      => 'Migration20160907105300ComUsers.php',
        'Migration2016090710560000ComSupport.php'    => 'Migration20160907105600ComSupport.php',
        'Migration2016090711260000Core.php'          => 'Migration20160907112600Core.php'
    );

    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (
            $schema->tableExists('#__migrations')
            && $schema->hasColumn('#__migrations', 'file')
        ) {
            foreach ($this->files as $old => $renamed) {
                $this->db->getQuery(true)
                    ->update('#__migrations')
                    ->set(['file' => $renamed])
                    ->where('file', '=', $old)
                    ->execute();
            }
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if (
            $schema->tableExists('#__migrations')
            && $schema->hasColumn('#__migrations', 'file')
        ) {
            foreach ($this->files as $old => $renamed) {
                $this->db->getQuery(true)
                    ->update('#__migrations')
                    ->set(['file' => $old])
                    ->where('file', '=', $renamed)
                    ->execute();
            }
        }
    }
}
