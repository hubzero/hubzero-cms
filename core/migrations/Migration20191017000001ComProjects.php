<?php

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for changing the data_definition field type to accommodate
 * longer strings needed for files with many fields
  *
**/
class Migration20191017000001ComProjects extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (
            $schema->tableExists('#__project_databases')
            && $schema->hasColumn('#__project_databases', 'data_definition')
        ) {
            $schema->modifyColumn('#__project_databases', 'data_definition')->mediumText()->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if (
            $schema->tableExists('#__project_databases')
            && $schema->hasColumn('#__project_databases', 'data_definition')
        ) {
            $schema->modifyColumn('#__project_databases', 'data_definition')->text();
        }
    }
}
