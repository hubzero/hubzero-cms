<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for forcing preferred settings for content handlers and jQuery plugins
  *
**/
class Migration20150224205200PlgContent extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__extensions')) {
            $plugin = $this->db->getQuery(true)
                ->select('*')
                ->from('#__extensions')
                ->where('type', '=', 'plugin')
                ->where('folder', '=', 'system')
                ->where('element', '=', 'jquery')
                ->first();

            if ($plugin) {
                $params = new \Hubzero\Config\Registry($plugin->params);
                $params->set('jquery', 1);
                $params->set('jqueryui', 1);
                $params->set('jqueryfb', 1);
                $params->set('activateAdmin', 0);
                $params->set('noconflictAdmin', 0);
                $params->set('activateSite', 1);
                $params->set('noconflictSite', 0);

                $this->db->getQuery(true)
                    ->update('#__extensions')
                    ->set(['params' => $params->toString()])
                    ->where('extension_id', '=', $plugin->extension_id)
                    ->execute();
            }

            $plugin = $this->db->getQuery(true)
                ->select('*')
                ->from('#__extensions')
                ->where('type', '=', 'plugin')
                ->where('folder', '=', 'content')
                ->where('element', '=', 'formatwiki')
                ->first();

            if ($plugin) {
                $params = new \Hubzero\Config\Registry($plugin->params);
                $params->set('applyFormat', 1);
                $params->set('convertFormat', 1);

                $this->db->getQuery(true)
                    ->update('#__extensions')
                    ->set(['params' => $params->toString()])
                    ->where('extension_id', '=', $plugin->extension_id)
                    ->execute();
            }

            $plugin = $this->db->getQuery(true)
                ->select('*')
                ->from('#__extensions')
                ->where('type', '=', 'plugin')
                ->where('folder', '=', 'content')
                ->where('element', '=', 'formathtml')
                ->first();

            if ($plugin) {
                $params = new \Hubzero\Config\Registry($plugin->params);
                $params->set('applyFormat', 1);
                $params->set('convertFormat', 0);
                $params->set('sanitizeBefore', 0);

                $this->db->getQuery(true)
                    ->update('#__extensions')
                    ->set(['params' => $params->toString()])
                    ->where('extension_id', '=', $plugin->extension_id)
                    ->execute();

                if (!$plugin->enabled) {
                    $this->enablePlugin('content', 'formathtml');
                }
            } else {
                $params = new \Hubzero\Config\Registry();
                $params->set('applyFormat', 1);
                $params->set('convertFormat', 0);
                $params->set('sanitizeBefore', 0);

                $this->addPluginEntry('content', 'formathtml', 1, $params->toString());
            }
        }
    }
}
