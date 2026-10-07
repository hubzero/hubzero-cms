<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Module\Tests;

use Hubzero\Test\Basic;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Hubzero\Module\Loader;
use Hubzero\Facades\Facade;
use Hubzero\Template\Tests\Support\Environment;

/**
 * Where a module's layout is loaded from
 *
 * Uses mod_announcements, a core module with a default layout, as the
 * module whose layout is being overridden.
 *
 * Runs in separate processes because the code under test goes through the
 * global `App` alias, which another test file replaces at load time.
 */
#[RunTestsInSeparateProcesses]
#[PreserveGlobalState(false)]
class LayoutPathTest extends Basic
{
    /**
     * @var  Environment
     */
    private $env;

    /**
     * @var  Loader
     */
    private $loader;

    /**
     * @return  void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->env = Environment::create();
        $this->loader = new Loader(Facade::getApplication());
    }

    /**
     * @return  void
     */
    protected function tearDown(): void
    {
        $this->env->restore();

        parent::tearDown();
    }

    /**
     * @return  void
     */
    public function testModuleLayoutIsTheFallback()
    {
        $this->assertSame(
            PATH_CORE . '/modules/mod_announcements/tmpl/default.php',
            $this->loader->getLayoutPath('mod_announcements')
        );
    }

    /**
     * @return  void
     */
    public function testSiteTemplateOverridesTheModule()
    {
        $site = $this->env->file('templates/site/html/mod_announcements/default.php');

        $this->assertSame($site, $this->loader->getLayoutPath('mod_announcements'));
    }

    /**
     * @return  void
     */
    public function testSuperGroupTemplateOverridesTheSiteTemplate()
    {
        $this->env->file('templates/site/html/mod_announcements/default.php');
        $group = $this->env->file('groups/42/template/html/mod_announcements/default.php');

        $this->assertSame($group, realpath($this->loader->getLayoutPath('mod_announcements')));
        $this->assertSame($group, realpath($this->loader->getLayoutPath('mod_announcements', '_:default')));
    }

    /**
     * Naming a template in the layout asks for that template alone
     *
     * @return  void
     */
    public function testAnExplicitlyNamedTemplateIsNotOverridden()
    {
        $site = $this->env->file('templates/site/html/mod_announcements/default.php');
        $this->env->file('groups/42/template/html/mod_announcements/default.php');

        $this->assertSame($site, $this->loader->getLayoutPath('mod_announcements', 'site:default'));
    }
}
