<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for incorrect link in default discover page content
  *
**/
class Migration20190327000000Discover extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__content')) {
            $pages = $this->db->getQuery(true)
                ->select('*')
                ->from('#__content')
                ->where('title', '=', 'Discover')
                ->where('alias', '=', 'discover')
                ->whereLike('introtext', 'href="/store"')
                ->loadObjectList();

            if ($pages) {
                foreach ($pages as $page) {
                    $content = $page->introtext;
                    $content = str_replace('href="/store"', 'href="/storefront"', $content);

                    $success = $this->db->getQuery(true)
                        ->update('#__content')
                        ->set(['introtext' => $content])
                        ->where('id', '=', $page->id)
                        ->execute();

                    if ($success) {
                        $this->log('Updated Store link from /store to /storefront in default Discover page');
                    } else {
                        $this->log('Failed to update Store link in default Discover page', 'warning');
                    }
                }
            }
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__content')) {
            $pages = $this->db->getQuery(true)
                ->select('*')
                ->from('#__content')
                ->where('title', '=', 'Discover')
                ->where('alias', '=', 'discover')
                ->whereLike('introtext', 'href="/storefront"')
                ->loadObjectList();

            if ($pages) {
                foreach ($pages as $page) {
                    $content = $page->introtext;
                    $content = str_replace('href="/storefront"', 'href="/store"', $content);

                    $success = $this->db->getQuery(true)
                        ->update('#__content')
                        ->set(['introtext' => $content])
                        ->where('id', '=', $page->id)
                        ->execute();

                    if ($success) {
                        $this->log('Updated Store link from /storefront to /store in default Discover page');
                    } else {
                        $this->log('Failed to update Store link in default Discover page', 'warning');
                    }
                }
            }
        }
    }
}
