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
?>
<div id="error-wrap">
	<div id="error-box" class="code-403">
		<h2><?php echo JText::_('MW_ACCESS_DENIED'); ?></h2>
<?php if ($this->getError()) { ?>
		<p class="error-reasons"><?php echo $this->getError(); ?></p>
<?php } ?>

		<p>The majority of tools are Open Source and freely available to the public. However, this particular tool has restricted access.</p>
		<h3>How do I fix this?</h3>
		<ul>
			<li>If you feel that you should be able to access this tool, please <a href="/feedback/report_problems/">contact us</a>, and we will check the permissions on your account.</li>
			<li>You might also try <a href="/tools/">browsing through other tools</a> on this site, to see if there is another freely available tool that would work just as well for you.</li>
		</ul>
	</div><!-- / #error-box -->
</div><!-- / #error-wrap -->