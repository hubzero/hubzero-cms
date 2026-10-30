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
     * Root on a hub that belongs to another account becomes that account,
     * says so, and gets no root warning
     *
     * @return  void
     */
    public function testRootOnAnotherAccountsHubSwitchesToItsOwner()
    {
        $privileges = m::mock(PrivilegeManager::class);
        $privileges->shouldReceive('isSudo')->andReturn(true);
        $privileges->shouldReceive('isRoot')->andReturn(true);
        $privileges->shouldReceive('getSudoUser')->andReturn('alice');
        $privileges->shouldReceive('switchToOwnerOf')->once()->with(__DIR__)->andReturn('www-data');
        $privileges->shouldNotReceive('initializeSudoEnvironment');

        $notifier = (new PrivilegeNotifier($privileges))->setStream($this->stream);

        $this->assertTrue($notifier->handleStartup(__DIR__));

        $out = $this->captured();
        $this->assertStringContainsString("Running as 'www-data', the account that owns the hub's files", $out);
        $this->assertStringNotContainsString('Security Warning', $out);
        $this->assertStringNotContainsString('Sudo Environment Detected', $out);
    }

    /**
     * sudo on a checkout that belongs to the person who ran it keeps the
     * existing handling: drop to them, with escalation still possible
     *
     * @return  void
     */
    public function testSudoOnYourOwnCheckoutKeepsSudoHandling()
    {
        $me = posix_getpwuid(fileowner(__DIR__))['name'];

        $privileges = m::mock(PrivilegeManager::class);
        $privileges->shouldReceive('isSudo')->andReturn(true);
        $privileges->shouldReceive('isRoot')->andReturn(true);
        $privileges->shouldReceive('getSudoUser')->andReturn($me);
        $privileges->shouldNotReceive('switchToOwnerOf');
        $privileges->shouldReceive('initializeSudoEnvironment')->once()->andReturn(true);

        $notifier = (new PrivilegeNotifier($privileges))->setStream($this->stream);

        $this->assertTrue($notifier->handleStartup(__DIR__));
        $this->assertStringContainsString("Now running as user '{$me}'", $this->captured());
    }

    /**
     * Someone who is not root runs as themselves, wherever the hub is
     *
     * @return  void
     */
    public function testRegularUserNeverSwitches()
    {
        $privileges = m::mock(PrivilegeManager::class);
        $privileges->shouldReceive('isSudo')->andReturn(false);
        $privileges->shouldReceive('isRoot')->andReturn(false);
        $privileges->shouldNotReceive('switchToOwnerOf');

        $notifier = (new PrivilegeNotifier($privileges))->setStream($this->stream);

        $this->assertTrue($notifier->handleStartup(__DIR__));
        $this->assertSame('', $this->captured());
    }

    /**
     * Without root, there is nothing to switch and nothing changes
     *
     * @return  void
     */
    public function testSwitchingNeedsRoot()
    {
        if (function_exists('posix_getuid') && posix_getuid() === 0) {
            $this->markTestSkipped('Running as root');
        }

        $privileges = new PrivilegeManager();

        $this->assertNull($privileges->switchToOwnerOf(__DIR__));
        $this->assertNull($privileges->getSwitchedTo());
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
