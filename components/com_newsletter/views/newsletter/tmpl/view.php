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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>

<div id="content-header">
	<h2><?php echo $this->title; ?></h2>
</div>
	
<div id="content-header-extra">
	<ul>
		<?php if (isset($this->id) && $this->id != 0) : ?>
			<li>
				<a href="<?php echo JRoute::_('index.php?option=com_newsletter&id=' . $this->id . '&task=output'); ?>" class="btn icon-file">Save as PDF</a>
			</li>
		<?php endif; ?>
		<li>
			<a href="<?php echo JRoute::_('index.php?option=com_newsletter&task=subscribe'); ?>" class="btn icon-feed">Subscribe To Mailing Lists</a>
		</li>
	</ul>
</div>
	
<div class="main section">
	<?php
		if ($this->getError())
		{
			echo '<p class="error">' . $this->getError() . '</p>';
		}
	?>
	<div class="aside">
		<div class="container">
			<h3>Past Newsletters &amp; Updates</h3>
			<ul>
				<?php foreach($this->newsletters as $newsletter) : ?>
					<?php if($newsletter->published) : ?>
						<li>
							<a class="<?php if ($this->id == $newsletter->id) { echo "active"; } ?>" href="<?php echo JRoute::_('index.php?option=com_newsletter&id='.$newsletter->id); ?>">
								<?php echo $newsletter->name; ?>
							</a>
						</li>
					<?php endif; ?>
				<?php endforeach; ?>
			</ul>
		</div>
		<div class="container">
			<h3>Newsletter Help</h3>
			<ul>
				<li>
					<a class="popup" href="<?php echo JRoute::_('index.php?option=com_help&component=newsletter&page=index'); ?>">Newsletter Help</a>
				</li>
			</ul>
		</div>
	</div><!-- /.aside -->
	
	<div class="subject newsletter">
		<?php if ($this->newsletter != '') : ?>
			<div class="container">
				<iframe id="newsletter-iframe" width="100%" height="0" src="index.php?option=com_newsletter&amp;id=<?php echo $this->id; ?>&amp;no_html=1"></iframe>
			</div>
		<?php else : ?>
			<p class="info">
				<?php echo JText::_('Currently there are no HUB newsletters.'); ?>
			</p>
		<?php endif; ?>
	</div><!-- /.subject -->
</div><!-- /.main .section -->