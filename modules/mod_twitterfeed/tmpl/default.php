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