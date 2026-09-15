<?php

/**
 * @package    framework
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Content\Migration\Macros;

use Hubzero\Content\Migration\Macro;

/**
 * Migration macro to enable a component
 **/
class EnableComponent extends Macro
{
    /**
     * Enable component
     *
     * @param   string  $element  Element
     * @return  bool
     **/
    public function __invoke($element)
    {
        $table = '#__extensions';
        if ($this->db->tableExists('#__components')) {
            $table = '#__components';
        }

        if ($this->db->tableExists($table)) {
            $enabled = 1;

            $query = $this->db->getQuery()
                ->update($table)
                ->set(array(
                    'enabled' => $enabled
                ))
                ->whereEquals('element', $element)
                ->toString();

            $this->db->setQuery($query);

            if ($this->db->query()) {
                $this->log(sprintf('Set component "%s" status to "%s"', $element, $enabled));
                $this->setRouteState($element, 1);
                return true;
            }
        }

        return false;
    }

    /**
     * Keep the component's own address in step with the component
     *
     * The route is a menu item, and a menu item can be published or not. A hub
     * that switches a component off and leaves its address answering has a page
     * for something it decided not to run.
     *
     * @param   string  $element  com_xyz
     * @param   int     $state    1 published, 0 not
     * @return  void
     */
    protected function setRouteState($element, $state)
    {
        if (!$this->db->tableExists('#__menu') || substr($element, 0, 4) !== 'com_') {
            return;
        }

        $alias = substr($element, 4);

        $menutypes = array();

        if ($this->db->tableHasField('#__menu_types', 'type')) {
            foreach ($this->db->getQuery(true)
                ->select('menutype')
                ->from('#__menu_types')
                ->whereEquals('type', 'component')
                ->fetch() as $row) {
                $menutypes[] = is_object($row) ? $row->menutype : $row['menutype'];
            }
        }

        if (!$menutypes) {
            $menutypes = array('components');
        }

        // Only the generated entry. A hub that made its own menu item for this
        // component made it on purpose and it is not this macro's to publish.
        $affected = 0;

        foreach ($menutypes as $menutype) {
            $this->db->getQuery()
                ->update('#__menu')
                ->set(array('published' => (int) $state))
                ->whereEquals('client_id', 0)
                ->whereEquals('menutype', $menutype)
                ->whereEquals('alias', $alias)
                ->execute();

            $affected++;
        }

        if ($affected) {
            $this->log(sprintf(
                '%s the address /%s',
                $state ? 'Restored' : 'Withdrew',
                $alias
            ));
        }
    }
}
