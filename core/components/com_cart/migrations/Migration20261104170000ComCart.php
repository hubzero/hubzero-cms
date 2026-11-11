<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Cart\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script to store the downloader's IP address as text
 *
 * #__cart_downloads.dIp held INET_ATON() of the address, an unsigned
 * integer. INET_ATON() of an IPv6 address is NULL, so IPv6 downloads were
 * logged with no address. It becomes varchar(45), the address as text, as
 * the other IP columns are; the existing integers are turned back into
 * dotted IPv4.
 **/
class Migration20261104170000ComCart extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        if (!$this->db->tableExists('#__cart_downloads')) {
            return;
        }

        $fields = $this->db->getTableColumns('#__cart_downloads', false);

        if (!isset($fields['dIp'])) {
            return;
        }

        if (preg_match('/int/i', $fields['dIp']->Type)) {
            // The integers become their decimal digits...
            $this->db->setQuery("ALTER TABLE `#__cart_downloads` MODIFY `dIp` varchar(45) NULL DEFAULT NULL");
            $this->db->query();
        }

        // ...and those become addresses; a run that stopped between the two
        // is finished here, since an address as text is never only digits
        $this->db->setQuery("UPDATE `#__cart_downloads` SET `dIp` = INET_NTOA(`dIp`) WHERE `dIp` REGEXP '^[0-9]+$'");
        $this->db->query();
    }

    /**
     * Down
     *
     * IPv6 addresses have no integer form, so leave the column as it is
     **/
    public function down() {}
}
