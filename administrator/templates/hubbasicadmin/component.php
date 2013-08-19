<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.filesystem.file');

$doc = JFactory::getDocument();

$doc->addStyleSheet('templates/'.$this->template.'/css/component.css');
if ($this->params->get('theme') && $this->params->get('theme') != 'gray') {
	$doc->addStyleSheet('templates/' . $this->template . '/css/themes/' . $this->params->get('theme') . '.css');
}

// Load language direction CSS
if ($this->direction == 'rtl') {
	$doc->addStyleSheet('templates/'.$this->template.'/css/common/rtl.css');
}

if (version_compare(JVERSION, '1.6', 'ge'))
{
	$doc->addScript('/media/system/js/mootools-core-uncompressed.js');
	$doc->addScript('/media/system/js/core-uncompressed.js');
	$doc->addScript('/media/system/js/mootools-more-uncompressed.js');
	$doc->addScript('templates/' . $this->template . '/js/component.js');
}

ximport('Hubzero_Browser');
$browser = new Hubzero_Browser();
$b = $browser->getBrowser();
$v = $browser->getBrowserMajorVersion();

$juser =& JFactory::getUser();
?>
<!DOCTYPE html>
<!--[if lt IE 7 ]> <html dir="<?php echo  $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="ie6"> <![endif]-->
<!--[if IE 7 ]>    <html dir="<?php echo  $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="ie7"> <![endif]-->
<!--[if IE 8 ]>    <html dir="<?php echo  $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="ie8"> <![endif]-->
<!--[if IE 9 ]>    <html dir="<?php echo  $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html dir="<?php echo $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="<?php echo $b . ' ' . $b . $v; ?>"> <!--<![endif]-->
	<head>
		<jdoc:include type="head" />

	</head>
	<body id="component-body" class="contentpane">
		<jdoc:include type="message" />
		<jdoc:include type="component" />
	</body>
</html>
