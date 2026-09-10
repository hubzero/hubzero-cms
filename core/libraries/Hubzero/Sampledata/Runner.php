<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2025 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Sampledata;

use Hubzero\Facades\App;

/**
 * Runs a sample data pack
 *
 * Keeps a note of every step that has finished, so a pack that gains steps can
 * be run again and only builds what is new, and so a step that fails half way
 * does not come back as a duplicate on the next attempt.
 **/
class Runner
{
    /**
     * The table the notes are kept in
     *
     * @var  string
     */
    const TABLE = '#__sampledata';

    /**
     * The database
     *
     * @var  \Hubzero\Database\Driver
     */
    protected $db;

    /**
     * Somewhere to say what is happening
     *
     * @var  callable|null
     */
    protected $reporter;

    /**
     * Set the runner up
     *
     * @param   object|null    $db        The database to build in
     * @param   callable|null  $reporter  Called with each line of progress
     * @return  void
     */
    public function __construct($db = null, ?callable $reporter = null)
    {
        $this->db       = $db ?: App::get('db');
        $this->reporter = $reporter;
    }

    /**
     * Run a pack
     *
     * @param   \Hubzero\Sampledata\Pack  $pack   The pack to run
     * @param   bool                      $again  Run steps that already have
     * @return  bool  True when every step that ran finished
     */
    public function run(Pack $pack, $again = false)
    {
        if (!$pack->exists()) {
            $this->report('There is no pack named ' . $pack->name() . '.', true);
            return false;
        }

        $this->prepare();

        $done  = $this->completed($pack->name());
        $steps = $pack->steps(function ($message) {
            $this->report('    ' . $message);
        });

        if (empty($steps)) {
            $this->report('The ' . $pack->name() . ' pack has no steps.');
            return true;
        }

        $ok = true;

        foreach ($steps as $step) {
            if (!$again && in_array($step->name(), $done, true)) {
                $this->report('  Already built: ' . $step->describe());
                continue;
            }

            $this->report('  ' . $step->describe());

            try {
                $step->run();
                $this->record($pack->name(), $step->name());
            } catch (\Exception $e) {
                $this->report('  Stopped at ' . $step->name() . ': ' . $e->getMessage(), true);
                $ok = false;
                break;
            }
        }

        return $ok;
    }

    /**
     * Take a pack's content back out
     *
     * @param   \Hubzero\Sampledata\Pack  $pack  The pack to remove
     * @return  bool  True when every step that had run took itself back
     */
    public function remove(Pack $pack)
    {
        if (!$pack->exists()) {
            $this->report('There is no pack named ' . $pack->name() . '.', true);
            return false;
        }

        $this->prepare();

        $done = $this->completed($pack->name());
        $ok   = true;

        foreach (array_reverse($pack->steps()) as $step) {
            if (!in_array($step->name(), $done, true)) {
                continue;
            }

            if (!$step->remove()) {
                $this->report('  ' . $step->name() . ' cannot take its content back.', true);
                $ok = false;
                continue;
            }

            $this->report('  Removed: ' . $step->describe());
            $this->forget($pack->name(), $step->name());
        }

        return $ok;
    }

    /**
     * What a pack has built so far
     *
     * @param   string  $pack  The pack to ask about
     * @return  array   The names of the steps that have finished
     */
    public function completed($pack)
    {
        if (!$this->db->tableExists(self::TABLE)) {
            return [];
        }

        $rows = $this->db->getQuery(true)
            ->select('step')
            ->from(self::TABLE)
            ->whereEquals('pack', $pack)
            ->fetch();

        $steps = [];

        foreach ($rows as $row) {
            $steps[] = is_object($row) ? $row->step : $row['step'];
        }

        return $steps;
    }

    /**
     * Make sure there is somewhere to keep the notes
     *
     * @return  void
     */
    protected function prepare()
    {
        if ($this->db->tableExists(self::TABLE)) {
            return;
        }

        $this->db->schema()->createTable(self::TABLE)
            ->increments('id')
            ->string('pack', 100)
            ->string('step', 100)
            ->datetime('built')
            ->primaryKey('id')
            ->uniqueIndex('uidx_pack_step', ['pack', 'step'])
            ->execute();
    }

    /**
     * Note that a step has finished
     *
     * @param   string  $pack  The pack it belongs to
     * @param   string  $step  The step that finished
     * @return  void
     */
    protected function record($pack, $step)
    {
        // A step rebuilt on purpose is noted once, not twice
        $this->forget($pack, $step);

        $this->db->getQuery(true)
            ->insert(self::TABLE)
            ->set([
                'pack'  => $pack,
                'step'  => $step,
                'built' => gmdate('Y-m-d H:i:s'),
            ])
            ->execute();
    }

    /**
     * Forget that a step ever ran
     *
     * @param   string  $pack  The pack it belongs to
     * @param   string  $step  The step to forget
     * @return  void
     */
    protected function forget($pack, $step)
    {
        $this->db->getQuery(true)
            ->delete(self::TABLE)
            ->whereEquals('pack', $pack)
            ->whereEquals('step', $step)
            ->execute();
    }

    /**
     * Say what is happening
     *
     * @param   string  $message  The line to report
     * @param   bool    $error    Whether it is bad news
     * @return  void
     */
    protected function report($message, $error = false)
    {
        if ($this->reporter) {
            call_user_func($this->reporter, $message, $error);
        }
    }
}
