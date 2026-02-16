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
 * Migration script for removing Joomla templates
  *
**/
class Migration20130926143200Core extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        $joomlas = array('atomic', 'bluestork', 'beez_20', 'hathor', 'beez5');
        $templates = array();

        if ($schema->tableExists('#__extensions')) {
            $result = $this->db->getQuery(true)
                ->select('*')
                ->from('#__template_styles')
                ->where('home', '=', 0)
                ->loadObjectList();

            $templates = array();
            foreach ($result as $r) {
                if (in_array($r->template, $joomlas)) {
                    $templates[] = $r->template;
                }
            }

            if (!empty($templates)) {
                $this->db->getQuery(true)
                    ->delete('#__extensions')
                    ->where('type', '=', 'template')
                    ->whereIn('element', $templates)
                    ->execute();
            }
        }

        if ($schema->tableExists('#__template_styles')) {
            if (empty($templates)) {
                $templates = $joomlas;
            }
            $this->db->getQuery(true)
                ->delete('#__template_styles')
                ->whereIn('template', $templates)
                ->where('home', '=', 0)
                ->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__template_styles')) {
            $beez20Params = '{"wrapperSmall":"53","wrapperLarge":"72",'
                . '"logo":"images\\/joomla_black.gif","sitetitle":"Joomla!",'
                . '"sitedescription":"Open Source Content Management",'
                . '"navposition":"left","templatecolor":"personal","html5":"0"}';
            $beez5Params = '{"wrapperSmall":"53","wrapperLarge":"72",'
                . '"logo":"images\\/sampledata\\/fruitshop\\/fruits.gif",'
                . '"sitetitle":"Joomla!","sitedescription":"Open Source Content Management",'
                . '"navposition":"left","html5":"0"}';

            $viewlevels = [
                [2, 'bluestork', '1', '0', 'Bluestork - Default', '{"useRoundedCorners":"1","showSiteName":"0"}'],
                [3, 'atomic', '0', '0', 'Atomic - Default', '{}'],
                [4, 'beez_20', 0, 0, 'Beez2 - Default', $beez20Params],
                [5, 'hathor', '1', '0', 'Hathor - Default', '{"showSiteName":"0","colourChoice":"","boldText":"0"}'],
                [6, 'beez5', 0, 0, 'Beez5 - Default', $beez5Params]
            ];

            foreach ($viewlevels as $level) {
                $this->db->getQuery(true)
                    ->insertOrIgnore('#__template_styles')
                    ->values([
                        'id'        => $level[0],
                        'template'  => $level[1],
                        'client_id' => $level[2],
                        'home'      => $level[3],
                        'title'     => $level[4],
                        'params'    => $level[5]
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
                    ->where('template', '=', $r->element);

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
        }
    }
}
