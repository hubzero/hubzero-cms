<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2025 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Sampledata;

use Hubzero\Facades\App;

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
     * Put a pack's own words in one of the hub's modules
     *
     * A hub's furniture - its footer, its notices, whatever a position holds -
     * is module content in the database rather than anything a template owns,
     * and the starter data ships a set of it that every hub carries until
     * somebody edits it by hand. A pack that is building a hub should be able
     * to say what that furniture reads, and not by having the template hide
     * the parts it disagrees with.
     *
     * Matches on whatever is given and writes whatever is given, so:
     *
     *     $this->module(
     *         ['module' => 'mod_custom', 'position' => 'footer'],
     *         ['content' => $markup, 'showtitle' => 0]
     *     );
     *
     * Writes only what differs, so running a pack again reports nothing and
     * changes nothing. Where nothing matches and $create is a position, the
     * module is made there and assigned to every page - a footer has to exist
     * before it can be written.
     *
     * @param   array         $find    Columns of #__modules to match on
     * @param   array         $values  Columns to set
     * @param   string|false  $create  Position to create it in if absent
     * @return  integer       How many modules this changed
     */
    protected function module(array $find, array $values, $create = false)
    {
        $db = App::get('db');

        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__modules');

        foreach ($find as $column => $value) {
            $query->whereEquals($column, $value);
        }

        $rows = $query->fetch();

        if (empty($rows) && $create) {
            return $this->makeModule($find, $values, $create) ? 1 : 0;
        }

        $written = 0;

        foreach ($rows as $row) {
            $set = [];

            foreach ($values as $column => $value) {
                $was = is_object($row) ? ($row->{$column} ?? null) : ($row[$column] ?? null);

                if ((string) $was !== (string) $value) {
                    $set[$column] = $value;
                }
            }

            if (!$set) {
                continue;
            }

            $db->getQuery(true)
                ->update('#__modules')
                ->set($set)
                ->whereEquals('id', is_object($row) ? $row->id : $row['id'])
                ->execute();

            $written++;
        }

        return $written;
    }

    /**
     * Make a module that is not there, and show it on every page
     *
     * @param   array   $find      Columns of #__modules to match on
     * @param   array   $values    Columns to set
     * @param   string  $position  Where it goes
     * @return  boolean
     */
    private function makeModule(array $find, array $values, $position)
    {
        $db = App::get('db');

        $row = array_merge([
            'title'     => 'Sample data',
            'content'   => '',
            'ordering'  => 1,
            'position'  => $position,
            'published' => 1,
            'module'    => 'mod_custom',
            'access'    => 1,
            'showtitle' => 0,
            'params'    => '',
            'client_id' => 0,
            'language'  => '*',
        ], $find, $values);

        $row['position'] = $position;

        $db->getQuery(true)
            ->insert('#__modules')
            ->values($row)
            ->execute();

        $id = $db->insertid();

        if (!$id) {
            return false;
        }

        // menuid 0 is every page: a module nobody has assigned is a module
        // nobody sees, and a pack has no menu item in mind for its furniture.
        $db->getQuery(true)
            ->insert('#__modules_menu')
            ->values(['moduleid' => $id, 'menuid' => 0])
            ->execute();

        return true;
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
