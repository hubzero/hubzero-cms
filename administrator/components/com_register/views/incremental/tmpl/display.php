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

JToolBarHelper::title(JText::_('Registration') . ': <small><small>[ ' . JText::_('Incremental Registration Options') . ' ]</small></small>', 'user.png');
JToolBarHelper::save();

$dbh =& JFactory::getDBO();
$dbh->setQuery('SELECT popover_text, award_per, test_group FROM #__incremental_registration_options ORDER BY added DESC LIMIT 1');
list($popoverText, $awardPer, $testGroup) = $dbh->loadRow();
$dbh->setQuery('SELECT hours FROM #__incremental_registration_popover_recurrence ORDER BY idx');
$recur = $dbh->loadResultArray();

require_once JPATH_BASE.'/components/com_register/tables/incremental.php';
$groups = new ModIncrementalRegistrationGroups;
$possibleCols = $groups->getPossibleColumns();
$groupDefs = $groups->getAllGroups();
?>
<script type="text/javascript">
	window.possibleCols = <?php echo json_encode($possibleCols); ?>;
	
	function submitbutton(pressbutton) {
		submitform(pressbutton);
	}
</script>
<form action="index.php" method="post" name="adminForm">
	<fieldset class="adminform">
		<legend>Incremental Registration Options</legend>
		<table class="admintable">
			<tbody>
				<tr>
					<td class="key" width="20%">Pop-over text</td>
					<td>
						<textarea name="popover" rows="10"><?php echo htmlentities($popoverText); ?></textarea>
					</td>
				</tr>
				<tr>
					<td class="key" width="20%">Award per field completed</td>
					<td>
						<input type="text" name="award-per" value="<?php echo str_replace('"', '&quot;', $awardPer); ?>" />
					</td>
				</tr>
				<tr>
					<td class="key" width="20%">Test group (name or id number)</td>
					<td>
						<input type="text" name="test-group" value="<?php echo str_replace('"', '&quot;', $testGroup); ?>" />
					</td>
				</tr>
				<tr>
					<td class="key">Field groups</td>
					<td>
						<p>Packages of profile information to prompt starting some time after registration</p>
						<ol style="margin:0 0 0 30px;">
							<?php 
								foreach ($groupDefs as $idx=>$group): 
									$unit = 'hour';
									if ($group['hours'] % (24*7) == 0):
										$unit = 'week';
										$group['hours'] /= (24*7);
									elseif ($group['hours'] % 24 == 0):
										$unit = 'day';
										$group['hours'] /= 24;
									endif;
							?>
							<li>
								<p>
									Beginning <input name="group-hours-<?php echo $idx; ?>" value="<?php echo $group['hours']; ?>" style="width: 40px;" />
									<select name="group-time-unit-<?php echo $idx; ?>" style="width: 100px;">
										<option value="hour" <?php if ($unit == 'hour') echo 'selected="selected" '; ?>>hours</option>
										<option value="day" <?php if ($unit == 'day') echo 'selected="selected" '; ?>>days</option>
										<option value="week" <?php if ($unit == 'week') echo 'selected="selected" '; ?>>weeks</option>
									</select> after registration, prompt for:
									<ul style="margin-top: 0; margin-left: 40px; margin-bottom: 0;">
										<?php foreach ($group['cols'] as $cidx=>$col): ?>
										<li>
											<select name="group-cols-<?php echo $idx; ?>[]" style="width: 300px;">
												<option value="">Select profile field...</option>
											<?php foreach ($possibleCols as $colName=>$colLabel): ?>
												<option value="<?php echo str_replace('"', '&quot;', $colName); ?>"<?php if ($colName == $col) echo ' selected="selected"'; ?>><?php echo htmlentities($colLabel); ?></option>	
											<?php endforeach; ?>
											</select>
											<button onclick="this.parentNode.parentNode.removeChild(this.parentNode); return false">Remove</button>
										</li>
										<?php endforeach; ?>
									</ul>
									<button style="margin-left: 40px;" onclick="(function(that) { 			
										var li = document.createElement('li');
										var ul = that.previousSibling;
										while (ul.tagName != 'UL') {
											ul = ul.previousSibling;
										}
										var sel = document.createElement('select');
										sel.setAttribute('style', 'width: 300px');
										var opt = document.createElement('option');
										opt.setAttribute('value', '');
										opt.appendChild(document.createTextNode('Select profile field...'));
										sel.appendChild(opt);
										for (var k in possibleCols) {
											if (possibleCols.hasOwnProperty(k)) {
												opt = document.createElement('option');
												opt.setAttribute('value', k);
												opt.appendChild(document.createTextNode(possibleCols[k]));
												sel.appendChild(opt);
											}
										}
										sel.setAttribute('name', 'group-cols-<?php echo $idx; ?>');
										li.appendChild(sel);

										var rm = document.createElement('button');
										rm.appendChild(document.createTextNode('Remove'));
										rm.onclick = function() {
											ul.removeChild(li);
											return false;
										};
										li.appendChild(rm);
										ul.appendChild(li);
									})(this); return false;">Add field</button>
								</p>
							</li>
							<?php endforeach; ?>
						</ol>
						<p>
							<button onclick="return false;">Add group</button>
						</p>
					</td>
				</tr>
				<tr>
					<td class="key">Recurrence</td>
					<td>
						<p>Time to wait before asking again after subsequent clicks of the "ask me later" button</p>
						<ol style="margin:0 0 20px 30px;">
							<?php 
								foreach ($recur as $idx=>$r): 
									$unit = 'hour';
									if ($r % (24*7) == 0):
										$unit = 'week';
										$r /= (24*7);
									elseif ($r % 24 == 0):
										$unit = 'day';
										$r /= 24;
									endif;
							?>
							<li>
								<input name="recur-<?php echo $idx; ?>" value="<?php echo $r; ?>" style="width: 40px;" />
								<select name="recur-type-<?php echo $idx; ?>" style="width: 100px;">
									<option value="hour" <?php if ($unit == 'hour') echo 'selected="selected" '; ?>>hours</option>
									<option value="day" <?php if ($unit == 'day') echo 'selected="selected" '; ?>>days</option>
									<option value="week" <?php if ($unit == 'week') echo 'selected="selected" '; ?>>weeks</option>
								</select>
								<button onclick="this.parentNode.parentNode.removeChild(this.parentNode); return false">Remove</button>
							</li>
							<?php 
								endforeach; 
							?>
						</ol>
						<button onclick="(function(that) { 
							var li = document.createElement('li');
							var ol = that.previousSibling;
							while (ol.tagName != 'OL') {
								ol = ol.previousSibling;
							}
							var len = ol.getElementsByTagName('li').length;
							var inp = document.createElement('input');
							inp.setAttribute('style', 'width: 40px;');
							inp.setAttribute('name', 'recur-' + len);
							li.appendChild(inp);
							var sel = document.createElement('select');
							sel.setAttribute('style', 'width: 100px;');
							sel.setAttribute('name', 'recur-type-' + len);
							var units = ['hour', 'day', 'week'];
							for (var idx = 0, len = units.length; idx < len; ++idx) {
								var op = document.createElement('option');
								op.setAttribute('value', units[idx]);
								op.appendChild(document.createTextNode(units[idx] + 's'));
								sel.appendChild(op);
							}
							li.appendChild(sel);
							var rm = document.createElement('button');
							rm.appendChild(document.createTextNode('Remove'));
							rm.onclick = function() {
								ol.removeChild(li);
								return false;
							};
							li.appendChild(rm);
							ol.appendChild(li);
						})(this); return false;">Add recurrence</button>
						<p style="margin-top: 20px;">After reaching the end of this list: <br /><input type="radio" name="repeat-type" checked="checked" /> repeat prompting indefinitely using the last delay listed between attempts<br /><input type="radio" name="repeat-type" /> stop prompting</p>
					</td>
				</tr>
			</tbody>
		</table>
	</fieldset>
	
	<input type="hidden" name="option" value="com_register" />
	<input type="hidden" name="controller" value="incremental" />
	<input type="hidden" name="task" value="save" />
	<?php echo JHTML::_('form.token'); ?>
</form>
