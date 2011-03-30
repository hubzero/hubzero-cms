<?php
/**
 * @package		HUBzero CMS
 * @author		Christopher Smoak <csmoak@purdue.edu>
 * @copyright	Copyright 2005-2010 by Purdue Research Foundation, West Lafayette, IN 47906
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
<div id="special-group-pane">
	<div id="special-group-container">
		<div class="three columns first">
			<h1>
				<a href="/" title="nanoHUB.org Home">nanohub.org</a>
			</h1>
			<p class="intro">
				You are currently viewing “<?php echo $this->group->get('description'); ?>” group powered by nanoHUB.org. 
				As you might notice this group looks different than normal groups, and is what we call a “special group”. 
				Nanotechnology related centers around the world need a way to showcase their research, and nanoHUB offers a stress free solution.
			</p>

			<h2>Interested in Having a “Special Group”?</h2>
			<p>
				If you are interested in having your own special group contact Michael McLennan at <a href="mailto:mmclennan@purdue.edu">mmclennan@purdue.edu</a>.
			</p>
		</div>
		<div class="three columns second">
			<h2>What is nanoHUB.org?</h2>
			<p>
				nanoHUB is a rich, web-based resource for research, education and collaboration in nanotechnology. 
				nanoHUB hosts over 1600 resources which will help you learn about nanotechnology. 
				Most importantly, the nanoHUB offers simulation tools which you can access from your web browser, so you can not only learn about but also simulate nanotechnology devices.
			</p>
			<div class="usage">
				<a href="/usage" class="usage_inner" title="nanoHUB.org Usage">&nbsp;</a>
			</div>
		</div>
		<div class="three columns third">
			<h2>Explore other nanoHUB.org Content!</h2>
			<ul class="nanohub-menu">
				<?php foreach($this->menu  as $item) { ?>
					<?php
						$link = $item['alias'];
						//$link = JRoute::_($item['link']); 
					?>
					<li><a href="<?php echo $link; ?>"><?php echo $item['name']; ?></a></li>
				<?php } ?>
			</ul>
		</div>
		<br class="clear" />
	</div>
</div><!-- / #special-group-pane -->