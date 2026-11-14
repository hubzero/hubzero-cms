<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2026 Purdue University.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\User\Tests;

use Hubzero\User\Profile;
use PHPUnit\Framework\TestCase;

/**
 * A profile's fields are the xprofiles columns and the values kept in the
 * xprofiles_* tables beside it; the properties it keeps for itself cannot
 * be read or written as fields.
 */
class ProfileFieldsTest extends TestCase
{
    /**
     * @return  void
     */
    public function testColumnsCanBeSetAndRead(): void
    {
        $profile = new Profile();

        $this->assertSame($profile, $profile->set('givenName', 'Ada'));
        $this->assertCount(0, $profile->getErrors());
        $this->assertSame('Ada', $profile->get('givenName'));
    }

    /**
     * @return  void
     */
    public function testSingleValuesFromTheirOwnTable(): void
    {
        $profile = new Profile();
        $profile->set('bio', 'Mathematician');

        $this->assertSame('Mathematician', $profile->get('bio'));
        $this->assertFalse($profile->add('bio', 'More'));
    }

    /**
     * @return  void
     */
    public function testListsFromTheirOwnTables(): void
    {
        $profile = new Profile();
        $profile->set('race', ['white', 'asian', 'asian']);

        $this->assertSame(['asian', 'white'], $profile->get('race'));

        $profile->add('race', 'black');
        $profile->remove('race', 'white');

        $this->assertSame(['asian', 'black'], array_values($profile->get('race')));
    }

    /**
     * @return  void
     */
    public function testAPasswordIsKeptAndHashed(): void
    {
        $profile = new Profile();
        $profile->set('password', 'secret');

        $this->assertSame('secret', $profile->get('password'));
        $this->assertNotSame('', (string) $profile->get('userPassword'));
        $this->assertNotSame('secret', $profile->get('userPassword'));
    }

    /**
     * @return  void
     */
    public function testUnknownNamesAreRefused(): void
    {
        $profile = new Profile();

        $this->assertFalse($profile->get('nosuchfield'));
        // A refused set() is recorded as an error; set() itself returns the profile
        $before = count($profile->getErrors());
        $profile->set('nosuchfield', 1);
        $this->assertCount($before + 1, $profile->getErrors());
    }

    /**
     * @return  void
     */
    public function testTheProfilesOwnPropertiesAreNotFields(): void
    {
        $profile = new Profile();
        $profile->set('password', 'secret');

        foreach (['plainPassword', 'paramsRegistry', 'auxsBio', 'auxvRace', '_password'] as $own) {
            $this->assertFalse($profile->get($own), $own);
            $before = count($profile->getErrors());
            $profile->set($own, 'x');
            $this->assertCount($before + 1, $profile->getErrors(), $own);
        }

        $this->assertSame('secret', $profile->get('password'));
    }
}
