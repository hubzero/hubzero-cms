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

if ($modxpoll->poll->id && $modxpoll->poll->title) {
	$options = $modxpoll->options;
?>
<form id="<?php echo ($modxpoll->formid) ? $modxpoll->formid : 'xpoll'.rand(); ?>" method="post" action="<?php echo JRoute::_('index.php?option=com_xpoll'); ?>">
	<fieldset>
		<h4><?php echo $modxpoll->poll->title; ?></h4>
		<ul class="poll">
<?php
$tabcnt = 0;
for ($i=0, $n=count( $options ); $i < $n; $i++) 
{ 
?>
		 <li>
			<input type="radio" name="voteid" id="voteid<?php echo $options[$i]->id;?>" value="<?php echo $options[$i]->id;?>" alt="<?php echo $options[$i]->id;?>" />
			<label for="voteid<?php echo $options[$i]->id;?>"><?php echo stripslashes($options[$i]->text); ?></label>
		 </li>
<?php
	if ($tabcnt == 1) {
		$tabcnt = 0;
	} else {
		$tabcnt++;
	}
}
?>
		</ul>
		<p><input type="submit" name="task_button" value="<?php echo JText::_('BUTTON_VOTE'); ?>" />&nbsp;&nbsp;
		<a href="<?php echo JRoute::_("index.php?option=com_xpoll&amp;task=view&amp;id=".$modxpoll->poll->id); ?>"><?php echo JText::_('BUTTON_RESULTS'); ?></a></p>

		<input type="hidden" name="id" value="<?php echo $modxpoll->poll->id;?>" />
		<input type="hidden" name="task" value="vote" />
	</fieldset>
</form>
<?php } ?>