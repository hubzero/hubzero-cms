<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Wiki\Parsermarkdown\Markdown;

/**
 * Table rendering with scope="col" on header cells.
 *
 * cebe/markdown emits bare <th> elements; a screen reader needs scope to
 * announce the column header for each data cell. Used in place of the
 * parent's TableTrait::renderTable() by the parser classes below.
 */
trait ScopedTableTrait
{
    /**
     * Render a table block, marking header cells as column headers
     *
     * @param   array   $block
     * @return  string
     */
    protected function renderTable($block)
    {
        $head  = '';
        $body  = '';
        $cols  = $block['cols'];
        $first = true;

        foreach ($block['rows'] as $row) {
            $cellTag = $first ? 'th' : 'td';
            $scope   = $first ? ' scope="col"' : '';
            $tds     = '';

            foreach ($row as $c => $cell) {
                $align = empty($cols[$c]) ? '' : ' align="' . $cols[$c] . '"';
                $tds  .= "<$cellTag$scope$align>" . trim($this->renderAbsy($cell)) . "</$cellTag>";
            }

            if ($first) {
                $head .= "<tr>$tds</tr>\n";
            } else {
                $body .= "<tr>$tds</tr>\n";
            }

            $first = false;
        }

        return $this->composeTable($head, $body);
    }
}
