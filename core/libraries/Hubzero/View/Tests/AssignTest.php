<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2026 Purdue University.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\View\Tests;

use Hubzero\Facades\Facade;
use Hubzero\View\View;
use PHPUnit\Framework\TestCase;

/**
 * assign() hands a view its data; the view's own state is not data, and
 * nothing assigned may replace it.
 */
class AssignTest extends TestCase
{
    /**
     * A view asks the request for the site's base URL
     *
     * @return  void
     */
    protected function setUp(): void
    {
        $app = Facade::getApplication();
        if (!isset($app['request'])) {
            $app['request'] = new BaseUrlRequest();
            $this->addedRequest = true;
        }
    }

    /**
     * @return  void
     */
    protected function tearDown(): void
    {
        if ($this->addedRequest) {
            unset(Facade::getApplication()['request']);
        }
    }

    /**
     * @var  bool
     */
    private $addedRequest = false;

    /**
     * @return  View
     */
    private function view()
    {
        return new View([
            'override_root' => [],
            'name'          => 'things',
            'base_path'     => sys_get_temp_dir(),
        ]);
    }

    /**
     * @return  void
     */
    public function testDataIsAssigned(): void
    {
        $view = $this->view();

        $this->assertTrue($view->assign(['title' => 'A title']));
        $this->assertTrue($view->assign('rows', [1, 2]));
        $this->assertTrue($view->assign((object) ['total' => 2]));

        $this->assertSame('A title', $view->get('title'));
        $this->assertSame([1, 2], $view->get('rows'));
        $this->assertSame(2, $view->get('total'));
    }

    /**
     * @return  void
     */
    public function testTheViewsOwnStateIsNotAssigned(): void
    {
        $view = $this->view();

        $view->assign(['viewName' => 'other', 'layoutName' => 'other', '_hidden' => true]);
        $view->assign((object) ['viewName' => 'other']);

        $this->assertFalse($view->assign('layoutName', 'other'));
        $this->assertFalse($view->assign('_hidden', true));
        $this->assertSame('things', $view->getName());
        $this->assertSame('default', $view->getLayout());
        $this->assertFalse(isset($view->_hidden));
    }
}
