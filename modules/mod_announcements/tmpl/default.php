<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
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
defined('_JEXEC') or die('Restricted access');

$morelink = count($this->content) > 0 ? $this->content[0]->catpath : 'announcements';
$morelink = $this->params->get('show_viewall', '') ? $morelink : '';
$subscribelink = $this->params->get('show_subscribe', '') &&  $this->params->get('subscribe_path', '') ?  $this->params->get('subscribe_path', '') : '';

?>
<?php if ($morelink or $subscribelink) { ?>
<p class="sublinks">
	<?php if ($morelink) { ?><a href="<?php echo $morelink;  ?>"><?php echo JText::_('MOD_ANNOUNCEMENTS_VIEW_ALL'); ?></a><?php } ?>
	<?php if ($morelink && $subscribelink) { ?> <span>|</span> <?php } ?>
	<?php if ($subscribelink) { ?><a href="<?php echo $subscribelink;  ?>" class="add"><?php echo $this->params->get('subscribe_label', JText::_('MOD_ANNOUNCEMENTS_SUBSCRIBE')); ?></a><?php } ?>
</p>
<?php } ?>

<div id="<?php echo $this->container; ?>">
<?php if ($this->params->get('show_search', '')) { ?>
	<form action="<?php echo JRoute::_('index.php?option=com_search'); ?>" method="get" class="search">
		<fieldset>
			<p>
				<input type="text" name="terms" value="" />
				<input type="hidden" name="section" value="content:announcements" />
				<input type="submit" value="<?php echo JText::_('MOD_ANNOUNCEMENTS_SEARCH'); ?>" />
			</p>
		</fieldset>
	</form>
<?php } ?>

<?php if (count($this->content) > 0) { ?>
	<ul>
		<?php
		foreach ($this->content as $item)
		{
			$url = DS . $item->catpath . DS . $item->alias;

			// get associated image
			preg_match('/<img\s+.*?src="(.*?)"/is', $item->introtext , $match);
			$img = count($match) > 1
			     ? trim(stripslashes($match[1]))
			     : $this->params->get('default_image', 'modules/mod_announcements/default.gif');
		?>
		<li>
		<?php if ($this->params->get('show_image', '')) { ?>
			<img src="<?php echo $img; ?>" alt="<?php echo $this->escape(stripslashes($item->title)); ?>" />
		<?php } ?>
			<span class="a-content">
				<span class="a-title">
					<a href="<?php echo $url; ?>">
						<?php echo $this->escape(stripslashes($item->title)); ?>
					</a>
				</span>
			<?php if ($this->params->get('show_date', '')) { ?>
				<span class="a-date">
					<?php echo JHTML::_('date', $item->publish_up, JText::_('DATE_FORMAT_HZ1')); ?>
				</span>
			<?php } ?>
			<?php if ($this->params->get('show_desc', '')) { ?>
				<span class="a-desc">
					<?php
					// get cleaned article body text
					$desc = preg_replace( '/<a\s+.*?href="([^"]+)"[^>]*>.*?<\/a>/is', '\2', $item->introtext);
					$desc = preg_replace( '/<span([^"]+)"[^>]*>.*?<\/span>/is', '\2', $desc);
					$desc = \Hubzero\Utility\Sanitize::clean($desc);

					echo \Hubzero\Utility\String::truncate($desc, $this->params->get('word_count', 100));
					?>
				</span>
			<?php } ?>
			<?php if ($this->params->get('show_morelink', '')) { ?>
				<span class="a-link">
					<a href="<?php echo $url; ?>">
						<?php echo JText::_('MOD_ANNOUNCEMENTS_READ_MORE'); ?>
					</a>
				</span>
			<?php } ?>
			</span>
		</li>
	<?php } ?>
	</ul>
<?php } else { ?>
	<p><?php echo JText::_('MOD_ANNOUNCEMENTS_NO_RESULTS'); ?></p>
<?php } ?>
</div><!-- / #pane-sliders -->