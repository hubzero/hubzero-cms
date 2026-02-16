<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;
use Hubzero\Database\Expression;

/**
 * Migration script for other changes
 *
*/
class Migration20130924000013Core extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        // Change config offset from '0' to 'UTC'!
        // @FIXME: should we actually set this based on offset, or assume 0?
        $configuration = file_get_contents(PATH_ROOT . DS . 'configuration.php');
        $configuration = preg_replace('/(var \$offset[\s]*=[\s]*[\'"]*)([\-0-9]+)([\'"]*)/', '$1UTC$3', $configuration);
        file_put_contents(PATH_ROOT . DS . 'configuration.php', $configuration);

        $schema->setTableEngine('#__core_log_searches', 'MYISAM');

        if ($schema->hasColumn('#__core_log_searches', 'hits')) {
            $schema->modifyColumn('#__core_log_searches', 'hits')->integer(10)->unsigned()->notNull()->default(0);
        }

        if ($schema->tableExists('#__sections')) {
            $schema->dropTable('#__sections');
        }

        if ($schema->hasColumn('#__categories', 'section')) {
            $schema->dropColumn('#__categories', 'section');
        }

        if (!$schema->tableExists('#__redirect_links')) {
            $schema->createTable('#__redirect_links')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->string('old_url', 255)
                ->string('new_url', 255)
                ->string('referer', 150)
                ->string('comment', 255)
                ->unsignedInteger('hits')->default(0)
                ->tinyInteger('published')
                ->datetime('created_date')->default('0000-00-00 00:00:00')
                ->datetime('modified_date')->default('0000-00-00 00:00:00')
                ->primaryKey('id')
                ->uniqueIndex('idx_link_old', 'old_url')
                ->index('idx_link_modifed', 'modified_date')
                ->engine('MYISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__user_notes')) {
            $schema->createTable('#__user_notes')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->unsignedInteger('user_id')->default(0)
                ->unsignedInteger('catid')->default(0)
                ->string('subject', 100)->default('')
                ->text('body')
                ->tinyInteger('state')->default(0)
                ->unsignedInteger('checked_out')->default(0)
                ->datetime('checked_out_time')->default('0000-00-00 00:00:00')
                ->unsignedInteger('created_user_id')->default(0)
                ->datetime('created_time')->default('0000-00-00 00:00:00')
                ->unsignedInteger('modified_user_id')
                ->datetime('modified_time')->default('0000-00-00 00:00:00')
                ->datetime('review_time')->default('0000-00-00 00:00:00')
                ->datetime('publish_up')->default('0000-00-00 00:00:00')
                ->datetime('publish_down')->default('0000-00-00 00:00:00')
                ->primaryKey('id')
                ->index('idx_user_id', 'user_id')
                ->index('idx_category_id', 'catid')
                ->engine('MYISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__associations')) {
            $schema->createTable('#__associations')
                ->string('id', 50)
                ->string('context', 50)
                ->char('key', 32)
                ->primaryKey(['context', 'id'])
                ->index('idx_key', 'key')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__overrider')) {
            $schema->createTable('#__overrider')
                ->integer('id', ['autoIncrement' => true])
                ->string('constant', 255)
                ->text('string')
                ->string('file', 255)
                ->primaryKey('id')
                ->engine('MYISAM')
                ->charset('utf8')
                ->execute();
        }

        if ($schema->tableExists('#__core_log_items')) {
            $schema->dropTable('#__core_log_items');
        }

        if ($schema->tableExists('#__stats_agents')) {
            $schema->dropTable('#__stats_agents');
        }

        if ($schema->tableExists('#__migration_backlinks')) {
            $schema->dropTable('#__migration_backlinks');
        }

        if (!$schema->tableExists('#__schemas')) {
            $schema->createTable('#__schemas')
                ->integer('extension_id')
                ->string('version_id', 20)
                ->primaryKey(['extension_id', 'version_id'])
                ->engine('MYISAM')
                ->charset('utf8')
                ->execute();

            $this->db->getQuery(true)
                ->insert('#__schemas')
                ->values([
                    'extension_id' => 700,
                    'version_id'   => '2.5.11'
                ])
                ->execute();
        }

        $schema->setTableEngine('#__session', 'MYISAM');

        // Modify session_id column and set as primary key
        if ($schema->hasColumn('#__session', 'session_id')) {
            $schema->table('#__session')->alter()
                ->modifyColumn('session_id')
                ->string(200)
                ->notNull()
                ->default('')
                ->dropPrimaryKey()
                ->addPrimaryKey('session_id')
                ->execute();
        }
        if (
            $schema->hasColumn('#__session', 'client_id')
            && $schema->hasColumn('#__session', 'session_id')
        ) {
            $schema->modifyColumn('#__session', 'client_id')->tinyInteger()->unsigned()->notNull()->default(0);
        }
        if ($schema->hasColumn('#__session', 'guest') && $schema->hasColumn('#__session', 'client_id')) {
            $schema->modifyColumn('#__session', 'guest')->tinyInteger(4)->unsigned()->nullable()->default(1);
        }
        if ($schema->hasColumn('#__session', 'username') && $schema->hasColumn('#__session', 'userid')) {
            $schema->modifyColumn('#__session', 'username')
                ->string(150)
                ->default('')
                ->execute();
        }
        if ($schema->hasColumn('#__session', 'data') && $schema->hasColumn('#__session', 'time')) {
            $schema->modifyColumn('#__session', 'data')->mediumText()->nullable();
        }
        if ($schema->hasColumn('#__session', 'gid')) {
            $schema->dropColumn('#__session', 'gid');
        }

        if (!$schema->tableExists('#__template_styles')) {
            $schema->createTable('#__template_styles')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->string('template', 50)->default('')
                ->unsignedTinyInteger('client_id')->default(0)
                ->char('home', 7)->default('0')
                ->string('title', 255)->default('')
                ->text('params')
                ->primaryKey('id')
                ->index('idx_template', 'template')
                ->index('idx_home', 'home')
                ->engine('MYISAM')
                ->charset('utf8')
                ->execute();

            if ($schema->tableExists('#__templates_menu')) {
                $beez20Params = '{"wrapperSmall":"53","wrapperLarge":"72",'
                    . '"logo":"images\\/joomla_black.gif","sitetitle":"Joomla!",'
                    . '"sitedescription":"Open Source Content Management",'
                    . '"navposition":"left","templatecolor":"personal","html5":"0"}';
                $beez5Params = '{"wrapperSmall":"53","wrapperLarge":"72",'
                    . '"logo":"images\\/sampledata\\/fruitshop\\/fruits.gif","sitetitle":"Joomla!",'
                    . '"sitedescription":"Open Source Content Management",'
                    . '"navposition":"left","html5":"0"}';
                $styles = [
                    [2, 'bluestork', '1', '0', 'Bluestork - Default', '{"useRoundedCorners":"1","showSiteName":"0"}'],
                    [3, 'atomic', '0', '0', 'Atomic - Default', '{}'],
                    [4, 'beez_20', 0, 0, 'Beez2 - Default', $beez20Params],
                    [
                        5,
                        'hathor',
                        '1',
                        '0',
                        'Hathor - Default',
                        '{"showSiteName":"0","colourChoice":"","boldText":"0"}',
                    ],
                    [6, 'beez5', 0, 0, 'Beez5 - Default', $beez5Params]
                ];

                foreach ($styles as $style) {
                    $this->db->getQuery(true)
                        ->insertOrIgnore('#__template_styles')
                        ->values([
                            'id'        => $style[0],
                            'template'  => $style[1],
                            'client_id' => $style[2],
                            'home'      => $style[3],
                            'title'     => $style[4],
                            'params'    => $style[5]
                        ])
                        ->execute();
                }

                // Insert all templates from extensions
                $result = $this->db->getQuery(true)
                    ->select('*')
                    ->from('#__extensions')
                    ->where('type', '=', 'template')
                    ->loadObjectList();

                foreach ($result as $r) {
                    $query = $this->db->getQuery(true)
                        ->select('id')
                        ->from('#__template_styles')
                        ->where('template', '=', $r->element)
                        ;

                    if ($query->exists()) {
                        continue;
                    }

                    $this->db->getQuery(true)
                        ->insert('#__template_styles')
                        ->values([
                            'template'  => $r->element,
                            'client_id' => $r->client_id,
                            'home'      => '0',
                            'title'     => ucfirst($r->element),
                            'params'    => '{}'
                        ])
                        ->execute();
                }

                // Update current templates to have home = 1 (one for site and one for admin)
                $result = $this->db->getQuery(true)
                    ->select(['template', 'client_id'])
                    ->from('#__templates_menu')
                    ->loadObjectList();

                foreach ($result as $r) {
                    $this->db->getQuery(true)
                        ->update('#__template_styles')
                        ->set(['home' => '1'])
                        ->where('template', '=', $r->template)
                        ->execute();
                }

                // Now make sure something is set for the admin template
                $query = $this->db->getQuery(true)
                    ->select('id')
                    ->from('#__template_styles')
                    ->where('client_id', '=', 1)
                    ->where('home', '=', '1')
                    ;

                if ($query->doesntExist()) {
                    $this->db->getQuery(true)
                        ->update('#__template_styles')
                        ->set(['home' => '1'])
                        ->where('template', '=', 'hubbasicadmin')
                        ->execute();
                }
            }
        }

        if ($schema->tableExists('#__templates_menu')) {
            $schema->dropTable('#__templates_menu');
        }

        if (!$schema->tableExists('#__updates')) {
            $schema->createTable('#__updates')
                ->integer('update_id', ['autoIncrement' => true])
                ->integer('update_site_id')->default(0)
                ->integer('extension_id')->default(0)
                ->integer('categoryid')->default(0)
                ->string('name', 100)->default('')
                ->text('description')
                ->string('element', 100)->default('')
                ->string('type', 20)->default('')
                ->string('folder', 20)->default('')
                ->tinyInteger('client_id')->default(0)
                ->string('version', 10)->default('')
                ->text('data')
                ->text('detailsurl')
                ->text('infourl')
                ->primaryKey('update_id')
                ->engine('MYISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__update_sites')) {
            $schema->createTable('#__update_sites')
                ->integer('update_site_id', ['autoIncrement' => true])
                ->string('name', 100)->default('')
                ->string('type', 20)->default('')
                ->text('location')
                ->integer('enabled')->default(0)
                ->bigInteger('last_check_timestamp')->nullable()->default(0)
                ->primaryKey('update_site_id')
                ->engine('MYISAM')
                ->charset('utf8')
                ->execute();

            $updateSites = [
                [1, 'Joomla Core', 'collection', 'http://update.joomla.org/core/list.xml', 1, 0],
                [2, 'Joomla Extension Directory', 'collection', 'http://update.joomla.org/jed/list.xml', 1, 0],
                [
                    3,
                    'Accredited Joomla! Translations',
                    'collection',
                    'http://update.joomla.org/language/translationlist.xml',
                    1,
                    0
                ]
            ];

            foreach ($updateSites as $site) {
                $this->db->getQuery(true)
                    ->insert('#__update_sites')
                    ->values([
                        'update_site_id'       => $site[0],
                        'name'                 => $site[1],
                        'type'                 => $site[2],
                        'location'             => $site[3],
                        'enabled'              => $site[4],
                        'last_check_timestamp' => $site[5]
                    ])
                    ->execute();
            }
        }

        if (!$schema->tableExists('#__update_sites_extensions')) {
            $schema->createTable('#__update_sites_extensions')
                ->integer('update_site_id')->default(0)
                ->integer('extension_id')->default(0)
                ->primaryKey(['update_site_id', 'extension_id'])
                ->engine('MYISAM')
                ->charset('utf8')
                ->execute();

            $updateSitesExtensions = [[1, 700], [2, 700], [3, 600]];

            foreach ($updateSitesExtensions as $ext) {
                $this->db->getQuery(true)
                    ->insert('#__update_sites_extensions')
                    ->values([
                        'update_site_id' => $ext[0],
                        'extension_id'   => $ext[1]
                    ])
                    ->execute();
            }
        }

        if (!$schema->tableExists('#__update_categories')) {
            $schema->createTable('#__update_categories')
                ->integer('categoryid', ['autoIncrement' => true])
                ->string('name', 20)->default('')
                ->text('description')
                ->integer('parent')->default(0)
                ->integer('updatesite')->default(0)
                ->primaryKey('categoryid')
                ->engine('MYISAM')
                ->charset('utf8')
                ->execute();
        }
    }
}
