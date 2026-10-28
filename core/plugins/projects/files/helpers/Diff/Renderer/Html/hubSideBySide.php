<?php

namespace Plugins\Projects\Files\Helpers;

/**
 * Side by side HTML diff generator for PHP DiffLib.
 *
 * Renders diff output as bare tbody rows (no table wrapper) for embedding
 * in existing markup. Based on chrisboulton/php-diff.
 *
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */
class HubSideBySide extends \Diff_Renderer_Html_Array
{
    /**
     * Render a and return diff with changes between the two sequences
     * displayed side by side.
     *
     * @return string The generated side by side diff.
     */
    public function render()
    {
        $changes = parent::render();

        $html = '';
        if (empty($changes)) {
            return $html;
        }

        foreach ($changes as $i => $blocks) {
            if ($i > 0) {
                $html .= '<tbody class="Skipped">';
                $html .= '<th>&hellip;</th><td>&nbsp;</td>';
                $html .= '<th>&hellip;</th><td>&nbsp;</td>';
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
                        $html .= '<td class="Left"><span>' . $line . '</span>&nbsp;</span></td>';
                        $html .= '<th>' . $toLine . '</th>';
                        $html .= '<td class="Right"><span>' . $line . '</span>&nbsp;</span></td>';
                        $html .= '</tr>';
                    }
                } elseif ($change['tag'] == 'insert') {
                    // Added lines only on the right side
                    foreach ($change['changed']['lines'] as $no => $line) {
                        $toLine = $change['changed']['offset'] + $no + 1;
                        $html .= '<tr>';
                        $html .= '<th>&nbsp;</th>';
                        $html .= '<td class="Left">&nbsp;</td>';
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
                        $html .= '<td class="Left"><del>' . $line . '</del>&nbsp;</td>';
                        $html .= '<th>&nbsp;</th>';
                        $html .= '<td class="Right">&nbsp;</td>';
                        $html .= '</tr>';
                    }
                } elseif ($change['tag'] == 'replace') {
                    // Show modified lines on both sides
                    if (count($change['base']['lines']) >= count($change['changed']['lines'])) {
                        foreach ($change['base']['lines'] as $no => $line) {
                            $fromLine = $change['base']['offset'] + $no + 1;
                            $html .= '<tr>';
                            $html .= '<th>' . $fromLine . '</th>';
                            $html .= '<td class="Left"><span>' . $line . '</span>&nbsp;</td>';
                            if (!isset($change['changed']['lines'][$no])) {
                                $toLine = '&nbsp;';
                                $changedLine = '&nbsp;';
                            } else {
                                $toLine = $change['base']['offset'] + $no + 1;
                                $changedLine = '<span>' . $change['changed']['lines'][$no] . '</span>';
                            }
                            $html .= '<th>' . $toLine . '</th>';
                            $html .= '<td class="Right">' . $changedLine . '</td>';
                            $html .= '</tr>';
                        }
                    } else {
                        foreach ($change['changed']['lines'] as $no => $changedLine) {
                            if (!isset($change['base']['lines'][$no])) {
                                $fromLine = '&nbsp;';
                                $line = '&nbsp;';
                            } else {
                                $fromLine = $change['base']['offset'] + $no + 1;
                                $line = '<span>' . $change['base']['lines'][$no] . '</span>';
                            }
                            $html .= '<tr>';
                            $html .= '<th>' . $fromLine . '</th>';
                            $html .= '<td class="Left"><span>' . $line . '</span>&nbsp;</td>';
                            $toLine = $change['changed']['offset'] + $no + 1;
                            $html .= '<th>' . $toLine . '</th>';
                            $html .= '<td class="Right">' . $changedLine . '</td>';
                            $html .= '</tr>';
                        }
                    }
                }
                $html .= '</tbody>';
            }
        }
        return $html;
    }
}
