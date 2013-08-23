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

ximport('Hubzero_View_Helper_Html');

$morelink = count($this->content) > 0 ? $this->content[0]->secname : 'announcements';
$morelink = $this->params->get('show_viewall', '') ? $morelink : '';
$subscribelink = $this->params->get('show_subscribe', '') &&  $this->params->get('subscribe_path', '') ?  $this->params->get('subscribe_path', '') : '';
$html = '';
?>
<?php if ($morelink or $subscribelink) { ?>
<p class="sublinks">
	<?php if ($morelink) { ?><a href="<?php echo $morelink;  ?>"><?php echo JText::_('View all'); ?> &rsaquo;</a><?php } ?>
	<?php if ($morelink && $subscribelink) { ?> <span>|</span> <?php } ?>
	<?php if ($subscribelink) { ?><a href="<?php echo $subscribelink;  ?>" class="add"><?php echo $this->params->get('subscribe_label', 'Subscribe to feed'); ?> &rsaquo;</a><?php } ?>
</p>
<?php } ?>

<div id="<?php echo $this->container; ?>">
<?php if ($this->params->get('show_search', '')) { ?>
	<form action="/search/" method="get" class="search">
		<fieldset>
			<p>
				<input type="text" name="terms" value="" />
				<input type="hidden" name="section" value="content:announcements" />
				<input type="submit" value="Search" />
			</p>
		</fieldset>
	</form>
<?php } ?>
<?php if (count($this->content) > 0) { ?>
    <ul>
<?php 
		$dateFormat = '%d %b %Y';
		$tz = 0;
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$dateFormat = 'd M Y';
			$tz = true;
		}
		foreach ($this->content as $item) 
		{
			$url  = $item->secname;
			$url .= $item->secname == $item->catname ? '' : DS . $item->catname;
			$url .= DS . $item->alias;
			
			if (version_compare(JVERSION, '1.6', 'ge'))
			{
				$url = $item->catpath . DS . $item->alias;
			}
			
			// get associated image
			preg_match('/<img\s+.*?src="(.*?)"/is', $item->introtext , $match);
			$img = count($match) > 1 ? trim(stripslashes($match[1])) : $this->params->get('default_image', 'modules/mod_announcements/default.gif');

			// get cleaned article body text
			$desc = preg_replace( '/<a\s+.*?href="([^"]+)"[^>]*>.*?<\/a>/is', '\2', $item->introtext );	
			$desc = preg_replace( '/<span([^"]+)"[^>]*>.*?<\/span>/is', '\2', $item->introtext );	
			$desc = Hubzero_View_Helper_Html::purifyText($desc);
?>
		<li>
<?php 		if ($this->params->get('show_image', '')) { ?>
			<img src="<?php echo $img; ?>" alt="<?php echo stripslashes($item->title); ?>" />
<?php 		} ?>
			<span class="a-content">
				<span class="a-title">
					<a href="<?php echo $url; ?>">
						<?php echo stripslashes($item->title); ?>
					</a>
				</span>
<?php 		if ($this->params->get('show_date', '')) { ?>
				<span class="a-date">
					<?php echo JHTML::_('date', $item->publish_up, $dateFormat, $tz); ?>
				</span>
<?php 		} ?>
<?php 		if ($this->params->get('show_desc', '') && $desc != '') { ?>
				<span class="a-desc">
					<?php echo Hubzero_View_Helper_Html::shortenText(($desc), $this->params->get('word_count', 100), 0); ?>
				</span>
<?php 		} ?>
<?php 		if ($this->params->get('show_morelink', '')) { ?>
				<span class="a-link">
					<a href="<?php echo $url; ?>">
						<?php echo JText::_('Read more...'); ?>
					</a>
				</span>
<?php 		} ?>
			</span>
		</li>
<?php 	} ?>
	</ul>
<?php } else { ?>
	<p><?php echo JText::_('No articles found.'); ?></p>
<?php } ?>
</div><!-- / #pane-sliders -->