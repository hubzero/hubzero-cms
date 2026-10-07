<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Groups\Members\Tests;

use Hubzero\Test\Basic;
use Plugins\Groups\Members\Helpers\RoleOrder;
use PHPUnit\Framework\Attributes\DataProvider;

require_once dirname(__DIR__) . '/helpers/roleorder.php';

/**
 * Default ordering of member roles
 */
class RoleOrderTest extends Basic
{
    /**
     * Role names and the term they name, as Y-m
     *
     * @return  array
     */
    public static function terms(): array
    {
        return [
            'month and year'          => ['May 2026', '2026-05'],
            'abbreviated month'       => ['Sept 2025', '2025-09'],
            'season first'            => ['Fall 2025', '2025-09'],
            'year first'              => ['2026 Spring', '2026-03'],
            'year first, autumn'      => ['2026 Fall', '2026-09'],
            'class of'                => ['Class of 2026', '2026-01'],
            'class of with season'    => ['Class of 2025, Fall', '2025-09'],
            'numeric month'           => ['05/2026', '2026-05'],
            'iso-ish'                 => ['2026-11', '2026-11'],
            'bare year'               => ['2027', '2027-01'],
            'weekday is not a month'  => ['Sat 2026', '2026-01'],
            'decade is not a month'   => ['2020s cohort 2024', '2024-01'],
        ];
    }

    /**
     * @param   string  $name
     * @param   string  $expected
     * @return  void
     */
    #[DataProvider('terms')]
    public function testTermNamesResolveToTheirMonth(string $name, string $expected)
    {
        $this->assertSame($expected, gmdate('Y-m', RoleOrder::date($name)));
    }

    /**
     * Names without a year are not terms
     *
     * @return  void
     */
    public function testNamesWithoutAYearAreNotTerms()
    {
        $this->assertNull(RoleOrder::date('Purdue University'));
        $this->assertNull(RoleOrder::date('Room 2026B staff'));
        $this->assertNull(RoleOrder::date('Fall'));
        $this->assertNull(RoleOrder::date('Cohort 42'));
    }

    /**
     * The result never depends on the current date
     *
     * @return  void
     */
    public function testWeekdayNamesDoNotShiftWithToday()
    {
        $this->assertSame(gmmktime(0, 0, 0, 1, 1, 2026), RoleOrder::date('Sat 2026'));
    }

    /**
     * Names first alphabetically, then terms by date, ties by name
     *
     * @return  void
     */
    public function testSortPutsNamesFirstThenTermsByDate()
    {
        $roles = [
            ['id' => 1, 'name' => '2026 Fall'],
            ['id' => 2, 'name' => 'Purdue University'],
            ['id' => 3, 'name' => 'May 2026'],
            ['id' => 4, 'name' => '2026 Spring'],
            ['id' => 5, 'name' => 'alumni'],
            ['id' => 6, 'name' => 'Fall 2025'],
            ['id' => 7, 'name' => 'Class of 2025, Fall'],
            ['id' => 8, 'name' => 'Autumn 2026'],
        ];

        $ordered = array_map(function ($role) {
            return $role['name'];
        }, RoleOrder::sort($roles));

        $this->assertSame([
            'alumni',
            'Purdue University',
            'Class of 2025, Fall',
            'Fall 2025',
            '2026 Spring',
            'May 2026',
            '2026 Fall',
            'Autumn 2026',
        ], $ordered);
    }

    /**
     * Sorting hands back the rows it was given, without the scratch key
     *
     * @return  void
     */
    public function testSortReturnsTheRowsUnchanged()
    {
        $ordered = RoleOrder::sort([['id' => 9, 'name' => 'May 2026', 'permissions' => '{}']]);

        $this->assertSame([['id' => 9, 'name' => 'May 2026', 'permissions' => '{}']], $ordered);
    }
}
