<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

if ($this->no_html) { 
	$app =& JFactory::getApplication();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title><?php echo $this->title; ?></title>
		<link rel="stylesheet" type="text/css" href="/templates/<?php echo $app->getTemplate(); ?>/css/main.css" />
<?php if (is_file(JPATH_ROOT.DS.'templates'.DS. $app->getTemplate() .DS.'html'.DS.$this->option.DS.'members.css')) { ?>
		<link rel="stylesheet" href="/templates/<?php echo $app->getTemplate(); ?>/html/<?php echo $this->option; ?>/resources.css" type="text/css" />
<?php } else { ?>
		<link rel="stylesheet" href="/components/<?php echo $this->option; ?>'/resources.css" type="text/css" />
<?php } ?>
	</head>
	<body id="resource-license">
		<div id="wrap">
			<div id="main">
				<h3><?php echo $this->title; ?></h3>
<?php } else { ?>
	<div id="content-header" class="full">
		<h2><?php echo $this->title; ?></h2>
<?php if ($this->row->codeaccess=='@OPEN') { ?>
		<p><?php echo JText::sprintf('COM_RESOURCES_OPEN_SOURCE', $this->row->version); ?></p>
<?php } else { ?>
		<p><?php echo JText::sprintf('COM_RESOURCES_CLOSED_SOURCE', $this->row->version); ?></p>
<?php } ?>
	</div><!-- / #content-header.full -->
	<div class="main section">
<?php } ?>
<?php if ($this->row->license) { ?>
		<pre><?php echo $this->row->license; ?></pre>
<?php } else { ?>
		<p class="warning"><?php echo JText::_('COM_RESOURCES_NO_LICENSE_TEXT'); ?></p>
<?php } ?>
<?php if ($this->no_html) { ?>
			</div><!-- / #main -->
		</div><!-- / #wrap -->
	</body>
</html>
<?php } else { ?>
	</div><!-- / .main section -->
<?php } ?>