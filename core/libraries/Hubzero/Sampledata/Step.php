<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2025 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Sampledata;

/**
 * One step of a sample data pack
 *
 * A step makes a piece of a hub's content and nothing else, so that a pack
 * reads as a list of what it builds. Steps run in the order their names sort,
 * are recorded when they finish, and are skipped on a later run, so a pack can
 * be extended and re-run without doubling what is already there.
 *
 * Anything random a step needs comes from random(), which is seeded from the
 * step's own name: two runs of the same step against the same hub produce the
 * same content.
 **/
abstract class Step
{
    /**
     * Where the pack's fixtures live
     *
     * @var  string
     */
    protected $fixtures;

    /**
     * Numbers, in a fixed order
     *
     * @var  \Hubzero\Sampledata\Sequence
     */
    protected $sequence;

    /**
     * Somewhere to say what is happening
     *
     * @var  callable|null
     */
    protected $reporter;

    /**
     * Set the step up to run
     *
     * @param   string          $fixtures  Directory holding the pack's fixtures
     * @param   callable|null   $reporter  Called with each line the step reports
     * @return  void
     */
    public function __construct($fixtures = '', ?callable $reporter = null)
    {
        $this->fixtures = rtrim((string) $fixtures, DIRECTORY_SEPARATOR);
        $this->reporter = $reporter;
        $this->sequence = new Sequence(static::class);
    }

    /**
     * What this step builds, in a few words
     *
     * @return  string
     */
    abstract public function describe();

    /**
     * Build it
     *
     * @return  void
     * @throws  \Exception  When the step cannot finish
     */
    abstract public function run();

    /**
     * The name this step is recorded and ordered under
     *
     * @return  string
     */
    public function name()
    {
        $parts = explode('\\', static::class);

        return array_pop($parts);
    }

    /**
     * Undo what the step built
     *
     * A step that cannot take itself back says so by leaving this alone; the
     * runner then reports that removing the pack is not something it can do.
     *
     * @return  bool  True if the step removed its content
     */
    public function remove()
    {
        return false;
    }

    /**
     * Say what is happening
     *
     * @param   string  $message  The line to report
     * @return  $this
     */
    protected function report($message)
    {
        if ($this->reporter) {
            call_user_func($this->reporter, $message);
        }

        return $this;
    }

    /**
     * Read one of the pack's fixture files
     *
     * @param   string  $name  File name, relative to the pack's fixtures
     * @return  array   The rows it holds, or an empty array if there are none
     */
    protected function fixture($name)
    {
        $path = $this->fixtures . DIRECTORY_SEPARATOR . $name;

        if (!is_file($path)) {
            return [];
        }

        $rows = json_decode(file_get_contents($path), true);

        return is_array($rows) ? $rows : [];
    }
}
