<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
