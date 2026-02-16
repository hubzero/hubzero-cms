<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for ensuring system route plugins are in the correct order
 *
*/
class Migration20150630201210Core extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__extensions')) {
            $query = $this->db->getQuery(true)
                ->select('*')
                ->from('#__extensions')
                ->where('folder', '=', 'system')
                ->order('ordering', 'ASC');
            $system = $query->loadObjectList();

            // Make sure we have results
            if (!$system || count($system) <= 0) {
                return;
            }

            $orderIdx = 0;
            $removals = [];
            $ordering = [
                'disablecache',
                'jquery',
                'certificate',
                'userconsent',
                'authfactors',
                'spamjail',
                'incomplete',
                'unconfirmed',
                'unapproved',
                'password',
            ];

            foreach ($ordering as $order) {
                // Find the item that we're interested in...
                $result = null;
                foreach ($system as $idx => $item) {
                    if ($item->element == $order) {
                        $result     = $item;
                        $removals[] = $idx;
                        break;
                    }
                }

                if (isset($result)) {
                    $this->db->getQuery(true)
                        ->update('#__extensions')
                        ->set(['ordering' => $orderIdx])
                        ->where('extension_id', '=', (int)$result->extension_id)
                        ->execute();
                    $orderIdx++;
                }
            }

            // Take out the items we've already saved
            if (count($removals) > 0) {
                foreach ($removals as $remove) {
                    unset($system[$remove]);
                }
            }

            // That leaves everything else, which we'll keep in their same relative order
            foreach ($system as $plugin) {
                $this->db->getQuery(true)
                    ->update('#__extensions')
                    ->set(['ordering' => $orderIdx])
                    ->where('extension_id', '=', (int)$plugin->extension_id)
                    ->execute();
                $orderIdx++;
            }
        }
    }
}
