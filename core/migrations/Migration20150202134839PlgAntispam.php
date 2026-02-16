<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script to move existing antispam plugins and add a couple more
  *
**/
class Migration20150202134839PlgAntispam extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        $params = '';

        if ($schema->tableExists('#__extensions')) {
            // Move the existing plugin entries when possible to preserve params,
            // otherwise add the entry
            foreach (array('akismet', 'mollom', 'spamassassin') as $plg) {
                $id = $this->db->getQuery(true)
                    ->select('extension_id')
                    ->from('#__extensions')
                    ->where('type', '=', 'plugin')
                    ->where('folder', '=', 'content')
                    ->where('element', '=', $plg)
                    ->value('extension_id');
                if ($id) {
                    $this->db->getQuery(true)
                        ->update('#__extensions')
                        ->set([
                            'folder' => 'antispam',
                            'name'   => 'plg_antispam_' . $plg
                        ])
                        ->where('extension_id', '=', $id)
                        ->execute();
                } else {
                    $this->addPluginEntry('antispam', $plg, 0);
                }
            }

            // Get the params from the old antispam plugin. We need the badwords list for the 'blacklist' plugin.
            $params = $this->db->getQuery(true)
                ->select('params')
                ->from('#__extensions')
                ->where('type', '=', 'plugin')
                ->where('folder', '=', 'content')
                ->where('element', '=', 'antispam')
                ->value('params');
        }

        if ($params) {
            $this->addPluginEntry('antispam', 'blacklist', 0, $params);
        } else {
            $this->addPluginEntry('antispam', 'blacklist', 0);
        }

        $this->addPluginEntry('antispam', 'linkrife', 0);
        $this->addPluginEntry('antispam', 'bayesian', 0);

        if (!$schema->tableExists('#__antispam_token_probs')) {
            $schema->createTable('#__antispam_token_probs')
                ->id()
                ->string('token', 256)
                ->float('prob')->default(0.00)
                ->float('prev_prob')->default(0.00)
                ->integer('in_ham')->default(0)
                ->integer('in_spam')->default(0)
                ->string('provider', 256)->nullable()
                ->string('param1', 256)
                ->string('param2', 256)
                ->datetime('created')->default('0000-00-00 00:00:00')
                ->datetime('modified')->default('0000-00-00 00:00:00')
                ->engine('InnoDB')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__antispam_token_counts')) {
            $schema->createTable('#__antispam_token_counts')
                ->id()
                ->integer('good_count')->default(0)
                ->integer('bad_count')->default(0)
                ->engine('InnoDB')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__antispam_message_hashes')) {
            $schema->createTable('#__antispam_message_hashes')
                ->id()
                ->string('hash', 256)
                ->engine('InnoDB')
                ->charset('utf8')
                ->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__extensions')) {
            // Move the existing plugin entries when possible to preserve params,
            // otherwise add the entry
            foreach (array('akismet', 'mollom', 'spamassassin') as $plg) {
                $id = $this->db->getQuery(true)
                    ->select('extension_id')
                    ->from('#__extensions')
                    ->where('type', '=', 'plugin')
                    ->where('folder', '=', 'antispam')
                    ->where('element', '=', $plg)
                    ->value('extension_id');
                if ($id) {
                    $this->db->getQuery(true)
                        ->update('#__extensions')
                        ->set([
                            'folder' => 'content',
                            'name'   => 'plg_content_' . $plg
                        ])
                        ->where('extension_id', '=', $id)
                        ->execute();
                } else {
                    $this->addPluginEntry('content', $plg, 0);
                }
            }
        }

        $this->deletePluginEntry('antispam', 'blacklist');
        $this->deletePluginEntry('antispam', 'linkrife');
        $this->deletePluginEntry('antispam', 'bayesian');

        if ($schema->tableExists('#__antispam_token_probs')) {
            $schema->dropTable('#__antispam_token_probs');
        }

        if ($schema->tableExists('#__antispam_token_counts')) {
            $schema->dropTable('#__antispam_token_counts');
        }

        if ($schema->tableExists('#__antispam_message_hashes')) {
            $schema->dropTable('#__antispam_message_hashes');
        }
    }
}
