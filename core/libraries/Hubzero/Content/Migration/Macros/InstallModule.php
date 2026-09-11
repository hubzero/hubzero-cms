<?php

/**
 * @package    framework
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Content\Migration\Macros;

use Hubzero\Content\Migration\Macro;

/**
 * Migration macro to install a module
 **/
class InstallModule extends Macro
{
    /**
     * Instead of just adding to the extensions table, install module in modules table
     *
     * @param   string  $module    Module name
     * @param   string  $position  Module position
     * @param   bool    $always    If true - always install, false - only install if another module
     *                             of that type isn't present
     * @param   array   $params    Params (if already known)
     * @param   int     $client    Client (site=0, admin=1)
     * @param   mixed   $menus     (int, array) menus to install to (0=all)
     * @return  void
     **/
    public function __invoke($module, $position, $always = true, $params = '', $client = 0, $menus = 0)
    {
        // The query builder binds what it is given, so values go in plain.
        // Quoting them here would put the quotes inside the value.
        $title  = ucfirst($module);
        $module = 'mod_' . strtolower($module);
        $client = (int) $client;
        $access = ($this->db->tableExists('#__extensions')) ? 1 : 0;
        $params = is_string($params) ? $params : json_encode($params);

        if (!$always) {
            $existing = $this->db->getQuery(true)
                ->select('id')
                ->from('#__modules')
                ->whereEquals('module', $module)
                ->value('id');

            if ($existing) {
                return true;
            }
        }

        $last = $this->db->getQuery(true)
            ->select('ordering')
            ->from('#__modules')
            ->whereEquals('position', $position)
            ->order('ordering', 'desc')
            ->limit(1)
            ->value('ordering');

        $this->db->getQuery(true)
            ->insert('#__modules')
            ->set([
                'title'     => $title,
                'content'   => '',
                'ordering'  => $last ? ((int) $last) + 1 : 0,
                'position'  => $position,
                'published' => 1,
                'module'    => $module,
                'access'    => $access,
                'showtitle' => 0,
                'params'    => $params,
                'client_id' => $client,
                'language'  => '*',
            ])
            ->execute();

        $id = $this->db->insertid();

        foreach ((array) $menus as $menu) {
            $this->db->getQuery(true)
                ->insert('#__modules_menu')
                ->set([
                    'moduleid' => $id,
                    'menuid'   => (int) $menu,
                ])
                ->execute();

            $this->log(sprintf('Added module_menu entry for module "%s" to menu "%s"', $module, $menu));
        }

        $this->log(sprintf('Installed module "%s" in position "%s"', $module, $position));

        return true;
    }
}
