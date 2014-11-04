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

$this->css();
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<div id="content-header-extra">
		<p>
			<a class="main-page btn" href="<?php echo JRoute::_('index.php?option=' . $this->option); ?>">
				<?php echo JText::_('COM_FEEDBACK_MAIN'); ?>
			</a>
		</p>
	</div><!-- / #content-header-extra -->
</header><!-- / #content-header -->

<section class="main section">
	<p class="passed"><?php echo JText::_('COM_FEEDBACK_STORY_THANKS'); ?></p>

	<div class="quote">
		<?php if (count($this->addedPictures)) { ?>
			<?php foreach ($this->addedPictures as $img) { ?>
				<img src="<?php echo $this->path . '/' . $img; ?>" alt="" />
			<?php } ?>
		<?php } ?>

		<blockquote cite="<?php echo $this->escape($this->row->fullname); ?>">
			<?php echo $this->escape(stripslashes($this->row->quote)); ?>
		</div>
		<p class="cite">
			<?php
			$profile = \Hubzero\User\Profile::getInstance($this->row->user_id);
			if ($profile)
			{
				echo '<img src="' . $profile->getPicture() . '" alt="' . $this->escape($this->row->fullname) . '" width="30" height="30" />';
			}
			?>
			<cite><?php echo $this->escape($this->row->fullname); ?></cite><br />
			<?php echo $this->escape($this->row->org); ?>
		</p>
	</div>
</section><!-- / .main section -->
