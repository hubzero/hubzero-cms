<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Template\Tests;

use Hubzero\Test\Basic;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Hubzero\Template\Overrides;
use Hubzero\Template\Tests\Support\Environment;

/**
 * Which template roots may override extension views and assets
 *
 * Runs in separate processes because the code under test goes through the
 * global `App` alias, which another test file replaces at load time.
 */
#[RunTestsInSeparateProcesses]
#[PreserveGlobalState(false)]
class OverridesTest extends Basic
{
    /**
     * @var  Environment
     */
    private $env;

    /**
     * @return  void
     */
    protected function tearDown(): void
    {
        if ($this->env) {
            $this->env->restore();
        }

        parent::tearDown();
    }

    /**
     * Inside a supergroup its template comes first, then the site template
     *
     * @return  void
     */
    public function testSuperGroupTemplateOutranksSiteTemplate()
    {
        $this->env = Environment::create();

        $roots = Overrides::roots();

        $this->assertCount(2, $roots);
        $this->assertSame(realpath($this->env->groupBase . '/template'), realpath($roots[0]));
        $this->assertSame($this->env->siteTemplate, $roots[1]);
    }

    /**
     * A supergroup without a template directory contributes nothing
     *
     * @return  void
     */
    public function testSuperGroupWithoutTemplateIsSkipped()
    {
        $this->env = Environment::create(false);

        $this->assertNull(Overrides::superGroupTemplatePath());
        $this->assertSame([$this->env->siteTemplate], Overrides::roots());
    }

    /**
     * Outside a supergroup only the site template applies
     *
     * @return  void
     */
    public function testNoSuperGroupGivesSiteTemplateOnly()
    {
        $this->env = Environment::create();
        $this->env->withoutSuperGroup();

        $this->assertFalse(Overrides::superGroup());
        $this->assertSame([$this->env->siteTemplate], Overrides::roots());
    }

    /**
     * With nothing set, a request that names no group resolves to no supergroup
     *
     * @return  void
     */
    public function testResolvesToNoneWhenTheRequestNamesNoGroup()
    {
        $this->env = Environment::create();
        Overrides::reset();

        $this->assertFalse(Overrides::superGroup());
        $this->assertSame([$this->env->siteTemplate], Overrides::roots());
    }
}
