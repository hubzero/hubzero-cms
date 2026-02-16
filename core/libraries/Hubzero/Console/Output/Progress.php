<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Console\Output;

use Hubzero\Console\Output;

/**
 * Output class for rendering progress bar/text
 *
 * Supports multiple display styles:
 * - 'percentage': Simple "45%" display
 * - 'ratio': Displays "(45/100)"
 * - 'bar': Visual progress bar "[=====>----] 45%"
 * - 'bar_eta': Bar with estimated time remaining "[=====>----] 45% ETA: 0:30"
 *
 * Usage:
 * ```php
 * $progress = $output->getProgressOutput();
 * $progress->init('Processing: ', 'bar', 100);
 * for ($i = 0; $i <= 100; $i++) {
 *     $progress->setProgress($i, 100);
 *     // ... do work ...
 * }
 * $progress->done('Completed!');
 * ```
 **/
class Progress extends Output
{
    /**
     * Track content length
     *
     * @var  int
     **/
    private $contentLength = 0;

    /**
     * Save initial message length
     *
     * @var  int
     **/
    private $initMessageLength = 0;

    /**
     * Progress bar width in characters
     *
     * @var  int
     **/
    private $barWidth = 30;

    /**
     * Progress bar fill character
     *
     * @var  string
     **/
    private $barFillChar = '=';

    /**
     * Progress bar empty character
     *
     * @var  string
     **/
    private $barEmptyChar = '-';

    /**
     * Progress bar pointer character
     *
     * @var  string
     **/
    private $barPointerChar = '>';

    /**
     * Progress bar left bracket
     *
     * @var  string
     **/
    private $barLeftBracket = '[';

    /**
     * Progress bar right bracket
     *
     * @var  string
     **/
    private $barRightBracket = ']';

    /**
     * Current progress type
     *
     * @var  string
     **/
    private $type = 'percentage';

    /**
     * Start time for ETA calculation
     *
     * @var  float|null
     **/
    private $startTime = null;

    /**
     * Total items for progress tracking
     *
     * @var  int|null
     **/
    private $total = null;

    /**
     * Show elapsed time in output
     *
     * @var  bool
     **/
    private $showElapsed = false;

    /**
     * Show ETA in output
     *
     * @var  bool
     **/
    private $showEta = false;

    /**
     * Spinner frames for indeterminate progress
     *
     * @var  array
     **/
    private $spinnerFrames = ['|', '/', '-', '\\'];

    /**
     * Current spinner frame index
     *
     * @var  int
     **/
    private $spinnerIndex = 0;

    /**
     * Initialize progress counter
     *
     * @param   string  $initMessage  Initial message
     * @param   string  $type         Progress type ('percentage', 'ratio', 'bar', 'bar_eta', 'spinner')
     * @param   int     $total        Total number of progress points
     * @return  $this
     **/
    public function init($initMessage = null, $type = 'percentage', $total = null)
    {
        // Force interactivity of this class (this doesn't affect our primary output class)
        $this->makeInteractive();

        // Store progress type and total
        $this->type = $type;
        $this->total = $total;
        $this->startTime = microtime(true);

        // Configure display options based on type
        $this->showEta = ($type === 'bar_eta');
        $this->showElapsed = false;

        if (isset($initMessage)) {
            // Add the intital message
            $this->addString($initMessage);

            // Track some string lengths
            $this->initMessageLength = strlen($initMessage);
        }

        switch ($type) {
            case 'ratio':
                $this->setProgress(0, $total);
                break;
            case 'bar':
            case 'bar_eta':
                $this->setProgress(0, $total);
                break;
            case 'spinner':
                $this->setSpinnerProgress();
                break;
            case 'percentage':
            default:
                // Set current progress to 0
                $this->setProgress('0');
                break;
        }

        return $this;
    }

    /**
     * Set progress bar width
     *
     * @param   int  $width  Width in characters
     * @return  $this
     **/
    public function setBarWidth(int $width): self
    {
        $this->barWidth = max(10, $width);
        return $this;
    }

    /**
     * Set progress bar characters
     *
     * @param   string  $fill     Fill character (default '=')
     * @param   string  $empty    Empty character (default '-')
     * @param   string  $pointer  Pointer character (default '>')
     * @return  $this
     **/
    public function setBarCharacters(string $fill = '=', string $empty = '-', string $pointer = '>'): self
    {
        $this->barFillChar = $fill;
        $this->barEmptyChar = $empty;
        $this->barPointerChar = $pointer;
        return $this;
    }

    /**
     * Set progress bar brackets
     *
     * @param   string  $left   Left bracket (default '[')
     * @param   string  $right  Right bracket (default ']')
     * @return  $this
     **/
    public function setBarBrackets(string $left = '[', string $right = ']'): self
    {
        $this->barLeftBracket = $left;
        $this->barRightBracket = $right;
        return $this;
    }

    /**
     * Enable or disable showing elapsed time
     *
     * @param   bool  $show  Whether to show elapsed time
     * @return  $this
     **/
    public function showElapsed(bool $show = true): self
    {
        $this->showElapsed = $show;
        return $this;
    }

    /**
     * Enable or disable showing ETA
     *
     * @param   bool  $show  Whether to show ETA
     * @return  $this
     **/
    public function showEta(bool $show = true): self
    {
        $this->showEta = $show;
        return $this;
    }

    /**
     * Set custom spinner frames
     *
     * @param   array  $frames  Array of spinner frame characters
     * @return  $this
     **/
    public function setSpinnerFrames(array $frames): self
    {
        if (count($frames) > 0) {
            $this->spinnerFrames = $frames;
        }
        return $this;
    }

    /**
     * Set the current progress val
     *
     * @param   int  $val  Progress value
     * @param   int  $tot  Total value
     * @return  void
     **/
    public function setProgress($val, $tot = null)
    {
        if ($this->contentLength > 0) {
            // Back up current length of content
            $this->backspace($this->contentLength);
        }

        // Update total if provided
        if (!is_null($tot)) {
            $this->total = $tot;
        }

        // Generate content based on type
        $content = $this->formatProgress($val);

        // Save length of content for next call
        $this->contentLength = strlen($content);

        // Add the string
        $this->addString($content);
    }

    /**
     * Update spinner for indeterminate progress
     *
     * @param   string|null  $message  Optional message to display with spinner
     * @return  void
     **/
    public function setSpinnerProgress($message = null)
    {
        if ($this->contentLength > 0) {
            $this->backspace($this->contentLength);
        }

        $frame = $this->spinnerFrames[$this->spinnerIndex % count($this->spinnerFrames)];
        $this->spinnerIndex++;

        $content = $frame;
        if ($message !== null) {
            $content .= ' ' . $message;
        }

        $this->contentLength = strlen($content);
        $this->addString($content);
    }

    /**
     * Advance progress by a given amount
     *
     * @param   int  $step  Amount to advance (default 1)
     * @return  void
     **/
    public function advance(int $step = 1)
    {
        static $current = 0;
        $current += $step;
        $this->setProgress($current, $this->total);
    }

    /**
     * Format progress output based on type
     *
     * @param   int  $val  Current progress value
     * @return  string
     **/
    private function formatProgress($val): string
    {
        $content = '';

        switch ($this->type) {
            case 'bar':
            case 'bar_eta':
                $content = $this->formatBarProgress($val);
                break;

            case 'ratio':
                $content = "({$val}/{$this->total})";
                break;

            case 'spinner':
                $frame = $this->spinnerFrames[$this->spinnerIndex % count($this->spinnerFrames)];
                $content = $frame;
                break;

            case 'percentage':
            default:
                $content = "{$val}%";
                break;
        }

        // Add time information if enabled
        $timeInfo = $this->formatTimeInfo($val);
        if (!empty($timeInfo)) {
            $content .= ' ' . $timeInfo;
        }

        return $content;
    }

    /**
     * Format visual progress bar
     *
     * @param   int  $val  Current progress value
     * @return  string
     **/
    private function formatBarProgress($val): string
    {
        $total = $this->total ?: 100;
        $percent = ($total > 0) ? min(100, ($val / $total) * 100) : 0;
        $filledWidth = (int) round(($percent / 100) * $this->barWidth);

        // Build the bar
        $bar = $this->barLeftBracket;

        if ($filledWidth > 0) {
            // Fill portion
            $bar .= str_repeat($this->barFillChar, max(0, $filledWidth - 1));

            // Pointer (or fill if at 100%)
            if ($percent >= 100) {
                $bar .= $this->barFillChar;
            } else {
                $bar .= $this->barPointerChar;
            }
        }

        // Empty portion
        $emptyWidth = $this->barWidth - $filledWidth;
        $bar .= str_repeat($this->barEmptyChar, max(0, $emptyWidth));

        $bar .= $this->barRightBracket;

        // Add percentage
        $bar .= sprintf(' %3d%%', (int) $percent);

        // Add ratio if we have a total
        if ($this->total !== null) {
            $bar .= sprintf(' (%d/%d)', $val, $this->total);
        }

        return $bar;
    }

    /**
     * Format time information (elapsed and/or ETA)
     *
     * @param   int  $val  Current progress value
     * @return  string
     **/
    private function formatTimeInfo($val): string
    {
        $parts = [];

        $elapsed = microtime(true) - $this->startTime;

        if ($this->showElapsed) {
            $parts[] = 'Elapsed: ' . $this->formatDuration($elapsed);
        }

        if ($this->showEta && $val > 0 && $this->total !== null && $val < $this->total) {
            $eta = $this->calculateEta($val, $elapsed);
            if ($eta !== null) {
                $parts[] = 'ETA: ' . $this->formatDuration($eta);
            }
        }

        return implode(' ', $parts);
    }

    /**
     * Calculate estimated time remaining
     *
     * @param   int    $current  Current progress value
     * @param   float  $elapsed  Elapsed time in seconds
     * @return  float|null
     **/
    private function calculateEta($current, float $elapsed): ?float
    {
        if ($current <= 0 || $this->total === null || $current >= $this->total) {
            return null;
        }

        $rate = $current / $elapsed;
        $remaining = $this->total - $current;

        return $remaining / $rate;
    }

    /**
     * Format duration in human-readable format
     *
     * @param   float  $seconds  Duration in seconds
     * @return  string
     **/
    private function formatDuration(float $seconds): string
    {
        if ($seconds < 0) {
            return '0:00';
        }

        $hours = (int) floor($seconds / 3600);
        $minutes = (int) floor(fmod($seconds, 3600) / 60);
        $secs = (int) floor(fmod($seconds, 60));

        if ($hours > 0) {
            return sprintf('%d:%02d:%02d', $hours, $minutes, $secs);
        }

        return sprintf('%d:%02d', $minutes, $secs);
    }

    /**
     * Get elapsed time since progress started
     *
     * @return  float  Elapsed time in seconds
     **/
    public function getElapsedTime(): float
    {
        if ($this->startTime === null) {
            return 0.0;
        }

        return microtime(true) - $this->startTime;
    }

    /**
     * Get current items per second rate
     *
     * @param   int  $current  Current progress value
     * @return  float
     **/
    public function getRate(int $current): float
    {
        $elapsed = $this->getElapsedTime();
        if ($elapsed <= 0) {
            return 0.0;
        }

        return $current / $elapsed;
    }

    /**
     * Finish progress output
     *
     * @param   string|null  $message  Optional completion message
     * @return  void
     **/
    public function done($message = null)
    {
        // Compute the total length of the output
        $length = $this->contentLength + $this->initMessageLength;

        // Back up all the way
        $this->backspace($length, true);

        // Show completion message if provided
        if ($message !== null) {
            $this->addLine($message, 'success');
        }

        // In case this gets used again...
        $this->contentLength     = 0;
        $this->initMessageLength = 0;
        $this->startTime         = null;
        $this->total             = null;
        $this->spinnerIndex      = 0;
    }

    /**
     * Finish progress but keep the final state visible
     *
     * @param   string|null  $message  Optional message to append
     * @return  void
     **/
    public function finish($message = null)
    {
        if ($message !== null) {
            $this->addString(' ' . $message);
        }
        $this->addLine('');

        // Reset state
        $this->contentLength     = 0;
        $this->initMessageLength = 0;
        $this->startTime         = null;
        $this->total             = null;
        $this->spinnerIndex      = 0;
    }
}
