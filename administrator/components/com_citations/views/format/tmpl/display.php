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

//add title and save button to toolbar
JToolBarHelper::title(JText::_('Citation Format'), 'citation.png');
JToolBarHelper::save();

// include citations format class
// new citations format object
require_once JPATH_ROOT . DS . 'components' . DS . 'com_citations' . DS . 'helpers' . DS . 'format.php';
$cf = new CitationFormat();
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script type="text/javascript">
function submitbutton(pressbutton) 
{
	var form = $('adminForm');
	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}
	// do field validation
	submitform(pressbutton);
}

var $jQ = jQuery.noConflict();
$jQ(document).ready(function(e) {
	var formatSelector = $jQ('#format-selector'),
		formatBox = $jQ('#format-string');
	
	//when we change format box
	formatSelector.on('change', function(event) {
		var value  = $jQ(this).val(),
			format = $jQ(this).find(':selected').attr('data-format');
		formatBox.val(format);
	});
	
	//when we customize the format
	formatBox.on('keyup', function(event) {
		var customOption = formatSelector.find('option[value=custom]');
		customOption.attr('data-format', formatBox.val());
	});
});
</script>


<form action="index.php" method="post" name="adminForm">
	<div class="col width-60 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('Citation Format'); ?></span></legend>
			<table class="admintable">
				<tbody>
					<tr>
						<th width="20%">
							<?php echo JText::_('Format Style:'); ?>
						</th>
						<td>
							<select name="format[style]" id="format-selector">
								<option value="apa" <?php if ($this->currentFormat->style == 'apa') { echo 'selected'; } ?> data-format="<?php echo str_replace('"', '\"', $this->apaFormat); ?>">APA Format</option>
								<option value="ieee" <?php if ($this->currentFormat->style == 'ieee') { echo 'selected'; } ?> data-format="<?php echo str_replace('"', '\"', $this->ieeeFormat); ?>">IEEE Format</option>
								<option value="custom" <?php if ($this->currentFormat->style != 'apa' && $this->currentFormat->style != 'ieee') { echo 'selected'; } ?> data-format="<?php echo str_replace('"', '\"', $this->currentFormat->format); ?>">Custom Format</option>
							</select>
						</td>
					</tr>
					<tr>
						<th width="20%">
							<?php echo JText::_('Format String:'); ?>
						</th>
						<td>
							<textarea name="format[format]" rows="10" id="format-string"><?php echo $this->currentFormat->format; ?></textarea>
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	</div>
	<div class="col width-40 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('Placeholders'); ?></span></legend>
			<table class="admintable">
				<thead>
					<tr>
						<th>Placeholder</th>
						<th>Value</th>
					</tr>
				</thead>
				<tbody>
					<?php
						// get the keys
						$keys = $cf->getTemplateKeys();
						
						foreach ($keys as $k => $v) 
						{
							echo "<tr><td>{$v}</td><td>{$k}</td></tr>";
						}
					?>
				</tbody>
			</table>
		</fieldset>
	</div>
	<input type="hidden" name="format[id]" value="<?php echo $this->currentFormat->id; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />
	<?php echo JHTML::_('form.token'); ?>
</form>
