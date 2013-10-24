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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
?>
<div id="content-header-extra">
	<ul id="useroptions">
		<li>
			<a href="<?php echo JRoute::_('index.php?option=com_feedback&task=success_story'); ?>" class="icon-add btn add">
				<?php echo JText::_('MOD_QUOTES_ADD_YOUR_STORY'); ?>
			</a>
		</li>
	</ul>
</div>

<?php if (count($this->quotes) > 0) { // Did we get any results? ?>
	<?php
	$base = rtrim(JURI::getInstance()->base(true), '/');
	foreach ($this->quotes as $quote)
	{
		$quote->org = str_replace('<br>', '<br />', $quote->org);

		if (isset($this->filters['id']) && $this->filters['id'] != '')
		{
			?>
			<div class="breadcrumbs">
				<p>
					<a href="<?php echo $base; ?>/about/quotes" class="breadcrumbs"><?php echo JText::_('MOD_QUOTES_NOTABLE_QUOTES'); ?></a> 
					&rsaquo; 
					<strong><?php echo $this->escape(stripslashes($quote->fullname)); ?></strong>
				</p>
			</div>
			<?php
		}
		?>
		<blockquote cite="<?php echo $this->escape(stripslashes($quote->fullname)); ?>">
		<?php if (isset($this->filters['id']) && $this->filters['id'] != '') { ?>
			<p>
				<?php echo $this->escape(stripslashes($quote->quote)); ?>
			</p>
		<?php } else { ?>
			<p>
			<?php if ($quote->short_quote != $quote->quote) { ?>
				<?php echo $this->escape(rtrim(stripslashes($quote->short_quote), '.')); ?>
				 &#8230; 
				<a href="<?php echo $base; ?>/about/quotes/?quoteid=<?php echo $quote->id; ?>" title="<?php echo JText::sprintf('MOD_QUOTES_VIEW_QUOTE_BY', $this->escape(stripslashes($quote->fullname))); ?>">
					<?php echo JText::_('MOD_QUOTES_MORE'); ?>
				</a>
			<?php } else { ?>
				<?php echo $this->escape(stripslashes($quote->short_quote); ?>
			<?php } ?>
			</p>
		<?php } ?>
		</blockquote>
		<p class="cite">
			<cite><?php echo $this->escape(stripslashes($quote->fullname)); ?></cite>
			<br /><?php echo $this->escape(stripslashes($quote->org)); ?>
		</p>
	<?php
	}
	?>
<?php } else { ?>
	<p><?php echo JText::_('MOD_QUOTES_NO_QUOTES_FOUND'); ?></p>
<?php } ?>
