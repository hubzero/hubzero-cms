<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Document\Tests;

use Hubzero\Test\Basic;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Hubzero\Document\Asset\Stylesheet;
use Hubzero\Template\Tests\Support\Environment;

/**
 * Which copy of an extension's asset gets served
 *
 * Uses mod_announcements' stylesheet, a core asset, as the asset being
 * overridden.
 *
 * Runs in separate processes because the code under test goes through the
 * global `App` alias, which another test file replaces at load time.
 */
#[RunTestsInSeparateProcesses]
#[PreserveGlobalState(false)]
class AssetOverrideTest extends Basic
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
     * @return  void
     */
    public function testCoreAssetIsTheFallback()
    {
        $asset = new Stylesheet('mod_announcements', 'mod_announcements.css');

        $this->assertSame(
            PATH_CORE . '/modules/mod_announcements/assets/css/mod_announcements.css',
            $asset->targetPath()
        );
        $this->assertIsString($asset->overridePath());
        $this->assertSame($this->env->siteTemplate . '/html/mod_announcements/mod_announcements.css', $asset->overridePath());
    }

    /**
     * @return  void
     */
    public function testSiteTemplateOverridesTheAsset()
    {
        $site = $this->env->file('templates/site/html/mod_announcements/mod_announcements.css');

        $asset = new Stylesheet('mod_announcements', 'mod_announcements.css');

        $this->assertSame($site, $asset->targetPath());
    }

    /**
     * @return  void
     */
    public function testSuperGroupTemplateOverridesTheSiteTemplate()
    {
        $this->env->file('templates/site/html/mod_announcements/mod_announcements.css');
        $group = $this->env->file('groups/42/template/html/mod_announcements/mod_announcements.css');

        $asset = new Stylesheet('mod_announcements', 'mod_announcements.css');

        $this->assertSame($group, realpath($asset->targetPath()));
        $this->assertTrue($asset->exists());
    }
}
