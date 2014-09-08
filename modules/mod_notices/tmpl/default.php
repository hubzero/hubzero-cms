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
defined('_JEXEC') or die('Restricted access');

if ($this->publish)
{
	$this->css();
?>
<div id="<?php echo $this->moduleid; ?>" class="modnotices <?php echo $this->alertlevel; ?>">
	<p>
		<?php echo stripslashes($this->message); ?>
		<?php
		$page = JRequest::getVar('REQUEST_URI', '', 'server');
		if ($page && $this->params->get('allowClose', 1))
		{
			$this->js();

			$page .= (strstr($page, '?')) ? '&' : '?';
			$page .= $this->moduleid . '=close';
			?>
			<a class="close" href="<?php echo $page; ?>" data-duration="<?php echo $this->days_left; ?>" title="<?php echo JText::_('MOD_NOTICES_CLOSE_TITLE'); ?>">
				<span><?php echo JText::_('MOD_NOTICES_CLOSE'); ?></span>
			</a>
			<?php
		}
		?>
	</p>
</div>
<?php } ?>