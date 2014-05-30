<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 * All rights reserved.
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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

if ($this->no_html) {
	$app = JFactory::getApplication();
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
		<link rel="stylesheet" href="/components/<?php echo $this->option; ?>'/assets/css/resources.css" type="text/css" />
		<?php } ?>
	</head>
	<body id="resource-license">
		<div id="wrap">
			<div id="main">
				<h3><?php echo $this->title; ?></h3>
<?php } else { ?>
	<header id="content-header">
		<h2><?php echo $this->title; ?></h2>
	<?php if ($this->tool) { ?>
		<?php if ($this->row->codeaccess=='@OPEN') { ?>
		<p><?php echo JText::sprintf('COM_RESOURCES_OPEN_SOURCE', $this->row->version); ?></p>
		<?php } else { ?>
		<p><?php echo JText::sprintf('COM_RESOURCES_CLOSED_SOURCE', $this->row->version); ?></p>
		<?php } ?>
	<?php } ?>
	</header><!-- / #content-header.full -->
	<section class="main section">
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
	</section><!-- / .main section -->
<?php } ?>