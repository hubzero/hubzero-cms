<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for deleting com_contact
**/
class Migration20140110132511ComContact extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        $id = $this->db->getQuery(true)
            ->select('extension_id')
            ->from('#__extensions')
            ->where('type', '=', 'component')
            ->where('element', '=', 'com_contact')
            ->value('extension_id');

        if ($id) {
            $this->deleteComponentEntry('contact');

            $this->deletePluginEntry('search', 'contacts');
            $this->deletePluginEntry('user', 'contactcreator');

            $schema->dropTable('#__contact_details');
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        $id = $this->db->getQuery(true)
            ->select('extension_id')
            ->from('#__extensions')
            ->where('type', '=', 'component')
            ->where('element', '=', 'com_contact')
            ->value('extension_id');

        if (!$id) {
            $this->addComponentEntry('contact');

            $this->addPluginEntry('search', 'contacts', 0);
            $this->addPluginEntry('user', 'contactcreator');

            if (!$schema->tableExists('#__contact_details')) {
                $schema->createTable('#__contact_details')
                    ->integer('id', ['autoIncrement' => true])
                    ->string('name', 255)->default('')
                    ->string('alias', 255)->default('')
                    ->string('con_position', 255)->nullable()
                    ->text('address')->nullable()
                    ->string('suburb', 100)->nullable()
                    ->string('state', 100)->nullable()
                    ->string('country', 100)->nullable()
                    ->string('postcode', 100)->nullable()
                    ->string('telephone', 255)->nullable()
                    ->string('fax', 255)->nullable()
                    ->mediumText('misc')->nullable()
                    ->string('image', 255)->nullable()
                    ->string('imagepos', 20)->nullable()
                    ->string('email_to', 255)->nullable()
                    ->unsignedTinyInteger('default_con')->default(0)
                    ->tinyInteger('published')->default(0)
                    ->unsignedInteger('checked_out')->default(0)
                    ->datetime('checked_out_time')->default('0000-00-00 00:00:00')
                    ->integer('ordering')->default(0)
                    ->text('params')
                    ->integer('user_id')->default(0)
                    ->integer('catid')->default(0)
                    ->unsignedInteger('access')->default(0)
                    ->string('mobile', 255)->default('')
                    ->string('webpage', 255)->default('')
                    ->string('sortname1', 255)
                    ->string('sortname2', 255)
                    ->string('sortname3', 255)
                    ->char('language', 7)
                    ->datetime('created')->default('0000-00-00 00:00:00')
                    ->unsignedInteger('created_by')->default(0)
                    ->string('created_by_alias', 255)->default('')
                    ->datetime('modified')->default('0000-00-00 00:00:00')
                    ->unsignedInteger('modified_by')->default(0)
                    ->text('metakey')
                    ->text('metadesc')
                    ->text('metadata')
                    ->unsignedTinyInteger('featured')->default(0)
                    ->string('xreference', 50)
                    ->datetime('publish_up')->default('0000-00-00 00:00:00')
                    ->datetime('publish_down')->default('0000-00-00 00:00:00')
                    ->primaryKey('id')
                    ->index('idx_access', 'access')
                    ->index('idx_checkout', 'checked_out')
                    ->index('idx_state', 'published')
                    ->index('idx_catid', 'catid')
                    ->index('idx_createdby', 'created_by')
                    ->index('idx_featured_catid', ['featured', 'catid'])
                    ->index('idx_language', 'language')
                    ->index('idx_xreference', 'xreference')
                    ->engine('MyISAM')
                    ->charset('utf8')
                    ->execute();
            }
        }
    }
}
