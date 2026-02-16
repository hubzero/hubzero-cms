<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for changing field lengths on Newsletter Template table to accomodate more styles
**/
class Migration20160216115900ComNewsletter extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__newsletter_templates')) {
            return;
        }

        // Batch column modifications
        // Batch column modifications
        $schema->modifyColumn('#__newsletter_templates', 'primary_title_color')
            ->string(255)
            ->nullable()
            ->default(null)
            ->execute();
        $schema->modifyColumn('#__newsletter_templates', 'primary_text_color')
            ->string(255)
            ->nullable()
            ->default(null)
            ->execute();
        $schema->modifyColumn('#__newsletter_templates', 'secondary_title_color')
            ->string(255)
            ->nullable()
            ->default(null)
            ->execute();
        $schema->modifyColumn('#__newsletter_templates', 'secondary_text_color')
            ->string(255)
            ->nullable()
            ->default(null)
            ->execute();
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__newsletter_templates')) {
            return;
        }

        // Batch column modifications
        // Batch column modifications
        $schema->modifyColumn('#__newsletter_templates', 'primary_title_color')
            ->string(100)
            ->nullable()
            ->default(null)
            ->execute();
        $schema->modifyColumn('#__newsletter_templates', 'primary_text_color')
            ->string(100)
            ->nullable()
            ->default(null)
            ->execute();
        $schema->modifyColumn('#__newsletter_templates', 'secondary_title_color')
            ->string(100)
            ->nullable()
            ->default(null)
            ->execute();
        $schema->modifyColumn('#__newsletter_templates', 'secondary_text_color')
            ->string(100)
            ->nullable()
            ->default(null)
            ->execute();
    }
}
