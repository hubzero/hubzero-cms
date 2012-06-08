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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

if ($this->poll->id && $this->poll->title) {
	$options = $this->options;
?>
<form id="<?php echo ($this->formid) ? $this->formid : 'xpoll'.rand(); ?>" method="post" action="<?php echo JRoute::_('index.php?option=com_xpoll'); ?>">
	<fieldset>
		<h4><?php echo $this->poll->title; ?></h4>
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
		<a href="<?php echo JRoute::_("index.php?option=com_xpoll&amp;task=view&amp;id=" . $this->poll->id); ?>"><?php echo JText::_('BUTTON_RESULTS'); ?></a></p>

		<input type="hidden" name="id" value="<?php echo $this->poll->id;?>" />
		<input type="hidden" name="task" value="vote" />
	</fieldset>
</form>
<?php } ?>