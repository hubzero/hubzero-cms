<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Component\Tests;

use Hubzero\Test\Basic;
use Hubzero\Container\Container;
use Hubzero\Facades\Facade;
use Hubzero\Component\SiteController;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;

/**
 * SiteController::requireViewLevel() and canView()
 *
 * Runs in separate processes because the controller goes through the
 * global `App` alias, which another test file replaces at load time.
 */
#[RunTestsInSeparateProcesses]
#[PreserveGlobalState(false)]
class ViewLevelGateTest extends Basic
{
    /**
     * The container the bootstrap installed, restored after each test
     *
     * @var  mixed
     */
    private $previousApp;

    /**
     * What App::redirect() was called with, if anything
     *
     * @var  array|null
     */
    private $redirect;

    /**
     * Swap in a container whose services answer what the gate asks
     *
     * @param   bool   $guest   Is the current user a guest
     * @param   array  $levels  View levels the current user holds
     * @return  void
     */
    private function boot(bool $guest, array $levels): void
    {
        $test = $this;
        $test->redirect = null;
        $app = new Container();

        $app['user'] = function () use ($guest, $levels) {
            return new class ($guest, $levels) {
                private $guest;
                private $levels;
                public function __construct($guest, $levels)
                {
                    $this->guest = $guest;
                    $this->levels = $levels;
                }
                public function isGuest()
                {
                    return $this->guest;
                }
                public function getAuthorisedViewLevels()
                {
                    return $this->levels;
                }
            };
        };

        $app['router'] = function () {
            return new class {
                public function url($url, $xhtml = true)
                {
                    return $url;
                }
            };
        };

        $app['request'] = function () {
            return new class {
                public function current($query = false)
                {
                    return '/publications/browse?tag=data';
                }
            };
        };

        $app['language'] = function () {
            return new class {
                public function txt($key)
                {
                    return $key;
                }
            };
        };

        $app['app'] = function () use ($app, $test) {
            return new class ($app, $test) {
                private $container;
                private $test;
                public function __construct($container, $test)
                {
                    $this->container = $container;
                    $this->test = $test;
                }
                public function get($id)
                {
                    return $this->container->get($id);
                }
                public function has($id)
                {
                    return $this->container->has($id);
                }
                public function redirect($url, $message = null, $type = 'message')
                {
                    $this->test->recordRedirect($url, $message, $type);
                }
                public function abort($code, $message = '')
                {
                    throw new \RuntimeException($message, $code);
                }
            };
        };

        $this->previousApp = Facade::getApplication();
        Facade::setApplication($app);
    }

    /**
     * Called by the app stub
     *
     * @param   string       $url
     * @param   string|null  $message
     * @param   string       $type
     * @return  void
     */
    public function recordRedirect($url, $message, $type): void
    {
        $this->redirect = compact('url', 'message', 'type');
    }

    /**
     * @return  void
     */
    protected function tearDown(): void
    {
        if ($this->previousApp !== null) {
            Facade::setApplication($this->previousApp);
        }

        parent::tearDown();
    }

    /**
     * A controller instance without running the constructor, which needs
     * a routed request and a component
     *
     * @return  SiteController
     */
    private function controller(): SiteController
    {
        $class = new \ReflectionClass(SiteController::class);

        return $class->newInstanceWithoutConstructor();
    }

    /**
     * Call a protected gate method
     *
     * @param   string  $name
     * @param   array   $args
     * @return  mixed
     */
    private function call(string $name, array $args)
    {
        $method = new \ReflectionMethod(SiteController::class, $name);
        $method->setAccessible(true);

        return $method->invokeArgs($this->controller(), $args);
    }

    /**
     * @return  void
     */
    public function testCanViewChecksTheUsersLevels()
    {
        $this->boot(false, [1, 2]);

        $this->assertTrue($this->call('canView', [1]));
        $this->assertTrue($this->call('canView', ['2']));
        $this->assertFalse($this->call('canView', [3]));
    }

    /**
     * A user holding the level passes with no redirect
     *
     * @return  void
     */
    public function testHoldingTheLevelPasses()
    {
        $this->boot(true, [1]);

        $this->assertTrue($this->call('requireViewLevel', [1, 'COM_X_LOGIN']));
        $this->assertNull($this->redirect);
    }

    /**
     * A guest without the level is sent to log in and brought back
     *
     * @return  void
     */
    public function testGuestWithoutTheLevelIsSentToLogIn()
    {
        $this->boot(true, [1]);

        $this->assertFalse($this->call('requireViewLevel', [2, 'COM_X_LOGIN']));

        $this->assertNotNull($this->redirect);
        $this->assertSame(
            'index.php?option=com_users&view=login&return=' . base64_encode('/publications/browse?tag=data'),
            $this->redirect['url']
        );
        $this->assertSame('COM_X_LOGIN', $this->redirect['message']);
        $this->assertSame('warning', $this->redirect['type']);
    }

    /**
     * Without a message the generic login notice is used
     *
     * @return  void
     */
    public function testDefaultMessage()
    {
        $this->boot(true, [1]);

        $this->call('requireViewLevel', [2]);

        $this->assertSame('JGLOBAL_YOU_MUST_LOGIN_FIRST', $this->redirect['message']);
    }

    /**
     * A logged-in user without the level gets a 403, not a login page
     *
     * @return  void
     */
    public function testLoggedInUserWithoutTheLevelGetsForbidden()
    {
        $this->boot(false, [1, 2]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionCode(403);
        $this->expectExceptionMessage('COM_X_LOGIN');

        $this->call('requireViewLevel', [3, 'COM_X_LOGIN']);
    }
}
