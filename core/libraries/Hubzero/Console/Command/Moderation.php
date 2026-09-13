<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Console\Command;

use Hubzero\Moderation\Economy;
use Hubzero\Moderation\Eligibility;
use Hubzero\Moderation\Activity;
use Hubzero\Moderation\Wallet;
use Hubzero\Moderation\Grant;
use Hubzero\Moderation\Grantor\IntervalGrantor;

/**
 * Inspect the moderation economy from the command line
 */
class Moderation extends Base implements CommandInterface
{
    /**
     * Default to the help
     *
     * @return  void
     */
    public function execute()
    {
        $this->help();
    }

    /**
     * Show the last few passes of the grantor
     *
     * muse moderation grants [--type=<item type>] [--limit=10]
     *
     * @return  void
     */
    public function grants()
    {
        $type  = (string) $this->arguments->getOpt('type', '');
        $limit = (int) $this->arguments->getOpt('limit', 10);

        $query = Grant::all()->order('created', 'desc')->limit($limit);

        if ($type) {
            $query->whereEquals('item_type', $type);
        }

        $rows = $query->rows();

        if (!count($rows)) {
            $this->output->addLine('No passes recorded.', 'warning');
            return;
        }

        $this->output->addLine(sprintf(
            '  %-20s %-20s %-10s %8s %8s %8s %8s %8s',
            'when', 'item type', 'grantor', 'eligible', 'granted', 'issued', 'expired', 'spent'
        ), 'info');

        foreach ($rows as $row) {
            $this->output->addLine(sprintf(
                '  %-20s %-20s %-10s %8s %8s %8s %8s %8s',
                $row->get('created'),
                $row->get('item_type'),
                $row->get('grantor'),
                $row->get('eligible'),
                $row->get('granted'),
                $row->get('credits_issued'),
                $row->get('credits_expired'),
                $row->get('credits_spent')
            ));
        }
    }

    /**
     * Show who currently holds credits
     *
     * muse moderation wallets --type=<item type>
     *
     * @return  void
     */
    public function wallets()
    {
        $type = (string) $this->arguments->getOpt('type', '');

        $query = Wallet::all()->where('credits', '>', 0)->order('credits', 'desc');

        if ($type) {
            $query->whereEquals('item_type', $type);
        }

        $rows = $query->rows();

        if (!count($rows)) {
            $this->output->addLine('Nobody is holding credits.', 'warning');
            return;
        }

        foreach ($rows as $row) {
            $this->output->addLine(sprintf(
                '  user %-8s %-20s %3s credits, expiring %s',
                $row->get('user_id'),
                $row->get('item_type'),
                $row->get('credits'),
                $row->get('credits_expire') ?: 'never'
            ));
        }
    }

    /**
     * Run a grant pass by hand
     *
     * muse moderation grant --type=<item type> [--fraction=0.15] [--credits=5]
     *
     * @return  void
     */
    public function grant()
    {
        $type = (string) $this->arguments->getOpt('type', '');

        if (!$type) {
            $this->output->error('Please name an item type with --type=<type>');
        }

        $settings = array(
            'grant_fraction'    => (float) $this->arguments->getOpt('fraction', 0.15),
            'credits_per_grant' => (int) $this->arguments->getOpt('credits', 5),
            'eligible_hitcount' => (int) $this->arguments->getOpt('reads', 3),
        );

        $economy = new Economy($type, new IntervalGrantor($settings), new Eligibility($settings));
        $grant   = $economy->grant();

        $this->output->addLine(sprintf(
            '%s eligible, %s granted, %s credits issued.',
            $grant->get('eligible'),
            $grant->get('granted'),
            $grant->get('credits_issued')
        ), 'success');
    }

    /**
     * Take back credits nobody spent in time
     *
     * muse moderation expire --type=<item type>
     *
     * @return  void
     */
    public function expire()
    {
        $type = (string) $this->arguments->getOpt('type', '');

        if (!$type) {
            $this->output->error('Please name an item type with --type=<type>');
        }

        $expired = (new Economy($type))->expire();

        $this->output->addLine(
            $expired
                ? sprintf('%s credit(s) expired unspent.', $expired)
                : 'Nothing had expired.',
            $expired ? 'success' : 'info'
        );
    }

    /**
     * Note that somebody read something, to build a pool for testing
     *
     * muse moderation read --user=<id> --type=<item type> [--times=5]
     *
     * @return  void
     */
    public function read()
    {
        $user  = (int) $this->arguments->getOpt('user', 0);
        $type  = (string) $this->arguments->getOpt('type', '');
        $times = (int) $this->arguments->getOpt('times', 1);

        if (!$user || !$type) {
            $this->output->error('Please give --user=<id> and --type=<item type>');
        }

        for ($i = 0; $i < max(1, $times); $i++) {
            Activity::record($user, $type);
        }

        $this->output->addLine(sprintf('Recorded %s read(s) for user %s on %s.', $times, $user, $type), 'success');
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
            ->addOverview('Inspect the moderation credit economy.')
            ->addTasks($this)
            ->addArgument('--type=<item type>', 'Which kind of item, as in com_forum.post.')
            ->addArgument('--user=<id>', 'For read: whose activity to record.')
            ->addArgument('--times=<n>', 'For read: how many reads to record. Defaults to 1.')
            ->addArgument('--fraction=<n>', 'For grant: share of the eligible pool to grant. Defaults to 0.15.')
            ->addArgument('--credits=<n>', 'For grant: credits per grant. Defaults to 5.')
            ->addArgument('--limit=<n>', 'For grants: how many passes to show. Defaults to 10.');
    }
}
