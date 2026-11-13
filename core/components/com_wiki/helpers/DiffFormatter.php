<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
/**
 * A class to format Diffs
 *
 * This class formats the diff in classic diff format.
 * It is intended that this class be customized via inheritance,
 * to obtain fancier outputs.
 *
 * @todo document
 */

namespace Components\Wiki\Helpers;

class DiffFormatter
{
    /**
     * Number of leading context "lines" to preserve.
     *
     * This should be left at zero for this class, but subclasses
     * may want to set this to other values.
     *
     * @var integer
     */
    public $leading_context_lines = 0;

    /**
     * Number of trailing context "lines" to preserve.
     *
     * This should be left at zero for this class, but subclasses
     * may want to set this to other values.
     *
     * @var integer
     */
    public $trailing_context_lines = 0;

    /**
     * Description for 'i'
     *
     * @var mixed
     */
    public $i = 0;

    /**
     * Format a diff.
     *
     * @param   object  $diff  A Diff object.
     * @return  string  The formatted output.
     */
    public function format($diff, ?\Closure $formatContextOutput = null)
    {
        $xi = $yi = 1;
        $x0 = $y0 = 0;
        $block = false;
        $context = [];

        $nlead = $this->leading_context_lines;
        $ntrail = $this->trailing_context_lines;

        $this->startDiff();

        echo '<table class="diffs">' . "\n";
        echo "\t" . '<tbody>' . "\n";
        foreach ($diff->edits as $edit) {
            //$this->i++;
            if ($edit->type == 'copy') {
                if (is_array($block)) {
                    if (count($edit->orig) <= $nlead + $ntrail) {
                        $block[] = $edit;
                    } else {
                        if ($ntrail) {
                            $context = array_slice($edit->orig, 0, $ntrail);
                            $block[] = new DiffOpCopy($context);
                        }
                        $this->block(
                            $x0,
                            $ntrail + $xi - $x0,
                            $y0,
                            $ntrail + $yi - $y0,
                            $block,
                        );
                        $block = false;
                    }
                }
                $context = $edit->orig;
            } else {
                if (!is_array($block)) {
                    $context = array_slice($context, count($context) - $nlead);
                    $x0 = $xi - count($context);
                    $y0 = $yi - count($context);
                    $block = [];
                    if ($context) {
                        $block[] = new DiffOpCopy($context);
                    }
                }
                $block[] = $edit;
            }

            if ($edit->orig) {
                $xi += count($edit->orig);
            }
            if ($edit->closing) {
                $yi += count($edit->closing);
            }

            foreach ($context as $ctx) {
                if ($formatContextOutput) {
                    $ctx = $formatContextOutput($ctx);
                } else {
                    // Unchanged context lines are raw page source - escape them
                    $ctx = htmlspecialchars($ctx);
                }

                $this->i++;
                echo "\t\t" . '<tr>' . "\n";
                echo "\t\t\t" . '<th>' . $this->i . '</th>' . "\n";
                echo "\t\t\t" . '<td colspan="4">' . $ctx . '</td>' . "\n";
                echo "\t\t" . '</tr>' . "\n";
            }
        }

        if (is_array($block)) {
            $this->block(
                $x0,
                $xi - $x0,
                $y0,
                $yi - $y0,
                $block,
            );
        }
        echo "\t" . '</tbody>' . "\n";
        echo '</table>' . "\n";
        $end = $this->endDiff();

        return $end;
    }

    /**
     * Short description for '_block'
     *
     * Long description (if any) ...
     *
     * @param      mixed $xbeg Parameter description (if any) ...
     * @param      mixed $xlen Parameter description (if any) ...
     * @param      mixed $ybeg Parameter description (if any) ...
     * @param      mixed $ylen Parameter description (if any) ...
     * @param      array &$edits Parameter description (if any) ...
     * @return     void
     */
    public function block($xbeg, $xlen, $ybeg, $ylen, &$edits)
    {
        $this->startBlock($this->blockHeader($xbeg, $xlen, $ybeg, $ylen));
        foreach ($edits as $edit) {
            if ($edit->type == 'copy') {
                $this->context($edit->orig);
            } elseif ($edit->type == 'add') {
                $this->added($edit->closing);
            } elseif ($edit->type == 'delete') {
                $this->deleted($edit->orig);
            } elseif ($edit->type == 'change') {
                $this->changed($edit->orig, $edit->closing);
            } else {
                trigger_error('Unknown edit type', E_USER_ERROR);
            }
        }
        $this->endBlock();
    }

    /**
     * Short description for '_start_diff'
     *
     * Long description (if any) ...
     *
     * @return     void
     */
    public function startDiff()
    {
        ob_start();
    }

    /**
     * Short description for '_end_diff'
     *
     * Long description (if any) ...
     *
     * @return     mixed Return description (if any) ...
     */
    public function endDiff()
    {
        $val = ob_get_contents();
        ob_end_clean();
        return $val;
    }

    /**
     * Short description for '_block_header'
     *
     * Long description (if any) ...
     *
     * @param      string $xbeg Parameter description (if any) ...
     * @param      number $xlen Parameter description (if any) ...
     * @param      string $ybeg Parameter description (if any) ...
     * @param      number $ylen Parameter description (if any) ...
     * @return     mixed Return description (if any) ...
     */
    public function blockHeader($xbeg, $xlen, $ybeg, $ylen)
    {
        if ($xlen > 1) {
            $xbeg .= "," . ((int) $xbeg + $xlen - 1);
        }
        if ($ylen > 1) {
            $ybeg .= "," . ((int) $ybeg + $ylen - 1);
        }

        return $xbeg . ($xlen ? ($ylen ? 'c' : 'd') : 'a') . $ybeg;
    }

    /**
     * Short description for '_start_block'
     *
     * Long description (if any) ...
     *
     * @param      mixed $header Parameter description (if any) ...
     * @return     void
     */
    public function startBlock($header)
    {
        echo $header;
    }

    /**
     * Short description for '_end_block'
     *
     * Long description (if any) ...
     *
     * @return     void
     */
    public function endBlock() {}

    /**
     * Short description for '_lines'
     *
     * Long description (if any) ...
     *
     * @param      array $lines Parameter description (if any) ...
     * @param      string $prefix Parameter description (if any) ...
     * @return     void
     */
    public function lines($lines, $prefix = ' ')
    {
        foreach ($lines as $line) {
            echo "$prefix $line\n";
        }
    }

    /**
     * Short description for '_context'
     *
     * Long description (if any) ...
     *
     * @param      mixed $lines Parameter description (if any) ...
     * @return     void
     */
    public function context($lines)
    {
        $this->lines($lines);
    }

    /**
     * Short description for '_added'
     *
     * Long description (if any) ...
     *
     * @param      mixed $lines Parameter description (if any) ...
     * @return     void
     */
    public function added($lines)
    {
        $this->lines($lines, '>');
    }

    /**
     * Short description for '_deleted'
     *
     * Long description (if any) ...
     *
     * @param      mixed $lines Parameter description (if any) ...
     * @return     void
     */
    public function deleted($lines)
    {
        $this->lines($lines, '<');
    }

    /**
     * Short description for '_changed'
     *
     * Long description (if any) ...
     *
     * @param      mixed $orig Parameter description (if any) ...
     * @param      mixed $closing Parameter description (if any) ...
     * @return     void
     */
    public function changed($orig, $closing)
    {
        $this->deleted($orig);
        echo "---\n";
        $this->added($closing);
    }
}
