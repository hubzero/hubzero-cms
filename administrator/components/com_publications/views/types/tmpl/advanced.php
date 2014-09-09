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

JToolBarHelper::title(JText::_('COM_PUBLICATIONS_PUBLICATION') . ' ' . JText::_('COM_PUBLICATIONS_MASTER_TYPE') . ' - ' . $this->row->type . ': [ ' . JText::_('COM_PUBLICATIONS_MTYPE_ADVANCED') . ' ]', 'addedit.png');
JToolBarHelper::save('saveadvanced');
JToolBarHelper::cancel();

$params = new JRegistry($this->row->params);
$manifest  = $this->curation->_manifest;
$curParams = $manifest->params;
$blocks	   = $manifest->blocks;

$blockSelection = array('active' => array());

?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	submitform( pressbutton );
	return;
}
</script>
<p class="backto"><a href="index.php?option=<?php echo $this->option; ?>&amp;controller=types&amp;task=edit&amp;id[]=<?php echo $this->row->id; ?>"><?php echo JText::_('COM_PUBLICATIONS_MTYPE_BACK') . ' ' . $this->row->type . ' ' . JText::_('COM_PUBLICATIONS_MASTER_TYPE'); ?></a></p>

<form action="index.php" method="post" id="item-form" name="adminForm">
		<fieldset class="adminform">
			<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
			<input type="hidden" name="task" value="saveadvanced" />
			<input type="hidden" name="neworder" id="neworder" value="" />
			<legend><span><?php echo JText::_('COM_PUBLICATIONS_MTYPE_ADVANCED_CURATION_EDITING'); ?></span></legend>
			<p class="hint"><?php echo JText::_('COM_PUBLICATIONS_MTYPE_ADVANCED_CURATION_EDITING_HINT'); ?></p>
			<div class="input-wrap">
				<textarea cols="50" rows="10" name="curation"><?php echo json_encode($this->curation->_manifest); ?></textarea>
			</div>
		</fieldset>
	<?php echo JHTML::_('form.token'); ?>
</form>