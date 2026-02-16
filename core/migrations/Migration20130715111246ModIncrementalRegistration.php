<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding mail preference option to incremental registration
  *
**/
class Migration20130715111246ModIncrementalRegistration extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->hasColumn('#__profile_completion_awards', 'mailPreferenceOption')) {
            $schema->addColumn('#__profile_completion_awards', 'mailPreferenceOption')
                ->integer()
                ->notNull()
                ->default(0);
        }

        if (
            !$this->db->getQuery(true)
            ->select('*')
            ->from('#__incremental_registration_labels')
            ->where('field', '=', 'mailPreferenceOption')
            ->exists()
        ) {
            $this->db->getQuery(true)
                ->insert('#__incremental_registration_labels')
                ->columns(['field', 'label'])
                ->values(['mailPreferenceOption', 'E-Mail Updates'])
                ->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->hasColumn('#__profile_completion_awards', 'mailPreferenceOption')) {
            $schema->dropColumn('#__profile_completion_awards', 'mailPreferenceOption');
        }

        $this->db->getQuery(true)
            ->delete('#__incremental_registration_labels')
            ->where('field', '=', 'mailPreferenceOption')
            ->execute();
    }
}
