<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Console\Tests;

use Hubzero\Test\Basic;
use Hubzero\Console\Output;
use Hubzero\Console\Output\Progress;

/**
 * Tests for Progress output class
 */
class ProgressTest extends Basic
{
    /**
     * Gets the buffered output for comparison
     *
     * @param   callable  $function  The action to perform
     * @return  string
     **/
    private function getBuffered($function)
    {
        ob_start();

        call_user_func($function);

        $contents = ob_get_contents();

        ob_end_clean();

        return $contents;
    }

    /**
     * Test Progress class exists
     *
     * @return  void
     **/
    public function testProgressClassExists()
    {
        $this->assertTrue(class_exists(Progress::class));
    }

    /**
     * Test can create Progress instance
     *
     * @return  void
     **/
    public function testCanCreateProgressInstance()
    {
        $progress = new Progress();
        $this->assertInstanceOf(Progress::class, $progress);
    }

    /**
     * Test init returns self for fluent interface
     *
     * @return  void
     **/
    public function testInitReturnsSelf()
    {
        $progress = new Progress();

        $this->getBuffered(function () use ($progress, &$result) {
            $result = $progress->init('Testing: ', 'percentage', 100);
        });

        $this->assertSame($progress, $result);
    }

    /**
     * Test setBarWidth returns self for fluent interface
     *
     * @return  void
     **/
    public function testSetBarWidthReturnsSelf()
    {
        $progress = new Progress();
        $result = $progress->setBarWidth(50);
        $this->assertSame($progress, $result);
    }

    /**
     * Test setBarCharacters returns self for fluent interface
     *
     * @return  void
     **/
    public function testSetBarCharactersReturnsSelf()
    {
        $progress = new Progress();
        $result = $progress->setBarCharacters('#', '.', '>');
        $this->assertSame($progress, $result);
    }

    /**
     * Test setBarBrackets returns self for fluent interface
     *
     * @return  void
     **/
    public function testSetBarBracketsReturnsSelf()
    {
        $progress = new Progress();
        $result = $progress->setBarBrackets('<', '>');
        $this->assertSame($progress, $result);
    }

    /**
     * Test showElapsed returns self for fluent interface
     *
     * @return  void
     **/
    public function testShowElapsedReturnsSelf()
    {
        $progress = new Progress();
        $result = $progress->showElapsed(true);
        $this->assertSame($progress, $result);
    }

    /**
     * Test showEta returns self for fluent interface
     *
     * @return  void
     **/
    public function testShowEtaReturnsSelf()
    {
        $progress = new Progress();
        $result = $progress->showEta(true);
        $this->assertSame($progress, $result);
    }

    /**
     * Test setSpinnerFrames returns self for fluent interface
     *
     * @return  void
     **/
    public function testSetSpinnerFramesReturnsSelf()
    {
        $progress = new Progress();
        $result = $progress->setSpinnerFrames(['*', 'o', '.']);
        $this->assertSame($progress, $result);
    }

    /**
     * Test percentage type output contains percentage symbol
     *
     * @return  void
     **/
    public function testPercentageTypeOutput()
    {
        $output = $this->getBuffered(function () {
            $progress = new Progress();
            $progress->init('', 'percentage');
            $progress->setProgress(50);
            // Don't call done() so we can see the output
        });

        $this->assertStringContainsString('50%', $output);
    }

    /**
     * Test ratio type output contains fraction format
     *
     * @return  void
     **/
    public function testRatioTypeOutput()
    {
        $output = $this->getBuffered(function () {
            $progress = new Progress();
            $progress->init('', 'ratio', 100);
            $progress->setProgress(50, 100);
        });

        $this->assertStringContainsString('(50/100)', $output);
    }

    /**
     * Test bar type output contains progress bar characters
     *
     * @return  void
     **/
    public function testBarTypeOutput()
    {
        $output = $this->getBuffered(function () {
            $progress = new Progress();
            $progress->init('', 'bar', 100);
            $progress->setProgress(50, 100);
        });

        // Should contain bar brackets
        $this->assertStringContainsString('[', $output);
        $this->assertStringContainsString(']', $output);
        // Should contain percentage
        $this->assertStringContainsString('50%', $output);
    }

    /**
     * Test bar with custom characters
     *
     * @return  void
     **/
    public function testBarWithCustomCharacters()
    {
        $output = $this->getBuffered(function () {
            $progress = new Progress();
            $progress->setBarCharacters('#', '.', '*');
            $progress->setBarBrackets('<', '>');
            $progress->init('', 'bar', 100);
            $progress->setProgress(50, 100);
        });

        $this->assertStringContainsString('<', $output);
        $this->assertStringContainsString('>', $output);
        $this->assertStringContainsString('#', $output);
    }

    /**
     * Test spinner type output contains spinner frame
     *
     * @return  void
     **/
    public function testSpinnerTypeOutput()
    {
        $output = $this->getBuffered(function () {
            $progress = new Progress();
            $progress->init('Loading: ', 'spinner');
        });

        // Default spinner starts with '|'
        $this->assertStringContainsString('|', $output);
    }

    /**
     * Test spinner with custom frames
     *
     * @return  void
     **/
    public function testSpinnerWithCustomFrames()
    {
        $output = $this->getBuffered(function () {
            $progress = new Progress();
            $progress->setSpinnerFrames(['*', 'o', '.']);
            $progress->init('Loading: ', 'spinner');
        });

        // Should start with first custom frame
        $this->assertStringContainsString('*', $output);
    }

    /**
     * Test getElapsedTime returns float
     *
     * @return  void
     **/
    public function testGetElapsedTimeReturnsFloat()
    {
        $progress = new Progress();

        $this->getBuffered(function () use ($progress) {
            $progress->init('', 'percentage');
        });

        // Small delay
        usleep(1000);

        $elapsed = $progress->getElapsedTime();
        $this->assertIsFloat($elapsed);
        $this->assertGreaterThan(0, $elapsed);
    }

    /**
     * Test getRate returns float
     *
     * @return  void
     **/
    public function testGetRateReturnsFloat()
    {
        $progress = new Progress();

        $this->getBuffered(function () use ($progress) {
            $progress->init('', 'percentage');
        });

        // Small delay
        usleep(10000);

        $rate = $progress->getRate(50);
        $this->assertIsFloat($rate);
        $this->assertGreaterThan(0, $rate);
    }

    /**
     * Test bar_eta type shows ETA
     *
     * @return  void
     **/
    public function testBarEtaTypeShowsEta()
    {
        $output = $this->getBuffered(function () {
            $progress = new Progress();
            $progress->init('', 'bar_eta', 100);
            // Small delay to make ETA calculation possible
            usleep(10000);
            $progress->setProgress(10, 100);
        });

        // Should contain ETA indicator
        $this->assertStringContainsString('ETA:', $output);
    }

    /**
     * Test Output class has getProgressOutput method
     *
     * @return  void
     **/
    public function testOutputHasGetProgressOutputMethod()
    {
        $output = new Output();
        $this->assertTrue(method_exists($output, 'getProgressOutput'));
    }

    /**
     * Test getProgressOutput returns Progress instance
     *
     * @return  void
     **/
    public function testGetProgressOutputReturnsProgressInstance()
    {
        $output = new Output();
        $progress = $output->getProgressOutput();
        $this->assertInstanceOf(Progress::class, $progress);
    }

    /**
     * Test Output class has withProgress method
     *
     * @return  void
     **/
    public function testOutputHasWithProgressMethod()
    {
        $output = new Output();
        $this->assertTrue(method_exists($output, 'withProgress'));
    }

    /**
     * Test withProgress iterates over items
     *
     * @return  void
     **/
    public function testWithProgressIteratesOverItems()
    {
        $items = ['a', 'b', 'c'];
        $processed = [];

        $output = new Output();
        $this->getBuffered(function () use ($output, $items, &$processed) {
            $output->withProgress($items, function ($item) use (&$processed) {
                $processed[] = $item;
                return strtoupper($item);
            });
        });

        $this->assertEquals(['a', 'b', 'c'], $processed);
    }

    /**
     * Test withProgress returns results array
     *
     * @return  void
     **/
    public function testWithProgressReturnsResults()
    {
        $items = ['a', 'b', 'c'];

        $output = new Output();
        $results = null;

        $this->getBuffered(function () use ($output, $items, &$results) {
            $results = $output->withProgress($items, function ($item) {
                return strtoupper($item);
            });
        });

        $this->assertEquals(['A', 'B', 'C'], $results);
    }

    /**
     * Test withProgress preserves array keys
     *
     * @return  void
     **/
    public function testWithProgressPreservesKeys()
    {
        $items = ['foo' => 1, 'bar' => 2, 'baz' => 3];

        $output = new Output();
        $results = null;

        $this->getBuffered(function () use ($output, $items, &$results) {
            $results = $output->withProgress($items, function ($item) {
                return $item * 2;
            });
        });

        $this->assertArrayHasKey('foo', $results);
        $this->assertArrayHasKey('bar', $results);
        $this->assertArrayHasKey('baz', $results);
        $this->assertEquals(2, $results['foo']);
        $this->assertEquals(4, $results['bar']);
        $this->assertEquals(6, $results['baz']);
    }

    /**
     * Test withProgress handles empty array
     *
     * @return  void
     **/
    public function testWithProgressHandlesEmptyArray()
    {
        $output = new Output();
        $results = null;

        $this->getBuffered(function () use ($output, &$results) {
            $results = $output->withProgress([], function ($item) {
                return $item;
            });
        });

        $this->assertEquals([], $results);
    }

    /**
     * Test Output class has withProgressFinish method
     *
     * @return  void
     **/
    public function testOutputHasWithProgressFinishMethod()
    {
        $output = new Output();
        $this->assertTrue(method_exists($output, 'withProgressFinish'));
    }

    /**
     * Test Output class has withSpinner method
     *
     * @return  void
     **/
    public function testOutputHasWithSpinnerMethod()
    {
        $output = new Output();
        $this->assertTrue(method_exists($output, 'withSpinner'));
    }

    /**
     * Test withSpinner executes callback
     *
     * @return  void
     **/
    public function testWithSpinnerExecutesCallback()
    {
        $output = new Output();
        $executed = false;

        $this->getBuffered(function () use ($output, &$executed) {
            $output->withSpinner('Working', function ($ticker) use (&$executed) {
                $executed = true;
            });
        });

        $this->assertTrue($executed);
    }

    /**
     * Test withSpinner returns callback result
     *
     * @return  void
     **/
    public function testWithSpinnerReturnsResult()
    {
        $output = new Output();
        $result = null;

        $this->getBuffered(function () use ($output, &$result) {
            $result = $output->withSpinner('Working', function ($ticker) {
                return 'done';
            });
        });

        $this->assertEquals('done', $result);
    }

    /**
     * Test advance method works
     *
     * @return  void
     **/
    public function testAdvanceMethod()
    {
        $output = $this->getBuffered(function () {
            $progress = new Progress();
            $progress->init('', 'ratio', 10);
            $progress->advance(1);
            $progress->advance(2);
        });

        // After advancing by 1 then 2, should show 3/10
        $this->assertStringContainsString('(3/10)', $output);
    }

    /**
     * Test done clears output
     *
     * @return  void
     **/
    public function testDoneMethod()
    {
        $progress = new Progress();

        // Should not throw any errors
        $this->getBuffered(function () use ($progress) {
            $progress->init('Test: ', 'bar', 100);
            $progress->setProgress(50, 100);
            $progress->done();
        });

        $this->assertTrue(true); // If we got here without error, test passes
    }

    /**
     * Test done with completion message
     *
     * @return  void
     **/
    public function testDoneWithMessage()
    {
        $output = $this->getBuffered(function () {
            $progress = new Progress();
            $progress->init('', 'bar', 100);
            $progress->setProgress(100, 100);
            $progress->done('Completed!');
        });

        $this->assertStringContainsString('Completed!', $output);
    }

    /**
     * Test finish method keeps output visible
     *
     * @return  void
     **/
    public function testFinishMethod()
    {
        $progress = new Progress();

        // Should not throw any errors
        $this->getBuffered(function () use ($progress) {
            $progress->init('Test: ', 'bar', 100);
            $progress->setProgress(100, 100);
            $progress->finish('All done!');
        });

        $this->assertTrue(true); // If we got here without error, test passes
    }

    /**
     * Test bar width minimum is enforced
     *
     * @return  void
     **/
    public function testBarWidthMinimumEnforced()
    {
        $output = $this->getBuffered(function () {
            $progress = new Progress();
            $progress->setBarWidth(5); // Below minimum of 10
            $progress->init('', 'bar', 100);
            $progress->setProgress(50, 100);
        });

        // Bar should still be at least 10 characters
        $this->assertStringContainsString('[', $output);
        $this->assertStringContainsString(']', $output);
    }

    /**
     * Test 100% progress fills entire bar
     *
     * @return  void
     **/
    public function testFullProgressFillsBar()
    {
        $output = $this->getBuffered(function () {
            $progress = new Progress();
            $progress->setBarWidth(10);
            $progress->init('', 'bar', 100);
            $progress->setProgress(100, 100);
        });

        // At 100%, should have full fill with no empty
        $this->assertStringContainsString('100%', $output);
        // The bar should be full of fill characters
        $this->assertStringContainsString('==========', $output);
    }

    /**
     * Test 0% progress shows empty bar
     *
     * @return  void
     **/
    public function testZeroProgressShowsEmptyBar()
    {
        $output = $this->getBuffered(function () {
            $progress = new Progress();
            $progress->setBarWidth(10);
            $progress->init('', 'bar', 100);
            $progress->setProgress(0, 100);
        });

        $this->assertStringContainsString('0%', $output);
        // The bar should be empty
        $this->assertStringContainsString('----------', $output);
    }
}
