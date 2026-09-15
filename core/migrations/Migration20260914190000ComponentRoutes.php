<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2025 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Give every site component the address it should have had
 *
 * The component menu was maintained by hand, so it drifted. A component with no
 * entry either does not route at all or routes by its own name with no Itemid,
 * and an Itemid is what the modules on a page and its template style hang from.
 * Nothing reported either state: the page just quietly had less than it should.
 *
 * From here on the entry is made when the component is installed, by the same
 * macro that has always made the admin one. This is the one-time catch-up for
 * everything installed before that.
 *
 * A component qualifies if it is enabled, has a site/ directory, and is not on
 * the list below. The directory test is necessary and not sufficient: com_cron,
 * com_saml and com_mailto all have one, and none of them is a page anybody
 * should be given a link to. There is no structural way to tell a page from an
 * endpoint, so the ones that are not pages are named.
 *
 * com_dataviewer is on the list for a third reason: it has a site half and
 * answers 404 to every spelling of its own name, having no controller a
 * generic route can reach. An entry for it would be an address pointing at a
 * 404, which is worse than no address.
 **/
class Migration20260914190000ComponentRoutes extends Base
{
    /**
     * Components with a site half that is not a page
     *
     * Machinery, or an endpoint something else talks to. com_content is here
     * for a different reason: it routes through articles and categories and has
     * never answered at /content.
     *
     * @var  array
     **/
    protected $notPages = array(
        'com_content',
        'com_cron',
        'com_dataviewer',
        'com_help',
        'com_mailto',
        'com_media',
        'com_messages',
        'com_oaipmh',
        'com_oauth',
        'com_redirect',
        'com_saml',
        'com_system',
    );

    /**
     * Up
     **/
    public function up()
    {
        if (!$this->db->tableExists('#__extensions') || !$this->db->tableExists('#__menu')) {
            return;
        }

        $rows = $this->db->getQuery(true)
            ->select('extension_id')
            ->select('element')
            ->select('enabled')
            ->from('#__extensions')
            ->whereEquals('type', 'component')
            ->fetch();

        $added = 0;

        foreach ($rows as $row) {
            $element = is_object($row) ? $row->element : $row['element'];
            $id      = (int) (is_object($row) ? $row->extension_id : $row['extension_id']);
            $enabled = (int) (is_object($row) ? $row->enabled : $row['enabled']);

            if (!$enabled || substr($element, 0, 4) !== 'com_') {
                continue;
            }

            if (in_array($element, $this->notPages, true)) {
                continue;
            }

            if (!is_dir(PATH_CORE . '/components/' . $element . '/site')) {
                continue;
            }

            $alias = substr($element, 4);

            // Anything already reachable under that name is left alone, whichever
            // menu it is filed under - a hub may have made its own.
            $exists = $this->db->getQuery(true)
                ->select('id')
                ->from('#__menu')
                ->whereEquals('client_id', 0)
                ->whereEquals('path', $alias)
                ->value('id');

            if ($exists) {
                continue;
            }

            $this->addComponentEntry(ucfirst($alias), $element, $enabled, '', false, true);

            $made = $this->db->getQuery(true)
                ->select('id')
                ->from('#__menu')
                ->whereEquals('client_id', 0)
                ->whereEquals('path', $alias)
                ->value('id');

            if ($made) {
                $added++;
                $this->log('Gave ' . $element . ' the address /' . $alias);
            }
        }

        $this->log($added
            ? 'Added ' . $added . ' component route' . ($added == 1 ? '' : 's')
            : 'Every site component already had an address');
    }

    /**
     * Down
     *
     * The routes are not removed. Which of them this migration added and which
     * a hub has since come to depend on is not something it can tell apart, and
     * taking away an address is worse than leaving one.
     **/
    public function down()
    {
        $this->log('Component routes are left in place; removing an address breaks links');
    }
}
