<?php
/**
 * @package		HUBzero CMS
 * @author		Christopher Smoak <csmoak@purdue.edu>
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

// No direct access
defined('_JEXEC') or die( 'Restricted access' ); 
?>
<h3><?php echo $moduleTitle; ?></h3>  
<?php if (isset($tweets['error']) && $tweets['error'] != '') {  ?>
	<p class="error">
		<?php echo $tweets['error']; ?>
	</p>
<?php } else { ?>
	<?php if ($displayLink=='yes') { ?>
		<a rel="external" href="http://twitter.com/<?php echo $twitterID; ?>"><?php echo JText::_('MOD_TWITTERFEED_FOLLOW_US'); ?></a>
	<?php } ?>
	<?php if ($displayIcon=='yes') { ?>
		<img class="twitterbird" src="/modules/mod_twitterfeed/twitter.png" alt="<?php echo JText::_('MOD_TWITTERFEED_ICON'); ?>" />
		<br clear="both" />
	<?php } ?>
	<ul class="twitterfeed">
		<?php array_shift($tweets); ?>
		<?php foreach ($tweets as $twit) { ?>
			<li>
				<span class="title"><?php echo $twit['tweet']; ?></span>
				<span class="date"><?php echo date("h:i a M d, Y", strtotime($twit['pubDate'])); ?></span>
				<a rel="external" href="<?php echo $twit['link']; ?>" class="viewstatus"><?php echo JText::_('MOD_TWITTERFEED_VIEW_STATUS'); ?></a>
			</li>
		<?php } ?>
	</ul>
<?php } ?>