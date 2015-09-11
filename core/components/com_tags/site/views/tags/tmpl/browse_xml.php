<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

Document::setType('xml');

// Output XML header.
echo '<?xml version="1.0" encoding="UTF-8" ?>' . "\n";

// Output root element.
echo '<root>' . "\n";

// Output the data.
echo "\t" . '<tags>' . "\n";
if ($this->rows)
{
	foreach ($this->rows as $datum)
	{
		echo "\t\t" . '<tag>' . "\n";
		echo "\t\t\t" . '<raw>' . $this->escape(stripslashes($datum->get('raw_tag'))) . '</raw>' . "\n";
		echo "\t\t\t" . '<normalized>' . $this->escape($datum->get('tag')) . '</normalized>' . "\n";
		echo "\t\t\t" . '<total>' . $this->escape($datum->get('total')) . '</total>' . "\n";
		echo "\t\t" . '</tag>' . "\n";
	}
}
echo "\t" . '</tags>' . "\n";

// Terminate root element.
echo '</root>' . "\n";
