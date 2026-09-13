<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Console\Command;

use Hubzero\Karma\Karma as Reputation;
use Hubzero\Karma\Scale;
use Hubzero\Karma\Ledger;
use Hubzero\Karma\Balance;

/**
 * Inspect and adjust karma from the command line
 */
class Karma extends Base implements CommandInterface
{
    /**
     * Default to showing one member's standing
     *
     * @return  void
     */
    public function execute()
    {
        $this->help();
    }

    /**
     * Report what a member has and what it allows them
     *
     * muse karma show --user=42 [--scale=global]
     *
     * @return  void
     */
    public function show()
    {
        $user = (int) $this->arguments->getOpt('user', $this->arguments->getOpt(3, 0));

        if (!$user) {
            $this->output->error('Please name a user with --user=<id>');
        }

        $this->output->addLine('Karma for user ' . $user, 'info');

        foreach (Scale::all()->order('ordering', 'asc')->rows() as $scale) {
            $balance = Balance::oneByUserAndScale($user, $scale->get('id'));

            $this->output->addLine(sprintf(
                '  %-20s %8s  raw %-8s  %s',
                $scale->get('alias'),
                Reputation::of($user, $scale->get('alias')),
                $balance->get('id') ? $balance->get('raw') : '-',
                $scale->adjective(Reputation::of($user, $scale->get('alias')))
            ));
        }

        $standing = Reputation::standing($user);

        if ($standing) {
            $this->output->addLine('What this allows:', 'info');

            foreach ($standing as $alias => $value) {
                $this->output->addLine(sprintf('  %-30s %s', $alias, var_export($value, true)));
            }
        }
    }

    /**
     * Show the most recent ledger entries for a member
     *
     * muse karma ledger --user=42 [--limit=20]
     *
     * @return  void
     */
    public function ledger()
    {
        $user  = (int) $this->arguments->getOpt('user', $this->arguments->getOpt(3, 0));
        $limit = (int) $this->arguments->getOpt('limit', 20);

        if (!$user) {
            $this->output->error('Please name a user with --user=<id>');
        }

        $rows = Ledger::all()
            ->whereEquals('subject_id', $user)
            ->order('created', 'desc')
            ->limit($limit)
            ->rows();

        if (!count($rows)) {
            $this->output->addLine('No entries.', 'warning');
            return;
        }

        foreach ($rows as $row) {
            $this->output->addLine(sprintf(
                '  %-20s %-24s %6s  applied %-6s  %s%s',
                $row->get('created'),
                $row->get('rule'),
                $row->get('delta'),
                $row->get('applied'),
                $row->get('source_type') ? $row->get('source_type') . '#' . $row->get('source_id') : '-',
                $row->isActive() ? '' : '  (reversed)'
            ));
        }
    }

    /**
     * Move a member's karma without a rule
     *
     * muse karma adjust --user=42 --scale=global --delta=-3 --reason=manual
     *
     * @return  void
     */
    public function adjust()
    {
        $user   = (int) $this->arguments->getOpt('user', 0);
        $scale  = (string) $this->arguments->getOpt('scale', 'global');
        $delta  = (float) $this->arguments->getOpt('delta', 0);
        $reason = (string) $this->arguments->getOpt('reason', 'manual');

        if (!$user || !$delta) {
            $this->output->error('Please give --user=<id> and a non-zero --delta=<n>');
        }

        $entry = Reputation::adjust($user, $scale, $delta, $reason, array(
            'source_type' => 'muse',
            'source_id'   => 0
        ));

        if (!$entry) {
            $this->output->error('Nothing was changed. Check the scale exists.');
        }

        $this->output->addLine(sprintf(
            'Adjusted user %s on %s by %s; karma is now %s',
            $user,
            $scale,
            $delta,
            Reputation::of($user, $scale)
        ), 'success');
    }

    /**
     * Rebuild balances from the ledger
     *
     * muse karma rebuild [--scale=global]
     *
     * @return  void
     */
    public function rebuild()
    {
        $only    = (string) $this->arguments->getOpt('scale', '');
        $now     = with(new \Hubzero\Utility\Date('now'))->toSql();
        $changed = 0;
        $seen    = 0;

        foreach (Scale::all()->rows() as $scale) {
            if ($only && $scale->get('alias') != $only) {
                continue;
            }

            $subjects = Ledger::all()
                ->select('subject_id')
                ->whereEquals('scale_id', $scale->get('id'))
                ->group('subject_id')
                ->rows();

            foreach ($subjects as $subject) {
                $seen++;

                if (Balance::rebuild($subject->get('subject_id'), $scale, $now)) {
                    $changed++;
                }
            }
        }

        $this->output->addLine(sprintf(
            'Checked %s balance(s); %s needed repair.',
            $seen,
            $changed
        ), 'success');
    }

    /**
     * Output help documentation
     *
     * @return  void
     */
    public function help()
    {
        $this
            ->output
            ->addOverview('Inspect and adjust site-wide karma.')
            ->addTasks($this)
            ->addArgument('--user=<id>', 'The member to act on.')
            ->addArgument('--scale=<alias>', 'Which scale. Defaults to "global" where one is needed.')
            ->addArgument('--delta=<n>', 'For adjust: the signed amount to move karma by.')
            ->addArgument('--reason=<text>', 'For adjust: what to record in the ledger. Defaults to "manual".')
            ->addArgument('--limit=<n>', 'For ledger: how many entries to show. Defaults to 20.');
    }
}
