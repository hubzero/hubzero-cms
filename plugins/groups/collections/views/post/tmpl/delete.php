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
defined('_JEXEC') or die('Restricted access');

$base = 'index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=' . $this->name;

$identifier = $this->post->item()->get('title');
if (!$identifier)
{
	$identifier = $this->post->item()->description('clean');
	if (!$identifier)
	{
		$identifier = '#' . $this->post->item()->get('id');
	}
}
?>
<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>
	<form action="<?php echo JRoute::_($base . '&scope=post/' . $this->post->get('id') . '/delete'); ?>" method="post" id="hubForm" class="full">
		<fieldset>
			<legend><?php echo JText::_('PLG_GROUPS_COLLECTIONS_DELETE_HEADER'); ?></legend>

			<p class="warning"><?php echo JText::sprintf('PLG_GROUPS_COLLECTIONS_DELETE_WARNING', $this->escape(stripslashes($identifier))); ?></p>

			<label>
				<input type="checkbox" class="option" name="confirmdel" value="1" /> 
				<?php echo JText::_('PLG_GROUPS_COLLECTIONS_DELETE_CONFIRM'); ?>
			</label>
		</fieldset>
		<div class="clear"></div>

		<input type="hidden" name="cn" value="<?php echo $this->group->get('cn'); ?>" />
		<input type="hidden" name="process" value="1" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="active" value="<?php echo $this->name; ?>" />
		<input type="hidden" name="action" value="delete" />
		<input type="hidden" name="post" value="<?php echo $this->post->get('id'); ?>" />
		<input type="hidden" name="no_html" value="<?php echo $this->no_html; ?>" />

		<?php echo JHTML::_('form.token'); ?>

		<p class="submit">
			<input type="submit" value="<?php echo JText::_('PLG_GROUPS_COLLECTIONS_DELETE'); ?>" />
<?php if (!$this->no_html) { ?>
			<a href="<?php echo JRoute::_($base . '&scope=' . $this->collection->get('alias')); ?>"><?php echo JText::_('Cancel'); ?></a>
<?php } ?>
		</p>
	</form>
