<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2025 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Console\Command;

use Hubzero\Facades\App;

/**
 * The addresses a hub's components answer at
 *
 * Every site component has an entry in the component menu, and that entry is
 * what gives it an address and an Itemid - and with the Itemid, the modules
 * assigned to that page and its template style. The entry is made when the
 * component is installed, so ordinarily there is nothing to do here.
 *
 * Ordinarily. A component dropped into a hub without a migration has none. A
 * hub that has been upgraded across many versions may be missing the ones added
 * before any of this existed. And an entry is locked in the admin, on purpose,
 * so this is also the way to change one that is wrong.
 **/
class Routes extends Base implements CommandInterface
{
    /**
     * Components with a site half that is not a page
     *
     * Machinery, or an endpoint that something else talks to rather than a
     * person. com_content is here because it routes through articles and has
     * never answered at /content; com_dataviewer because it answers 404 to
     * every spelling of its own name.
     *
     * @var  array
     */
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
     * Default - say what is out of step without changing anything
     *
     * @return  void
     **/
    public function execute()
    {
        $this->check();
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
                 'The addresses a hub\'s components answer at. "check" says what is out'
                 . ' of step; "fix" puts it right.'
             )
             ->addTasks($this)
             ->render();
    }

    /**
     * Say which components have no address, and which addresses have no component
     *
     * @museDescription  Report components whose route is missing or stale
     *
     * @return  void
     */
    public function check()
    {
        $this->report($this->survey());
    }

    /**
     * Give every site component the address it should have
     *
     * @museDescription  Create any missing component routes
     *
     * @return  void
     */
    public function fix()
    {
        $found = $this->survey();

        if (!$found['missing'] && !$found['orphaned']) {
            $this->output->addLine('Every site component already has its address.', 'success');
            return;
        }

        $routes = new \Hubzero\Menu\ComponentRoute(App::get('db'));

        foreach ($found['missing'] as $element => $id) {
            if ($routes->create($element, $id)) {
                $this->output->addLine(
                    'Gave ' . $element . ' the address /' . substr($element, 4),
                    'success'
                );
            } else {
                $this->output->addLine('Could not give ' . $element . ' an address', 'warning');
            }
        }

        foreach ($found['orphaned'] as $alias => $element) {
            $this->output->addLine(
                'Left /' . $alias . ' alone: ' . $element . ' is not installed here,'
                . ' and an address somebody may be linking to is not this command\'s to remove',
                'warning'
            );
        }
    }

    /**
     * What is there against what should be
     *
     * @return  array
     */
    protected function survey()
    {
        $db = App::get('db');

        $components = array();

        $installed = $db->getQuery(true)
            ->select('extension_id')
            ->select('element')
            ->select('enabled')
            ->from('#__extensions')
            ->whereEquals('type', 'component')
            ->fetch();

        foreach ($installed as $row) {
            $element = is_object($row) ? $row->element : $row['element'];
            $enabled = (int) (is_object($row) ? $row->enabled : $row['enabled']);
            $id      = (int) (is_object($row) ? $row->extension_id : $row['extension_id']);

            if (!$enabled || substr($element, 0, 4) !== 'com_') {
                continue;
            }

            if (in_array($element, $this->notPages, true)) {
                continue;
            }

            if (!is_dir(PATH_CORE . '/components/' . $element . '/site')) {
                continue;
            }

            $components[$element] = $id;
        }

        $routed = array();

        $entries = $db->getQuery(true)
            ->select('alias')
            ->select('link')
            ->from('#__menu')
            ->whereEquals('client_id', 0)
            ->whereEquals('menutype', $this->menutype($db))
            ->fetch();

        foreach ($entries as $row) {
            $alias = is_object($row) ? $row->alias : $row['alias'];
            $link  = is_object($row) ? $row->link : $row['link'];

            $routed[$alias] = preg_match('/option=(com_\w+)/', (string) $link, $m)
                ? $m[1]
                : 'com_' . $alias;
        }

        $missing = array();

        foreach ($components as $element => $id) {
            // Anything already reachable under that name counts, whichever menu
            // it is filed under - a hub may have made its own
            $taken = $db->getQuery(true)
                ->select('id')
                ->from('#__menu')
                ->whereEquals('client_id', 0)
                ->whereEquals('path', substr($element, 4))
                ->value('id');

            if (!$taken) {
                $missing[$element] = $id;
            }
        }

        $orphaned = array();

        foreach ($routed as $alias => $element) {
            if (!isset($components[$element])) {
                $orphaned[$alias] = $element;
            }
        }

        return array(
            'components' => $components,
            'missing'    => $missing,
            'orphaned'   => $orphaned,
        );
    }

    /**
     * Print a survey
     *
     * @param   array  $found  What survey() returned
     * @return  void
     */
    protected function report($found)
    {
        $this->output->addLine(
            count($found['components']) . ' site components should have an address.'
        );

        if (!$found['missing']) {
            $this->output->addLine('None is missing one.', 'success');
        } else {
            $this->output->addLine(
                count($found['missing']) . ' with no address:',
                'warning'
            );

            foreach ($found['missing'] as $element => $id) {
                $this->output->addLine('  ' . $element . '  ->  /' . substr($element, 4));
            }

            $this->output->addLine('Run "muse routes fix" to create them.');
        }

        if ($found['orphaned']) {
            $this->output->addLine(
                count($found['orphaned']) . ' address'
                . (count($found['orphaned']) == 1 ? '' : 'es')
                . ' for something not installed or switched off:',
                'warning'
            );

            foreach ($found['orphaned'] as $alias => $element) {
                $this->output->addLine('  /' . $alias . '  ->  ' . $element);
            }

            $this->output->addLine(
                'These are left alone: somebody may be linking to them.'
            );
        }
    }

    /**
     * The menu the component routes live in
     *
     * @param   object  $db  The database
     * @return  string
     */
    protected function menutype($db)
    {
        $routes = new \Hubzero\Menu\ComponentRoute($db);

        $found = $routes->menutype();

        return $found ? $found : 'components';
    }
}
