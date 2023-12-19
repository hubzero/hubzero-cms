<?php
/**
 * @package   hubzero-cms
 * @copyright Copyright (c) 2005-2020 The Regents of the University of California.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$breadcrumbs = $this->breadcrumbs;
$pageTitle = $this->pageTitle;

$cumulativePath = '';

foreach ($breadcrumbs as $text => $url)
{
	$cumulativePath .= $url;
	Pathway::append($text, $cumulativePath);
}

Document::setTitle($pageTitle);
