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

$this->css();
?>
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
	<?php if ($this->row->license) { ?>
		<pre><?php echo $this->row->license; ?></pre>
	<?php } else { ?>
		<p class="warning"><?php echo JText::_('COM_RESOURCES_NO_LICENSE_TEXT'); ?></p>
	<?php } ?>
</section><!-- / .main section -->