<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\View\Tests;

use Hubzero\Test\Basic;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Hubzero\View\View;
use Hubzero\Component\View as ComponentView;
use Hubzero\Plugin\View as PluginView;
use Hubzero\Template\Overrides;
use Hubzero\Template\Tests\Support\Environment;

/**
 * Views search every template override root, highest priority first
 *
 * The supergroup template (inside a supergroup's pages) sits above the
 * site template, which sits above the extension's own layouts. This holds
 * for views constructed directly, not only those a controller hands out.
 *
 * Runs in separate processes because the code under test goes through the
 * global `App` alias, which another test file replaces at load time.
 */
#[RunTestsInSeparateProcesses]
#[PreserveGlobalState(false)]
class OverrideRootsTest extends Basic
{
    /**
     * @var  Environment
     */
    private $env;

    /**
     * @return  void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->env = Environment::create();
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
     * The view's template search stack, top (first searched) first
     *
     * @param   View  $view
     * @return  array
     */
    private function templatePaths(View $view): array
    {
        $paths = new \ReflectionProperty(View::class, '_path');
        $paths->setAccessible(true);

        return $paths->getValue($view)['template'];
    }

    /**
     * The override directories expected for an extension's view, top first
     *
     * @param   string  $extension
     * @param   string  $name
     * @return  array
     */
    private function expectedOverrides(string $extension, string $name): array
    {
        return array_map(function ($root) use ($extension, $name) {
            return $root . '/html/' . $extension . '/' . $name . '/';
        }, Overrides::roots());
    }

    /**
     * @return  void
     */
    public function testBaseViewSearchesSuperGroupThenSiteTemplate()
    {
        $view = new View([
            'name'          => 'foo',
            'base_path'     => $this->env->root . '/com_x',
            'override_path' => 'com_x',
        ]);

        $paths = $this->templatePaths($view);

        $this->assertSame($this->expectedOverrides('com_x', 'foo'), array_slice($paths, 0, 2));
        $this->assertSame($this->env->root . '/com_x/views/foo/tmpl/', $paths[2]);
    }

    /**
     * A single override_root string still works as it always has
     *
     * @return  void
     */
    public function testExplicitOverrideRootStringIsHonoured()
    {
        $view = new View([
            'name'          => 'foo',
            'base_path'     => $this->env->root . '/com_x',
            'override_path' => 'com_x',
            'override_root' => '/elsewhere/html',
        ]);

        $paths = $this->templatePaths($view);

        $this->assertSame('/elsewhere/html/com_x/foo/', $paths[0]);
        $this->assertSame($this->env->root . '/com_x/views/foo/tmpl/', $paths[1]);
    }

    /**
     * A component view built directly, not by a controller, sees the same roots
     *
     * @return  void
     */
    public function testDirectlyConstructedComponentViewSeesEveryRoot()
    {
        $view = new ComponentView([
            'name'          => 'pages',
            'base_path'     => $this->env->root . '/com_groups',
            'override_path' => 'com_groups',
        ]);

        $this->assertSame(
            $this->expectedOverrides('com_groups', 'pages'),
            array_slice($this->templatePaths($view), 0, 2)
        );
    }

    /**
     * @return  void
     */
    public function testPluginViewSearchesSuperGroupThenSiteTemplate()
    {
        $view = new PluginView([
            'folder'    => 'groups',
            'element'   => 'members',
            'name'      => 'browse',
            'base_path' => $this->env->root . '/plg',
        ]);

        $paths = $this->templatePaths($view);

        $this->assertSame($this->expectedOverrides('plg_groups_members', 'browse'), array_slice($paths, 0, 2));
        $this->assertSame($this->env->root . '/plg/views/browse/tmpl/', $paths[2]);
    }

    /**
     * An explicit override_path on a plugin view names the only root searched,
     * and '' turns overrides off
     *
     * @return  void
     */
    public function testPluginViewExplicitOverridePath()
    {
        $base = ['folder' => 'groups', 'element' => 'members', 'name' => 'browse', 'base_path' => $this->env->root . '/plg'];

        $only = $this->templatePaths(new PluginView($base + ['override_path' => '/alt']));
        $this->assertSame('/alt/html/plg_groups_members/browse/', $only[0]);
        $this->assertSame($this->env->root . '/plg/views/browse/tmpl/', $only[1]);

        $none = $this->templatePaths(new PluginView($base + ['override_path' => '']));
        $this->assertSame([$this->env->root . '/plg/views/browse/tmpl/'], $none);
    }

    /**
     * Outside a supergroup only the site template overrides
     *
     * @return  void
     */
    public function testOutsideASuperGroupOnlyTheSiteTemplateOverrides()
    {
        $this->env->withoutSuperGroup();

        $view = new View([
            'name'          => 'foo',
            'base_path'     => $this->env->root . '/com_x',
            'override_path' => 'com_x',
        ]);

        $paths = $this->templatePaths($view);

        $this->assertSame($this->env->siteTemplate . '/html/com_x/foo/', $paths[0]);
        $this->assertSame($this->env->root . '/com_x/views/foo/tmpl/', $paths[1]);
    }
}
