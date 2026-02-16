<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding billboards component
 *
 */
class Migration20130930160944ComBillboards extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__billboards')) {
            $schema->createTable('#__billboards')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->integer('collection_id')->nullable()
                ->string('name', 255)->nullable()
                ->string('header', 255)->nullable()
                ->text('text')->nullable()
                ->string('learn_more_text', 255)->nullable()
                ->string('learn_more_target', 255)->nullable()
                ->string('learn_more_class', 255)->nullable()
                ->string('learn_more_location', 255)->nullable()
                ->string('background_img', 255)->nullable()
                ->string('padding', 255)->nullable()
                ->string('alias', 255)->nullable()
                ->text('css')->nullable()
                ->tinyInteger('published')->default(0)
                ->integer('ordering')->nullable()
                ->integer('checked_out')->default(0)
                ->datetime('checked_out_time')->default('0000-00-00 00:00:00')
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();

            $this->db->getQuery(true)
                ->insert('#__billboards')
                ->set([
                    'collection_id'       => 1,
                    'name'                => 'Powered by HUBzero',
                    'header'              => 'Powered by HUBzero',
                    'text'                => 'HUBzero is a platform used to create dynamic web sites '
                        . 'for scientific research and educational activities. '
                        . 'With HUBzero, you can easily publish your research software and related educational '
                        . 'materials on the web.',
                    'learn_more_text'     => 'Learn more &rsaquo;',
                    'learn_more_target'   => 'http://hubzero.org/about',
                    'learn_more_class'    => 'learnmore',
                    'learn_more_location' => 'relative',
                    'background_img'      => 'slideone.png',
                    'padding'             => '15px',
                    'alias'               => 'poweredbyhubzero',
                    'css'                 => '',
                    'published'           => 1,
                    'ordering'            => 1,
                    'checked_out'        => 0,
                    'checked_out_time'    => '0000-00-00 00:00:00'
                ])
                ->execute();

            $this->db->getQuery(true)
                ->insert('#__billboards')
                ->set([
                    'collection_id'       => 1,
                    'name'                => 'Interactive simulation tools',
                    'header'              => 'Interactive simulation tools',
                    'text'                => 'The signature service of a hub is its ability to deliver interactive, '
                        . 'graphical simulation tools through an ordinary web browser. '
                        . 'In the world of portals and cyber-environments, '
                        . 'this capability is '
                        . 'completely unique.',
                    'learn_more_text'     => 'Learn more &rsaquo;',
                    'learn_more_target'   => 'http://hubzero.org/tour/features/#tools',
                    'learn_more_class'    => 'learnmore',
                    'learn_more_location' => 'bottomright',
                    'background_img'      => 'slidetwo.png',
                    'padding'             => '0 0 0 225px',
                    'alias'               => 'interactivesimulationtools',
                    'css'                 => '',
                    'published'           => 1,
                    'ordering'            => 2,
                    'checked_out'        => 0,
                    'checked_out_time'    => '0000-00-00 00:00:00'
                ])
                ->execute();

            $this->db->getQuery(true)
                ->insert('#__billboards')
                ->set([
                    'collection_id'       => 1,
                    'name'                => 'Electronic library of resources',
                    'header'              => 'Electronic library of resources',
                    'text'                => 'Each hub is a place for users to come together and share information. '
                        . 'One important way to accomplish this is by encouraging all users to upload their own tools, '
                        . 'presentations, and other materials onto the hub.<br />',
                    'learn_more_text'     => 'Learn more &rsaquo;',
                    'learn_more_target'   => '/contribute',
                    'learn_more_class'    => 'learnmore',
                    'learn_more_location' => 'relative',
                    'background_img'      => 'slidethree.png',
                    'padding'             => '0 0 0 190px',
                    'alias'               => 'electroniclibraryofresources',
                    'css'                 => "#electroniclibraryofresources h3 {\r\nline-height:2em;\r\n}",
                    'published'           => 1,
                    'ordering'            => 3,
                    'checked_out'        => 0,
                    'checked_out_time'    => '0000-00-00 00:00:00'
                ])
                ->execute();

            $this->db->getQuery(true)
                ->insert('#__billboards')
                ->set([
                    'collection_id'       => 1,
                    'name'                => 'User groups for collaboration',
                    'header'              => 'User groups for collaboration',
                    'text'                => 'Groups are an easy way to share content and conversation, '
                        . 'either privately or with the world. '
                        . 'Many times, a group already exist for a specific interest '
                        . 'or topic. '
                        . 'If you can\'t find one you like, '
                        . 'feel free to start your own.',
                    'learn_more_text'     => 'Learn more &rsaquo;',
                    'learn_more_target'   => '/groups',
                    'learn_more_class'    => 'learnmore',
                    'learn_more_location' => 'bottomright',
                    'background_img'      => 'slidefour.png',
                    'padding'             => '0 0 0 170px',
                    'alias'               => 'usergroupsforcollaboration',
                    'css'                 => "#usergroupsforcollaboration h3 {\r\nline-height:2em;\r\n}",
                    'published'           => 1,
                    'ordering'            => 4,
                    'checked_out'        => 0,
                    'checked_out_time'    => '0000-00-00 00:00:00'
                ])
                ->execute();
        }

        if (!$schema->tableExists('#__billboard_collection')) {
            $schema->createTable('#__billboard_collection')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->string('name', 255)->nullable()
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();

            $this->db->getQuery(true)
                ->insert('#__billboard_collection')
                ->set(['name' => 'Home Default Billboard'])
                ->execute();
        }

        $this->addComponentEntry('Billboards');
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__billboards')) {
            $schema->dropTable('#__billboards');
        }

        if ($schema->tableExists('#__billboard_collection')) {
            $schema->dropTable('#__billboard_collection');
        }

        $this->deleteComponentEntry('Billboards');
    }
}
