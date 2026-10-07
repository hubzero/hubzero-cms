<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Console\Tests;

use Hubzero\Test\Basic;
use Hubzero\Console\PrivilegeNotifier;
use Hubzero\System\PrivilegeManager;
use Mockery as m;

/**
 * PrivilegeNotifier startup handling
 *
 * Output is captured through an injected memory stream, and the prompt is
 * kept out of the way by marking the run non-interactive on argv, the same
 * way a scripted caller would.
 */
class PrivilegeNotifierTest extends Basic
{
    /**
     * argv before the test put its flag on it
     *
     * @var  array|null
     */
    private $argv;

    /**
     * Captured notifier output
     *
     * @var  resource
     */
    private $stream;

    /**
     * Mark the run non-interactive and set up a capture stream
     *
     * @return  void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->argv = $_SERVER['argv'] ?? null;
        $_SERVER['argv'] = array_merge((array) $this->argv, ['--no-interaction']);

        $this->stream = fopen('php://memory', 'w+');
    }

    /**
     * Restore argv
     *
     * @return  void
     */
    protected function tearDown(): void
    {
        if ($this->argv === null) {
            unset($_SERVER['argv']);
        } else {
            $_SERVER['argv'] = $this->argv;
        }

        fclose($this->stream);
        m::close();

        parent::tearDown();
    }

    /**
     * Build a notifier over a mocked privilege manager
     *
     * @param   bool       $sudo
     * @param   bool       $root
     * @param   bool|null  $dropResult  What initializeSudoEnvironment() returns, or null if it must not be called
     * @return  PrivilegeNotifier
     */
    private function notifier(bool $sudo, bool $root, ?bool $dropResult = null): PrivilegeNotifier
    {
        $privileges = m::mock(PrivilegeManager::class);
        $privileges->shouldReceive('isSudo')->andReturn($sudo);
        $privileges->shouldReceive('isRoot')->andReturn($root);
        $privileges->shouldReceive('getSudoUser')->andReturn($sudo ? 'alice' : null);

        if ($dropResult === null) {
            $privileges->shouldNotReceive('initializeSudoEnvironment');
        } else {
            $privileges->shouldReceive('initializeSudoEnvironment')->once()->andReturn($dropResult);
        }

        return (new PrivilegeNotifier($privileges))->setStream($this->stream);
    }

    /**
     * What the notifier wrote
     *
     * @return  string
     */
    private function captured(): string
    {
        rewind($this->stream);
        return stream_get_contents($this->stream);
    }

    /**
     * A plain user gets no notice at all
     *
     * @return  void
     */
    public function testRegularUserIsSilent()
    {
        $this->assertTrue($this->notifier(false, false)->handleStartup());
        $this->assertSame('', $this->captured());
    }

    /**
     * sudo to a non-root user has nothing to drop and says nothing
     *
     * @return  void
     */
    public function testSudoToNonRootIsSilent()
    {
        $this->assertTrue($this->notifier(true, false)->handleStartup());
        $this->assertSame('', $this->captured());
    }

    /**
     * sudo to root drops and reports the user it dropped to
     *
     * @return  void
     */
    public function testSudoToRootDropsAndReports()
    {
        $this->assertTrue($this->notifier(true, true, true)->handleStartup());

        $out = $this->captured();
        $this->assertStringContainsString('Sudo Environment Detected', $out);
        $this->assertStringContainsString("Now running as user 'alice'", $out);
        $this->assertStringNotContainsString('Security Warning', $out);
    }

    /**
     * When the drop fails the process is still root and gets the root
     * warning, not a banner claiming a switch
     *
     * @return  void
     */
    public function testFailedDropFallsBackToRootWarning()
    {
        $this->assertTrue($this->notifier(true, true, false)->handleStartup());

        $out = $this->captured();
        $this->assertStringNotContainsString('Now running as user', $out);
        $this->assertStringContainsString('Security Warning', $out);
        $this->assertStringContainsString('continuing as root', $out);
    }

    /**
     * Plain root gets the warning; non-interactive runs carry on
     *
     * @return  void
     */
    public function testRootWarnsAndContinuesNonInteractively()
    {
        $this->assertTrue($this->notifier(false, true)->handleStartup());

        $out = $this->captured();
        $this->assertStringContainsString('Security Warning', $out);
        $this->assertStringContainsString('continuing as root', $out);
    }

    /**
     * No escape codes unless the stream is a terminal
     *
     * @return  void
     */
    public function testNoAnsiOnANonTerminalStream()
    {
        $this->notifier(true, true, true)->handleStartup();

        $this->assertStringNotContainsString("\033[", $this->captured());
    }

    /**
     * An explicit ansi setting wins over the stream
     *
     * @return  void
     */
    public function testExplicitAnsiIsHonoured()
    {
        $this->notifier(true, true, true)->setAnsi(true)->handleStartup();

        $this->assertStringContainsString("\033[31m", $this->captured());
    }

    /**
     * --no-colors on argv turns colour off even when asked for by default
     *
     * @return  void
     */
    public function testNoColorsFlagIsHonoured()
    {
        $_SERVER['argv'][] = '--no-colors';

        $this->notifier(true, true, true)->handleStartup();

        $this->assertStringNotContainsString("\033[", $this->captured());
    }
}
