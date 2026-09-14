<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2025 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Bootstrap\Install\Tests;

use Hubzero\Test\Basic;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * What a hub holds the moment it is installed
 *
 * Three files decide that - schema.sql makes the tables, data.sql fills the
 * ones every hub needs, and starter.sql adds the sample content - and until
 * now nothing checked any of them. A mistake in these files is not a failing
 * feature, it is an installer that does not install, and the way it was found
 * was by installing a hub and noticing.
 *
 * These tests read the three files rather than run them, so they need no
 * database and no server. That is enough to catch the kinds of mistake these
 * files actually attract: a row with the wrong number of values, a NULL in a
 * column that refuses one, a default template that is not here, and text that
 * was written to be edited by whoever installed the hub and never was.
 *
 * What they cannot check is whatever migrations settle afterwards. Installing
 * is two things - these files, and then every migration in order - so a row
 * that looks wrong here may be one a migration corrects a moment later. The
 * main menu module is the example: the base data puts it in position-7, which
 * no template has drawn in years, and a migration moves it to user3.
 **/
class InstallSqlTest extends Basic
{
    /**
     * The install files, read
     *
     * @var  array
     */
    protected static $read = array();

    /**
     * The schema, and what a hub ends up holding
     *
     * @param   bool  $sample  With the sample content, or without
     * @return  object  Sql
     */
    protected function hub($sample = true)
    {
        $key = $sample ? 'sample' : 'bare';

        if (!isset(self::$read[$key])) {
            $sql = new Sql();
            $sql->read($this->file('schema.sql'))
                ->read($this->file('data.sql'));

            if ($sample) {
                $sql->read($this->file('starter.sql'));
            }

            self::$read[$key] = $sql;
        }

        return self::$read[$key];
    }

    /**
     * Where the install files are
     *
     * @param   string  $name  Which one
     * @return  string
     */
    protected function file($name)
    {
        return dirname(__DIR__) . '/sql/mysql/' . $name;
    }

    /**
     * Where the site templates are
     *
     * @return  string
     */
    protected function templates()
    {
        return dirname(dirname(dirname(__DIR__))) . '/templates';
    }

    /**
     * Both ways a hub can be installed
     *
     * @return  array
     */
    public static function installs()
    {
        return array(
            'with sample data' => array(true),
            'without sample data' => array(false),
        );
    }

    /**
     * Every row goes into a table the schema makes
     *
     * @param   bool  $sample  Which install
     * @return  void
     */
    #[DataProvider('installs')]
    public function testEveryRowHasATable($sample)
    {
        $hub = $this->hub($sample);

        foreach ($hub->tables() as $table) {
            $this->assertTrue($hub->has($table), $table . ' is written to but never created');
        }

        $this->assertNotEmpty($hub->tables(), 'the schema creates no tables at all');
    }

    /**
     * A row that lists no columns supplies one value per column
     *
     * The dump writes most rows positionally, so a column added to the schema
     * without the data files following it shows up here rather than as a
     * failed install.
     *
     * @param   bool  $sample  Which install
     * @return  void
     */
    #[DataProvider('installs')]
    public function testRowsAreTheRightWidth($sample)
    {
        $hub = $this->hub($sample);

        foreach ($hub->tables() as $table) {
            foreach ($hub->rows($table) as $i => $row) {
                $this->assertSame(
                    $row['#columns'],
                    $row['#values'],
                    sprintf(
                        '%s row %d gives %d values for %d columns',
                        $table,
                        $i + 1,
                        $row['#values'],
                        $row['#columns']
                    )
                );
            }
        }
    }

    /**
     * Nothing offers NULL to a column that will not take it
     *
     * @param   bool  $sample  Which install
     * @return  void
     */
    #[DataProvider('installs')]
    public function testNothingIsNullThatCannotBe($sample)
    {
        $hub = $this->hub($sample);

        foreach ($hub->tables() as $table) {
            $required = $hub->required($table);

            if (!$required) {
                continue;
            }

            foreach ($hub->rows($table) as $i => $row) {
                foreach ($required as $column) {
                    if (!array_key_exists($column, $row)) {
                        continue;
                    }

                    $this->assertNotNull(
                        $row[$column],
                        sprintf('%s row %d gives NULL for %s, which is NOT NULL', $table, $i + 1, $column)
                    );
                }
            }
        }
    }

    /**
     * A hub opens on a template that is actually here
     *
     * @param   bool  $sample  Which install
     * @return  void
     */
    #[DataProvider('installs')]
    public function testTheDefaultTemplateExists($sample)
    {
        $home = $this->hub($sample)->where('template_styles', array('client_id' => 0, 'home' => 1));

        $this->assertCount(1, $home, 'a hub should open on exactly one template');

        $template = $home[0]['template'];

        $this->assertDirectoryExists(
            $this->templates() . '/' . $template,
            'the default template style names "' . $template . '", which is not in core/templates'
        );

        $this->assertFileExists(
            $this->templates() . '/' . $template . '/index.php',
            $template . ' has no index.php to draw a page with'
        );
    }

    /**
     * Exactly one menu item is the front page
     *
     * @param   bool  $sample  Which install
     * @return  void
     */
    #[DataProvider('installs')]
    public function testOneMenuItemIsHome($sample)
    {
        $home = $this->hub($sample)->where('menu', array('client_id' => 0, 'home' => 1));

        $this->assertCount(
            1,
            $home,
            'a hub needs one front page, and this one has ' . count($home)
        );
    }

    /**
     * No shipped page is waiting for somebody to finish writing it
     *
     * A hub that never edits these is not unusual, so what they say out of the
     * box is what the site says. None of them may carry an instruction to the
     * administrator or a blank where a detail goes.
     *
     * @param   bool  $sample  Which install
     * @return  void
     */
    #[DataProvider('installs')]
    public function testNoPageIsStillATemplate($sample)
    {
        $unfinished = array(
            'Edit this page',
            'MyInstitution',
            'MyState',
            'MyCounty',
            'Your Organization',
            'Your Funding',
            'your science area',
            '{Street Address}',
            '{Postal Code}',
            '{Telephone}',
            '1234 Street',
            '555-555-5555',
        );

        $articles = $this->hub($sample)->rows('content');

        if (!$sample) {
            $this->assertSame(array(), $articles, 'a hub without sample data ships no articles');
            return;
        }

        foreach ($articles as $row) {
            $body = (isset($row['introtext']) ? $row['introtext'] : '')
                  . (isset($row['fulltext']) ? $row['fulltext'] : '');

            foreach ($unfinished as $phrase) {
                $this->assertStringNotContainsStringIgnoringCase(
                    $phrase,
                    $body,
                    sprintf('"%s" still says "%s"', isset($row['title']) ? $row['title'] : '?', $phrase)
                );
            }
        }
    }

    /**
     * Nothing prints the hub's address where its name belongs
     *
     * {xhub:getcfg hubShortURL} returns Request::base() - the whole address,
     * scheme, host and port. Prose wants hubShortName. The shipped content
     * used the wrong one seventy-five times before anybody read the rendered
     * page carefully.
     *
     * @param   bool  $sample  Which install
     * @return  void
     */
    #[DataProvider('installs')]
    public function testNoPagePrintsTheHubsAddressAsItsName($sample)
    {
        $articles = $this->hub($sample)->rows('content');

        if (!$sample) {
            $this->assertSame(array(), $articles, 'a hub without sample data ships no articles');
            return;
        }

        foreach ($articles as $row) {
            $body = (isset($row['introtext']) ? $row['introtext'] : '')
                  . (isset($row['fulltext']) ? $row['fulltext'] : '');

            $this->assertStringNotContainsString(
                'hubShortURL',
                $body,
                sprintf(
                    '"%s" uses hubShortURL, which renders as the whole address;'
                    . ' hubShortName is the hub\'s name',
                    isset($row['title']) ? $row['title'] : '?'
                )
            );
        }
    }
}
