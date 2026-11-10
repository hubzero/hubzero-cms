<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2025 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Menus\Tests;

use Hubzero\Test\Basic;
use PHPUnit\Framework\Attributes\DataProvider;
use Components\Menus\Models\Item;

require_once dirname(__DIR__) . '/models/item.php';

/**
 * Tidying a declared address
 *
 * An item can say what address it answers at instead of taking the one its
 * place in the menu gives it. That value is typed by hand into a text box and
 * then matched against the path of every incoming request, so it has to come
 * out of the box looking like a path and nothing else.
 **/
class RouteTest extends Basic
{
    /**
     * Things people type, and what should be stored
     *
     * @return  array
     */
    public static function routes()
    {
        return [
            'plain'                => ['handbook', 'handbook'],
            'nested'               => ['docs/handbook', 'docs/handbook'],
            'leading slash'        => ['/handbook', 'handbook'],
            'trailing slash'       => ['handbook/', 'handbook'],
            'both'                 => ['/docs/handbook/', 'docs/handbook'],
            'doubled slashes'      => ['docs//handbook', 'docs/handbook'],
            'shouted'              => ['Docs/Handbook', 'docs/handbook'],
            'spaces'               => ['  handbook  ', 'handbook'],
            'inner spaces'         => ['user guide', 'userguide'],
            'hyphens and unders'   => ['a-b_c', 'a-b_c'],
            'a dot'                => ['faq.html', 'faq.html'],
            'climbing out'         => ['../../etc/passwd', 'etc/passwd'],
            'a single dot'         => ['./handbook', 'handbook'],
            'backslashes'          => ['docs\\handbook', 'docs/handbook'],
            'a query string'       => ['page?id=1', 'pageid1'],
            'a fragment'           => ['page#top', 'pagetop'],
            'an absolute url'      => ['https://elsewhere.test/x', 'https/elsewhere.test/x'],
            'a null byte'          => ["handbook\0.php", 'handbook.php'],
            'nothing'              => ['', ''],
            'only slashes'         => ['///', ''],
            'only dots'            => ['../..', ''],
        ];
    }

    /**
     * A typed address comes out as a path or as nothing
     *
     * @param   string  $typed   What somebody put in the box
     * @param   string  $stored  What should be kept
     * @return  void
     */
    #[DataProvider('routes')]
    public function testCleanRoute($typed, $stored)
    {
        $this->assertSame($stored, Item::cleanRoute($typed));
    }

    /**
     * Whatever is stored is a path, every time
     *
     * The value is compared against an incoming request path, so it must never
     * begin or end with a slash, never hold an empty or dot segment, and never
     * hold a character that means something else in a URL.
     *
     * @return  void
     */
    public function testWhatIsStoredIsAlwaysAPath()
    {
        foreach (self::routes() as $case => $pair) {
            $route = Item::cleanRoute($pair[0]);

            if ($route === '') {
                continue;
            }

            $this->assertSame($route, trim($route, '/'), $case . ': no slash at either end');
            $this->assertStringNotContainsString('//', $route, $case . ': no empty segment');
            $this->assertMatchesRegularExpression(
                '#^[a-z0-9._-]+(/[a-z0-9._-]+)*$#',
                $route,
                $case . ': path segments only'
            );

            foreach (explode('/', $route) as $segment) {
                $this->assertNotSame('.', $segment, $case . ': no dot segment');
                $this->assertNotSame('..', $segment, $case . ': no climbing out');
            }
        }
    }

    /**
     * Tidying something already tidy changes nothing
     *
     * The value is cleaned on every save, so a route that survived one save has
     * to survive every later one unchanged or an address would drift on its own.
     *
     * @return  void
     */
    public function testCleaningIsStable()
    {
        foreach (self::routes() as $case => $pair) {
            $once  = Item::cleanRoute($pair[0]);
            $twice = Item::cleanRoute($once);

            $this->assertSame($once, $twice, $case . ': cleaning twice is cleaning once');
        }
    }
}
