<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Document;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;

// No direct access.
defined('_HZEXEC_') or die();

Document::setType('xml');

// Output XML header.
echo '<?xml version="1.0" encoding="UTF-8" ?>' . "\n";

// Output root element.
echo '<root>' . "\n";

if (count($this->tags) == 1) {
    $tagobj = $this->tags[0];

    echo "\t" . '<tag>' . "\n";
    echo "\t\t" . '<raw>' . htmlspecialchars(stripslashes($tagobj->raw_tag)) . '</raw>' . "\n";
    echo "\t\t" . '<normalized>' . htmlspecialchars($tagobj->tag) . '</normalized>' . "\n";
    if ($tagobj->description != '') {
        $desc = htmlspecialchars(
            trim(\Hubzero\Utility\Sanitize::stripAll($tagobj->description))
        );
        echo "\t\t" . '<description><![CDATA[' . $desc . ']]></description>' . "\n";
    }
    echo "\t" . '</tag>' . "\n";
}

// Output the data.
$foundresults = false;
$dopaging = false;
$cats = $this->cats;
$html = "\t" . '<categories>' . "\n";
$k = 0;
foreach ($this->results as $category) {
    $amt = count($category);

    if ($amt > 0) {
        $foundresults = true;

        $name  = $cats[$k]['title'];
        $total = $cats[$k]['total'];
        $divid = $cats[$k]['category'];

        // Is this category the active category?
        if (!$this->active || $this->active == $cats[$k]['category']) {
            // It is - get some needed info
            $name  = $cats[$k]['title'];
            $total = $cats[$k]['total'];
            $divid = $cats[$k]['category'];

            if ($this->active == $cats[$k]['category']) {
                $dopaging = true;
            }
        } else {
            // It is not - does this category have sub-categories?
            if (isset($cats[$k]['_sub']) && is_array($cats[$k]['_sub'])) {
                // It does - loop through them and see if one is the active category
                foreach ($cats[$k]['_sub'] as $sub) {
                    if ($this->active == $sub['category']) {
                        // Found an active category
                        $name  = $sub['title'];
                        $total = $sub['total'];
                        $divid = $sub['category'];

                        $dopaging = true;
                        break;
                    }
                }
            }
        }

        $html .= "\t\t" . '<category>' . "\n";
        $html .= "\t\t\t" . '<type>' . $divid . '</type>' . "\n";
        $html .= "\t\t\t" . '<title>' . htmlspecialchars($name) . '</title>' . "\n";
        $html .= "\t\t\t" . '<total>' . $total . '</total>' . "\n";
        $html .= "\t\t\t" . '<items>' . "\n";
        foreach ($category as $row) {
            $row->href = str_replace('&amp;', '&', $row->href);
            $row->href = str_replace('&', '&amp;', $row->href);

            if (strstr($row->href, 'index.php')) {
                $row->href = Route::url($row->href);
            }
            if (substr($row->href, 0, 1) == '/') {
                $row->href = substr($row->href, 1, strlen($row->href));
            }

            $html .= "\t\t\t\t" . '<item>' . "\n";
            $cleanTitle = htmlspecialchars(
                \Hubzero\Utility\Sanitize::stripAll($row->title)
            );
            $html .= "\t\t\t\t\t" . '<title>' . $cleanTitle . '</title>' . "\n";
            if (isset($row->text) && $row->text != '') {
                $row->text = strip_tags($row->text);
                $cleanText = htmlspecialchars(
                    \Hubzero\Utility\Sanitize::stripAll($row->text)
                );
                $html .= "\t\t\t\t\t" . '<description><![CDATA['
                    . $cleanText . ']]></description>' . "\n";
            } elseif (isset($row->itext) && $row->itext != '') {
                $row->itext = strip_tags($row->itext);
                $cleanText = htmlspecialchars(
                    \Hubzero\Utility\Sanitize::stripAll($row->itext)
                );
                $html .= "\t\t\t\t\t" . '<description><![CDATA['
                    . $cleanText . ']]></description>' . "\n";
            } elseif (isset($row->ftext) && $row->ftext != '') {
                $row->ftext = strip_tags($row->ftext);
                $cleanText = htmlspecialchars(
                    \Hubzero\Utility\Sanitize::stripAll($row->ftext)
                );
                $html .= "\t\t\t\t\t" . '<description><![CDATA['
                    . $cleanText . ']]></description>' . "\n";
            }
            $html .= "\t\t\t\t\t" . '<link>' . Request::base() . $row->href . '</link>' . "\n";
            $html .= "\t\t\t\t" . '</item>' . "\n";
        }
        $html .= "\t\t\t" . '</items>' . "\n";
        $html .= "\t\t" . '</category>' . "\n";
    }
    $k++;
}
$html .= "\t" . '</categories>' . "\n";
echo $html;

// Terminate root element.
echo '</root>' . "\n";
