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
 * Migration script for getting rid of show_pdf_icon in extension params
 *
 */
class Migration20130517101308ComContent extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__components')) {
            $this->db->getQuery(true)
                ->update('#__components')
                ->set(['params' => Expression::replace(Expression::column('params'), "show_pdf_icon=\n", '')])
                ->where('option', '=', 'com_content')
                ->execute();

            $this->db->getQuery(true)
                ->update('#__components')
                ->set(['params' => Expression::replace(Expression::column('params'), "show_pdf_icon=0\n", '')])
                ->where('option', '=', 'com_content')
                ->execute();

            $this->db->getQuery(true)
                ->update('#__components')
                ->set(['params' => Expression::replace(Expression::column('params'), "show_pdf_icon=1\n", '')])
                ->where('option', '=', 'com_content')
                ->execute();

            $this->db->getQuery(true)
                ->update('#__menu')
                ->set(['params' => Expression::replace(Expression::column('params'), "show_pdf_icon=\n", '')])
                ->whereLike('link', 'com_content')
                ->execute();

            $this->db->getQuery(true)
                ->update('#__menu')
                ->set(['params' => Expression::replace(Expression::column('params'), "show_pdf_icon=0\n", '')])
                ->whereLike('link', 'com_content')
                ->execute();

            $this->db->getQuery(true)
                ->update('#__menu')
                ->set(['params' => Expression::replace(Expression::column('params'), "show_pdf_icon=1\n", '')])
                ->whereLike('link', 'com_content')
                ->execute();
        } else {
            $results = $this->db->getQuery(true)
                ->select(['extension_id', 'params'])
                ->from('#__extensions')
                ->where('element', '=', 'com_content')
                ->loadObjectList();

            if ($results) {
                foreach ($results as $r) {
                    $params = json_decode($r->params);
                    unset($params->show_pdf_icon);

                    $this->db->getQuery(true)
                        ->update('#__extensions')
                        ->set(['params' => json_encode($params)])
                        ->where('extension_id', '=', $r->extension_id)
                        ->execute();
                }
            }

            $results = $this->db->getQuery(true)
                ->select(['id', 'params'])
                ->from('#__menu')
                ->whereLike('link', 'com_content')
                ->loadObjectList();

            if ($results) {
                foreach ($results as $r) {
                    $params = json_decode($r->params);
                    unset($params->show_pdf_icon);

                    $this->db->getQuery(true)
                        ->update('#__menu')
                        ->set(['params' => json_encode($params)])
                        ->where('id', '=', $r->id)
                        ->execute();
                }
            }
        }
    }
}
