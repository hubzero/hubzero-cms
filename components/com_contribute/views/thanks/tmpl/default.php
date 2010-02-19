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
?>
<div id="content-header" class="full">
	<h2><?php echo $this->title; ?></h2>
</div><!-- / #content-header -->

<div class="main section">
<?php if ($this->getError()) { ?>
	<p class="warning"><?php echo $this->getError(); ?></p>
<?php } ?>
<?php if ($this->config->get('autoapprove') == 1) { ?>
		<p class="passed">Thank you for your contribution! You may view your contribution <a href="<?php echo JRoute::_('index.php?option=com_resources&id='.$this->resource->id); ?>">here</a>.</p>
<?php } else { ?>
		<p class="passed">Thank you for your contribution! All contributions must undergo a review process. If accepted, you will be notified when it is available from our <a href="<?php echo JRoute::_('index.php?option=com_resources'); ?>">resources</a>.</p>
<?php } ?>
	<p>Contribution submitted:</p>
	<p>
		<strong>Title:</strong> <?php echo stripslashes($this->resource->title); ?><br />
		<strong>ID#:</strong> <?php echo $this->resource->id; ?><br />
	</p>
	<p class="adminoptions">
		<a href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=start'); ?>">Start a new submission</a> or 
		<a href="<?php echo JRoute::_('index.php?option='.$this->option); ?>">Return to start</a>
	</p>
</div><!-- / .main section -->