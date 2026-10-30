<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Console\Tests;

use Hubzero\Test\Basic;
use Hubzero\Console\Command\Install\Answers;

/**
 * Installer answers read from the environment
 */
class AnswersTest extends Basic
{
    /**
     * The variables these tests set
     *
     * @var  array
     */
    private const VARIABLES = [
        'HUB_SITENAME',
        'HUB_LIVE_SITE',
        'HUB_DB_HOST',
        'HUB_DB_PASSWORD',
        'HUB_ADMIN_USERNAME',
        'HUB_ADMIN_EMAIL',
        'HUB_SAMPLE_DATA',
    ];

    /**
     * Leave neither variables nor answers behind
     *
     * @return  void
     */
    public function tearDown(): void
    {
        foreach (self::VARIABLES as $variable) {
            putenv($variable);
        }

        Answers::reset();

        parent::tearDown();
    }

    /**
     * Each variable answers its question in its section
     *
     * @return  void
     */
    public function testVariablesFillTheirSections()
    {
        putenv('HUB_SITENAME=Example Hub');
        putenv('HUB_LIVE_SITE=https://hub.example.org');
        putenv('HUB_DB_HOST=hub-db');
        putenv('HUB_DB_PASSWORD=secret');
        putenv('HUB_ADMIN_USERNAME=admin');
        putenv('HUB_ADMIN_EMAIL=admin@example.org');

        Answers::fromEnvironment();

        $this->assertTrue(Answers::isUnattended());
        $this->assertSame(
            ['sitename' => 'Example Hub', 'live_site' => 'https://hub.example.org'],
            Answers::section('site')
        );
        $this->assertSame(['host' => 'hub-db', 'password' => 'secret'], Answers::section('database'));
        $this->assertSame(['username' => 'admin', 'email' => 'admin@example.org'], Answers::section('admin'));
    }

    /**
     * A variable left unset or empty leaves its answer to the default
     *
     * @return  void
     */
    public function testUnsetAndEmptyVariablesAreLeftOut()
    {
        putenv('HUB_SITENAME=');

        Answers::fromEnvironment();

        $this->assertTrue(Answers::isUnattended());
        $this->assertSame([], Answers::section('site'));
        $this->assertSame([], Answers::section('database'));
        $this->assertFalse(Answers::wantsSampleData());
    }

    /**
     * Sample data is asked for the way an answer file asks for it
     *
     * @return  void
     */
    public function testSampleData()
    {
        putenv('HUB_SAMPLE_DATA=1');
        Answers::fromEnvironment();
        $this->assertTrue(Answers::wantsSampleData());

        putenv('HUB_SAMPLE_DATA=no');
        Answers::fromEnvironment();
        $this->assertFalse(Answers::wantsSampleData());
    }
}
