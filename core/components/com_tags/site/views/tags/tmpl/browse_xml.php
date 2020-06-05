<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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
