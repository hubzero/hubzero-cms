<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Console\Command;

use Hubzero\Utility\Date;
use Components\Story\Models\Submission;

/**
 * Work the submissions queue from the command line
 */
class Story extends Base implements CommandInterface
{
    /**
     * Default to the help text
     *
     * @return  void
     */
    public function execute()
    {
        $this->help();
    }

    /**
     * Make sure the component's models are loadable
     *
     * @return  bool
     */
    private function models()
    {
        $path = PATH_CORE . DS . 'components' . DS . 'com_story' . DS . 'models' . DS . 'submission.php';

        if (!file_exists($path)) {
            $this->output->error('com_story is not installed.');
            return false;
        }

        require_once $path;

        return true;
    }

    /**
     * Show the queue as it currently stands
     *
     * muse story queue [--all]
     *
     * @return  void
     */
    public function queue()
    {
        if (!$this->models()) {
            return;
        }

        $query = $this->arguments->getOpt('all')
            ? Submission::all()
            : Submission::open();

        $rows = $query->order('popularity', 'desc')->rows();

        $this->output->addLine('The submissions queue', 'info');

        if (!count($rows)) {
            $this->output->addLine('  Nothing waiting.');
            return;
        }

        foreach ($rows as $row) {
            $this->output->addLine(sprintf(
                '  #%-4s %8s  %-8s %-9s %s',
                $row->get('id'),
                round((float) $row->get('popularity'), 1),
                'ed ' . round((float) $row->get('editor_popularity'), 1),
                $row->get('state'),
                substr($row->get('subject'), 0, 58)
            ));
        }
    }

    /**
     * Let the queue settle
     *
     * The same work plg_cron_story does on a schedule, for a hub that would
     * rather run it by hand or wants to see what it would do first.
     *
     * muse story decay [--dry-run]
     *
     * @return  void
     */
    public function decay()
    {
        if (!$this->models()) {
            return;
        }

        $dry    = (bool) $this->arguments->getOpt('dry-run');
        $config = \Component::params('com_story');
        $now    = Date::of('now');

        $rows    = Submission::all()->whereIn('state', Submission::openStates())->rows();
        $touched = 0;

        $this->output->addLine($dry ? 'Decay, dry run' : 'Decay', 'info');

        foreach ($rows as $row) {
            $before = (float) $row->get('popularity');

            $row->decay($config, $now);

            $after = (float) $row->get('popularity');

            if (abs($after - $before) < 0.01) {
                continue;
            }

            $this->output->addLine(sprintf(
                '  #%-4s %8s -> %-8s %s',
                $row->get('id'),
                round($before, 2),
                round($after, 2),
                substr($row->get('subject'), 0, 48)
            ));

            if (!$dry) {
                $row->save();
            }

            $touched++;
        }

        $this->output->addLine($touched
            ? sprintf('  %d submission(s) %s.', $touched, $dry ? 'would move' : 'moved')
            : '  Nothing had moved far enough to be worth a write.');
    }

    /**
     * Help
     *
     * @return  void
     */
    public function help()
    {
        $this->output
            ->addOverview('Work the com_story submissions queue.')
            ->addTasks($this)
            ->addArgument('--all', 'With queue: include submissions already decided.', 'Default: only what is still open')
            ->addArgument('--dry-run', 'With decay: report what would move without writing anything.', 'Default: write');
    }
}
