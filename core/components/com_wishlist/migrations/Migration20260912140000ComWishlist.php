<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2025 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Wishlist\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Repair wish list names that were stored as an untranslated language key
 *
 * The publications plugin named a new list with Lang::txt() of a key nobody
 * had defined, so the name it stored was the literal string
 * "COM_WISHLIST_NAME_RESOURCE" followed by the publication's alias. The name
 * is written once, when the list is created, so every list made since carries
 * it and will go on showing it until it is corrected here.
 *
 * Only rows that still hold the untranslated key, and only the key: whatever
 * follows it is the publication's alias and is right.
 **/
class Migration20260912140000ComWishlist extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        if (!$this->db->tableExists('#__wishlist')) {
            return;
        }

        $this->db->setQuery(
            "UPDATE `#__wishlist`
                SET `title` = CONCAT('Publication', SUBSTRING(`title`, 27))
              WHERE `category` = 'publication'
                AND `title` LIKE 'COM\\_WISHLIST\\_NAME\\_RESOURCE %'"
        );
        $this->db->query();
    }

    /**
     * Down
     **/
    public function down()
    {
        if (!$this->db->tableExists('#__wishlist')) {
            return;
        }

        $this->db->setQuery(
            "UPDATE `#__wishlist`
                SET `title` = CONCAT('COM_WISHLIST_NAME_RESOURCE', SUBSTRING(`title`, 12))
              WHERE `category` = 'publication'
                AND `title` LIKE 'Publication %'"
        );
        $this->db->query();
    }
}
