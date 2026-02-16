<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for modules table changes
 *
*/
class Migration20130924000011ComModules extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        $first = false;

        $schema->setTableEngine('#__modules', 'MYISAM');
        $schema->setTableEngine('#__modules_menu', 'MYISAM');

        if ($schema->hasColumn('#__modules', 'numnews')) {
            $schema->dropColumn('#__modules', 'numnews');
            $first = true;
        }
        if ($schema->hasColumn('#__modules', 'control')) {
            $schema->dropColumn('#__modules', 'control');
        }
        if ($schema->hasColumn('#__modules', 'iscore')) {
            $schema->dropColumn('#__modules', 'iscore');
        }
        if (!$schema->hasColumn('#__modules', 'note') && $schema->hasColumn('#__modules', 'title')) {
            $schema->addColumn('#__modules', 'note')
                ->string(255)
                ->notNull()
                ->default('')
                ->after('title')
                ->execute();
        }
        if (
            !$schema->hasColumn('#__modules', 'language')
            && $schema->hasColumn('#__modules', 'client_id')
        ) {
            $schema->addColumn('#__modules', 'language')->char(7)->notNull()->after('client_id');
        }
        $schema->addIndex('#__modules', 'idx_language', 'language');

        if ($schema->hasColumn('#__modules', 'position')) {
            $schema->modifyColumn('#__modules', 'position')
                ->string(50)
                ->notNull()
                ->default('')
                ->execute();
        }
        if ($schema->hasColumn('#__modules', 'title')) {
            $schema->modifyColumn('#__modules', 'title')
                ->string(100)
                ->notNull()
                ->default('')
                ->execute();
        }
        if ($schema->hasColumn('#__modules', 'params')) {
            $schema->modifyColumn('#__modules', 'params')->text()->notNull();
        }
        if ($schema->hasColumn('#__modules', 'checked_out')) {
            $schema->modifyColumn('#__modules', 'checked_out')->integer(10)->unsigned()->notNull()->default(0);
        }
        if ($schema->hasColumn('#__modules', 'access')) {
            $schema->modifyColumn('#__modules', 'access')->integer(10)->unsigned()->notNull()->default(0);
        }
        if (
            !$schema->hasColumn('#__modules', 'publish_up')
            && $schema->hasColumn('#__modules', 'checked_out_time')
        ) {
            $schema->addColumn('#__modules', 'publish_up')
                ->datetime()
                ->notNull()
                ->default('0000-00-00 00:00:00')
                ->after('checked_out_time');
        }
        if (
            !$schema->hasColumn('#__modules', 'publish_down')
            && $schema->hasColumn('#__modules', 'publish_up')
        ) {
            $schema->addColumn('#__modules', 'publish_down')
                ->datetime()
                ->notNull()
                ->default('0000-00-00 00:00:00')
                ->after('publish_up');
        }

        if ($first) {
            $this->db->getQuery(true)
                ->update('#__modules')
                ->set(['module' => 'mod_menu'])
                ->where('module', '=', 'mod_mainmenu')
                ->execute();

            // Add modules_menu entry admin modules that previously didn't need an entry
            $results = $this->db->getQuery(true)
                ->select('id')
                ->from('#__modules')
                ->where('published', '=', '1')
                ->where('client_id', '=', '1')
                ->loadObjectList();

            foreach ($results as $r) {
                // First, make sure it isn't already there
                $ret = $this->db->getQuery(true)
                    ->select('*')
                    ->from('#__modules_menu')
                    ->where('moduleid', '=', (int)$r->id)
                    ->where('menuid', '=', 0)
                    ->first();

                if ($ret) {
                    continue;
                }

                $this->db->getQuery(true)
                    ->insert('#__modules_menu')
                    ->values([
                        'moduleid' => $r->id,
                        'menuid'   => 0
                    ])
                    ->execute();
            }

            // Update menu params (specifically to fix menu_image)
            $results = $this->db->getQuery(true)
                ->select(['id', 'params', 'module'])
                ->from('#__modules')
                ->loadObjectList();

            if (count($results) > 0) {
                foreach ($results as $r) {
                    $params = trim($r->params);
                    if (empty($params) || $params == '{}') {
                        continue;
                    }

                    $array = array();
                    $ar    = explode("\n", $params);

                    foreach ($ar as $a) {
                        $a = trim($a);
                        if (empty($a)) {
                            continue;
                        }

                        $ar2 = explode("=", $a, 2);

                        $array[$ar2[0]] = (isset($ar2[1])) ? $ar2[1] : '';
                    }

                    if ($r->module == 'mod_breadcrumbs') {
                        $array['showHere'] = 0;
                    } elseif ($r->module == 'mod_newsflash') {
                        $this->db->getQuery(true)
                            ->update('#__modules')
                            ->set(['module' => 'mod_articles_news'])
                            ->where('id', '=', $r->id)
                            ->execute();

                        // Update a few param names
                        $array['item_heading'] = 'h4';
                        $array['count']        = isset($array['items']) ? $array['items'] : 0;
                        $array['ordering']     = "a.publish_up";
                        $array['layout']       = "_:vertical";
                        $array['cachemode']    = "itemid";
                    }

                    $this->db->getQuery(true)
                        ->update('#__modules')
                        ->set(['params' => json_encode($array)])
                        ->where('id', '=', $r->id)
                        ->execute();
                }
            }
        }
    }
}
