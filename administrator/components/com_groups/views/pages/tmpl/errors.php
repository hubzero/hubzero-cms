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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$this->css();

JToolBarHelper::title($this->group->get('description').': '.$this->page->get('title').' - ' . JText::_('COM_GROUPS_PAGES_ERRORS'), 'groups.png');
JToolBarHelper::custom('errorscheckagain', 'check', 'check', 'COM_GROUPS_PAGES_CHECK_AGAIN', false);
JToolBarHelper::cancel();

// page version content
$content = $this->page->version()->get('content');
?>

<form action="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;gid=<?php echo $this->group->cn; ?>" method="post" name="adminForm" id="item-form">

	<p class="error">
		<?php echo JText::sprintf('COM_GROUPS_PAGES_ERROR_LIST' , $this->page->get('title'), $this->error); ?>
	</p>

	<h3><?php echo JText::_('COM_GROUPS_PAGES_VIEW_RAW_CODE'); ?></h3>
	<div class="code">
		<?php
			$lines = explode("\n", $content);
			$lineCode = '';
			for ($i=1; $i <= count($lines); $i++)
			{
				$lineCode .= "&nbsp;".$i."&nbsp;<br>";
			}
		?>
		<table>
			<tr>
				<td class="lines"><?php echo $lineCode; ?></td>
				<td class="code">
					<?php highlight_string($content); ?>
				</td>
			</tr>
		</table>
	</div>

	<h3><?php echo JText::_('COM_GROUPS_PAGES_UPDATE_CONTENT'); ?></h3>
	<textarea name="page[content]" rows="40"><?php echo $content; ?></textarea>

	<input type="hidden" name="page[id]" value="<?php echo $this->page->get('id'); ?>">
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="gid" value="<?php echo $this->group->get('cn'); ?>" />
	<input type="hidden" name="task" value="save" />
	<?php echo JHTML::_('form.token'); ?>
</form>