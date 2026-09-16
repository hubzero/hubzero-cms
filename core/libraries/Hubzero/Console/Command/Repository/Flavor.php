<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Console\Command\Repository;

use Hubzero\Console\Command\Base;
use Hubzero\Console\Command\CommandInterface;
use Hubzero\Content\Migration\Base as Migration;
use Hubzero\Facades\App;

/**
 * The flavor of a hub: whether it runs simulation tools
 *
 * A hub is either the CMS on its own - the default - or the CMS with the
 * tool middleware behind it, which is what the "full" flavor names. The
 * difference is a handful of pieces that exist only to launch and manage
 * tools: the tools component and its usage metrics, the dashboard modules
 * that list a member's tools and sessions, the knowledge base's tool
 * articles, and the "tools" resource type. Setting a flavor turns those on
 * or off together, so a hub without a middleware does not advertise what it
 * cannot do, and one that gains a middleware gets all of it back with one
 * command.
 *
 * Nothing is deleted either way. A flavor is a switch, and switching back
 * restores what was there.
 **/
class Flavor extends Base implements CommandInterface
{
    /**
     * The flavors there are
     *
     * @var  array
     */
    protected $flavors = array('default', 'full');

    /**
     * Components that only mean something with a tool middleware
     *
     * @var  array
     */
    protected $components = array('com_tools', 'com_usage');

    /**
     * Dashboard modules that only mean something with a tool middleware
     *
     * @var  array
     */
    protected $modules = array('mod_mytools', 'mod_mysessions');

    /**
     * The member dashboard's default tiles, by module id in the starter data,
     * each in its column; rows follow the order given here
     *
     * Every hub gets these eight. The full flavor adds My Tools and My
     * Sessions; the default flavor leaves them out rather than offering
     * tiles for modules it has just disabled.
     *
     * @var  array
     */
    protected $tiles = array(
        array('module' => 44, 'col' => 1),   // Dashboard Introduction
        array('module' => 35, 'col' => 1),   // My Points
        array('module' => 38, 'col' => 1),   // My Questions
        array('module' => 39, 'col' => 1),   // My Wishes
        array('module' => 33, 'col' => 2),   // My Groups
        array('module' => 42, 'col' => 2),   // My Projects
        array('module' => 34, 'col' => 2),   // My Drafts
        array('module' => 37, 'col' => 3),   // My Tickets
    );

    /**
     * The tiles the full flavor adds, at the top of the third column
     *
     * @var  array
     */
    protected $toolTiles = array(
        array('module' => 41, 'col' => 3),   // My Sessions
        array('module' => 36, 'col' => 3),   // My Tools
    );

    /**
     * Default (required) command
     *
     * @return  void
     **/
    public function execute()
    {
        $this->help();
    }

    /**
     * Set the flavor
     *
     * @return  void
     **/
    public function set()
    {
        $flavor = strtolower((string) $this->arguments->getOpt(3));

        if (!in_array($flavor, $this->flavors)) {
            $this->output->error(
                'Please say which flavor: ' . implode(' or ', $this->flavors)
                . ($flavor !== '' ? " ('" . $flavor . "' is not one)" : '')
            );
        }

        $full      = ($flavor == 'full');
        $database  = App::get('db');
        $migration = new Migration($database);
        $verb      = $full ? 'Enabling' : 'Disabling';

        foreach ($this->components as $component) {
            $full ? $migration->enableComponent($component) : $migration->disableComponent($component);
            $this->output->addLine($verb . ' ' . $component);
        }

        foreach ($this->modules as $module) {
            $full ? $migration->enableModule($module) : $migration->disableModule($module);
            $this->output->addLine($verb . ' ' . $module);
        }

        // My Drafts lists a member's contributions in progress, tools among
        // them; it stays, and only its tools column goes
        $this->setModuleParam($database, 'mod_mycontributions', 'show_tools', $full ? '1' : '0');
        $this->output->addLine(($full ? 'Showing' : 'Hiding') . ' tools in mod_mycontributions');

        // The tool tiles go first so they head their column
        $tiles = $this->layout($full ? array_merge($this->toolTiles, $this->tiles) : $this->tiles);

        $migration->savePluginParams('members', 'dashboard', array(
            'allow_customization' => '1',
            'position'            => 'memberDashboard',
            'defaults'            => json_encode($tiles),
        ));
        $this->output->addLine('Setting the default member dashboard (' . count($tiles) . ' tiles)');

        // The knowledge base's tool articles: the Tools category, and the
        // one about reaching tool session storage over WebDAV
        if ($database->tableExists('#__categories')) {
            $database->setQuery(
                "UPDATE `#__categories` SET `published` = " . ($full ? 1 : 0)
                . " WHERE `extension` = 'com_kb' AND `alias` = 'tools'"
            );
            $database->query();
        }

        if ($database->tableExists('#__kb_articles')) {
            $database->setQuery(
                "UPDATE `#__kb_articles` SET `state` = " . ($full ? 1 : 0) . " WHERE `alias` = 'webdav'"
            );
            $database->query();
        }
        $this->output->addLine(($full ? 'Publishing' : 'Unpublishing') . ' the knowledge base tool articles');

        // The "tools" resource type: contributable or not, rather than
        // present or not, so the resources that already have it keep it
        if ($database->tableExists('#__resource_types')) {
            $database->setQuery("SELECT COUNT(*) FROM `#__resource_types` WHERE `alias` = 'tools'");

            if (!(int) $database->loadResult() && $full) {
                $database->setQuery(
                    "INSERT INTO `#__resource_types` (`id`, `alias`, `type`, `category`, `description`,"
                    . " `contributable`, `customFields`, `params`) VALUES (7, 'tools', 'Tools', 27,"
                    . " 'Simulation and modeling tools that can be accessed via a web browser.', 1,"
                    . " 'poweredby=Powered by=textarea=0\ncredits=Credits=textarea=0\n"
                    . "sponsoredby=Sponsored by=textarea=0\nreferences=References=textarea=0',"
                    . " 'plg_citations=1\nplg_questions=1\nplg_recommendations=1\nplg_related=1\n"
                    . "plg_reviews=1\nplg_usage=1\nplg_versions=1\nplg_favorite=1\nplg_share=1\n"
                    . "plg_wishlist=1\nplg_supportingdocs=1\nplg_about=0\nplg_abouttool=1')"
                );
                $database->query();
                $this->output->addLine('Adding the tools resource type');
            } else {
                $database->setQuery(
                    "UPDATE `#__resource_types` SET `contributable` = " . ($full ? 1 : 0) . " WHERE `alias` = 'tools'"
                );
                $database->query();
                $this->output->addLine('Making the tools resource type ' . ($full ? '' : 'not ') . 'contributable');
            }
        }

        $this->output->addLine("The hub is now the {$flavor} flavor.", 'success');
    }

    /**
     * Say which flavor the hub is, piece by piece
     *
     * @return  void
     **/
    public function status()
    {
        $database = App::get('db');
        $on       = array();
        $off      = array();

        foreach (array_merge($this->components, $this->modules) as $element) {
            $database->setQuery(
                "SELECT `enabled` FROM `#__extensions` WHERE `element` = " . $database->quote($element)
            );
            $enabled = $database->loadResult();

            if ($enabled === null) {
                $off[] = $element . ' (not installed)';
            } elseif ((int) $enabled) {
                $on[] = $element;
            } else {
                $off[] = $element;
            }
        }

        if ($database->tableExists('#__categories')) {
            $database->setQuery(
                "SELECT `published` FROM `#__categories` WHERE `extension` = 'com_kb' AND `alias` = 'tools'"
            );
            $published = $database->loadResult();

            if ($published !== null) {
                (int) $published ? ($on[] = 'knowledge base Tools category') : ($off[] = 'knowledge base Tools category');
            }
        }

        if ($database->tableExists('#__resource_types')) {
            $database->setQuery("SELECT `contributable` FROM `#__resource_types` WHERE `alias` = 'tools'");
            $contributable = $database->loadResult();

            if ($contributable === null) {
                $off[] = 'tools resource type (absent)';
            } else {
                (int) $contributable ? ($on[] = 'tools resource type') : ($off[] = 'tools resource type');
            }
        }

        // The component is the deciding piece: a hub with com_tools on is
        // trying to be full, whatever state the rest is in
        $database->setQuery("SELECT `enabled` FROM `#__extensions` WHERE `element` = 'com_tools'");
        $flavor = (int) $database->loadResult() ? 'full' : 'default';

        $this->output->addLine("This hub reads as the {$flavor} flavor.", 'success');

        if ($on) {
            $this->output->addLine('On:  ' . implode(', ', $on));
        }

        if ($off) {
            $this->output->addLine('Off: ' . implode(', ', $off));
        }

        $mixed = ($flavor == 'full') ? $off : $on;

        if ($mixed) {
            $this->output->addLine(
                'Not what the ' . $flavor . ' flavor sets: ' . implode(', ', $mixed)
                . ". Run 'muse repository:flavor set {$flavor}' to line them up.",
                'warning'
            );
        }
    }

    /**
     * Output help documentation
     *
     * @return  void
     **/
    public function help()
    {
        $this->output
             ->getHelpOutput()
             ->addOverview(
                 'Set the flavor of the hub: whether it runs simulation tools. '
                 . 'The default flavor is the CMS on its own, with the tools component, '
                 . 'its usage metrics, the tool dashboard modules, the knowledge base\'s '
                 . 'tool articles and the "tools" resource type turned off. The full '
                 . 'flavor turns them all back on, for a hub with a tool middleware '
                 . 'behind it. Nothing is deleted either way.'
             )
             ->noArgsSection()
             ->addSection('Usage')
             ->addArgument('muse repository:flavor set [default|full]')
             ->addArgument('muse repository:flavor status')
             ->addSpacer()
             ->addSection('Flavors')
             ->addArgument(
                 'default',
                 'The CMS without tools. What a hub is unless it has a middleware.'
             )
             ->addArgument(
                 'full',
                 'The CMS with tools: everything the default flavor turns off, on.'
             )
             ->render();
    }

    /**
     * Give each tile its row: two rows high, stacked in its column in the
     * order the tiles were given
     *
     * @param   array  $tiles  Each with a module and a col
     * @return  array  Each with row, size_x and size_y as well
     */
    private function layout(array $tiles)
    {
        $next = array();
        $laid = array();

        foreach ($tiles as $tile) {
            $col = (int) $tile['col'];
            $row = isset($next[$col]) ? $next[$col] : 1;
            $next[$col] = $row + 2;

            $laid[] = array(
                'module' => (int) $tile['module'],
                'col'    => $col,
                'row'    => $row,
                'size_x' => 1,
                'size_y' => 2,
            );
        }

        // Column by column, top to bottom - the order the shipped data uses,
        // so setting the flavor a hub already has changes nothing
        usort($laid, function ($a, $b) {
            return ($a['col'] - $b['col']) ?: ($a['row'] - $b['row']);
        });

        return $laid;
    }

    /**
     * Set one parameter on every instance of a module
     *
     * @param   object  $database
     * @param   string  $module    The module element, e.g. mod_mycontributions
     * @param   string  $key       The parameter
     * @param   string  $value     Its new value
     * @return  void
     */
    private function setModuleParam($database, $module, $key, $value)
    {
        $database->setQuery(
            "SELECT `id`, `params` FROM `#__modules` WHERE `module` = " . $database->quote($module)
        );

        foreach ((array) $database->loadObjectList() as $row) {
            $params = json_decode((string) $row->params, true);
            $params = is_array($params) ? $params : array();
            $params[$key] = $value;

            $database->setQuery(
                "UPDATE `#__modules` SET `params` = " . $database->quote(json_encode($params))
                . " WHERE `id` = " . (int) $row->id
            );
            $database->query();
        }
    }
}
