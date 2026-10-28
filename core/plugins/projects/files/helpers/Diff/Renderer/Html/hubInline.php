<?php

namespace Plugins\Projects\Files\Helpers;

/**
 * Inline HTML diff generator for PHP DiffLib.
 *
 * Renders diff output as bare tbody rows (no table wrapper) for embedding
 * in existing markup. Based on chrisboulton/php-diff.
 *
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */
class HubInline extends \Diff_Renderer_Html_Array
{
    /**
     * Render a and return diff with changes between the two sequences
     * displayed inline (under each other)
     *
     * @return string The generated inline diff.
     */
    public function render()
    {
        $changes = parent::render();
        $html = '';
        if (empty($changes)) {
            return $html;
        }

        /*$html .= '<table class="Differences DifferencesInline">';
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<th>Old</th>';
        $html .= '<th>New</th>';
        $html .= '<th>Differences</th>';
        $html .= '</tr>';
        $html .= '</thead>';*/
        foreach ($changes as $i => $blocks) {
            // If this is a separate block, we're condensing code so output ...,
            // indicating a significant portion of the code has been collapsed as
            // it is the same
            if ($i > 0) {
                $html .= '<tbody class="Skipped">';
                $html .= '<th>&hellip;</th>';
                $html .= '<th>&hellip;</th>';
                $html .= '<td>&nbsp;</td>';
                $html .= '</tbody>';
            }

            foreach ($blocks as $change) {
                $html .= '<tbody class="Change' . ucfirst($change['tag']) . '">';
                // Equal changes should be shown on both sides of the diff
                if ($change['tag'] == 'equal') {
                    foreach ($change['base']['lines'] as $no => $line) {
                        $fromLine = $change['base']['offset'] + $no + 1;
                        $toLine = $change['changed']['offset'] + $no + 1;
                        $html .= '<tr>';
                        $html .= '<th>' . $fromLine . '</th>';
                        $html .= '<th>' . $toLine . '</th>';
                        $html .= '<td class="Left">' . $line . '</td>';
                        $html .= '</tr>';
                    }
                } elseif ($change['tag'] == 'insert') {
                    // Added lines only on the right side
                    foreach ($change['changed']['lines'] as $no => $line) {
                        $toLine = $change['changed']['offset'] + $no + 1;
                        $html .= '<tr>';
                        $html .= '<th>&nbsp;</th>';
                        $html .= '<th>' . $toLine . '</th>';
                        $html .= '<td class="Right"><ins>' . $line . '</ins>&nbsp;</td>';
                        $html .= '</tr>';
                    }
                } elseif ($change['tag'] == 'delete') {
                    // Show deleted lines only on the left side
                    foreach ($change['base']['lines'] as $no => $line) {
                        $fromLine = $change['base']['offset'] + $no + 1;
                        $html .= '<tr>';
                        $html .= '<th>' . $fromLine . '</th>';
                        $html .= '<th>&nbsp;</th>';
                        $html .= '<td class="Left"><del>' . $line . '</del>&nbsp;</td>';
                        $html .= '</tr>';
                    }
                } elseif ($change['tag'] == 'replace') {
                    // Show modified lines on both sides
                    foreach ($change['base']['lines'] as $no => $line) {
                        $fromLine = $change['base']['offset'] + $no + 1;
                        $html .= '<tr>';
                        $html .= '<th>' . $fromLine . '</th>';
                        $html .= '<th>&nbsp;</th>';
                        $html .= '<td class="Left"><span>' . $line . '</span></td>';
                        $html .= '</tr>';
                    }

                    foreach ($change['changed']['lines'] as $no => $line) {
                        $toLine = $change['changed']['offset'] + $no + 1;
                        $html .= '<tr>';
                        $html .= '<th>' . $toLine . '</th>';
                        $html .= '<th>&nbsp;</th>';
                        $html .= '<td class="Right"><span>' . $line . '</span></td>';
                        $html .= '</tr>';
                    }
                }
                $html .= '</tbody>';
            }
        }
        //$html .= '</table>';
        return $html;
    }
}
