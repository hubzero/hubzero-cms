<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('COM_MEMBERS_REGISTRATION') . ': ' . Lang::txt('Incremental Options'), 'user.png');
Toolbar::save();

$dbh = App::get('db');
$dbh->setQuery('SELECT popover_text, award_per, test_group FROM `#__incremental_registration_options` ORDER BY added DESC LIMIT 1');
list($popoverText, $awardPer, $testGroup) = $dbh->loadRow();
$dbh->setQuery('SELECT hours FROM `#__incremental_registration_popover_recurrence` ORDER BY idx');
$recur = $dbh->loadColumn();

$groups = new Components\Members\Models\Incremental\Groups;
$possibleCols = $groups->getPossibleColumns();
$groupDefs = $groups->getAllGroups();
?>
<script type="text/javascript">
	var possibleCols = <?php echo json_encode($possibleCols); ?>;

	function submitbutton(pressbutton) {
		submitform(pressbutton);
	}
	var addField = function(that, idx) {
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
		sel.setAttribute('name', 'group-cols-' + idx + '[]');
		li.appendChild(sel);
		var rm = document.createElement('button');
		rm.appendChild(document.createTextNode('Remove field'));
		rm.onclick = function() {
			ul.removeChild(li);
			return false;
		};
		li.appendChild(rm);
		ul.appendChild(li);
	};

	var renumberGroups = function() {
		var lis = document.getElementById('reg-groups').getElementsByTagName('li');
		var idx = 0;
		for (var lidx = 0; lidx < lis.length; ++lidx) {
			if (lis[lidx].getAttribute('class') == 'reg-group') {
				var li = lis[lidx];
				var inps = li.getElementsByTagName('input');
				for (var sidx = 0; sidx < inps.length; ++sidx) {
					inps[sidx].setAttribute('name', inps[sidx].getAttribute('name').replace(/\d+$/, idx));
				}
				var sels = li.getElementsByTagName('select');
				for (var sidx = 0; sidx < sels.length; ++sidx) {
					sels[sidx].setAttribute('name', sels[sidx].getAttribute('name').replace(/\d+(\[\])?$/, idx + '$1'));
				}
				var buts = li.getElementsByTagName('button');
				for (var sidx = 0; sidx < buts.length; ++sidx) {
					if (buts[sidx].getAttribute('class') == 'add-field') {
						buts[sidx].onclick = function() {
							addField(this, idx - 1);
							return false;
						};
					}
				}
				++idx;
			}
		}
	};

	var addGroup = function(that) {
		var li = document.createElement('li');
		li.setAttribute('class', 'reg-group');
		var ol = that.parentNode;
		while (ol.tagName != 'OL') {
			ol = ol.previousSibling;
		}
		var p = document.createElement('p');
		li.appendChild(p);
		ol.appendChild(li);
		p.appendChild(document.createTextNode('Beginning '));
		var hours = document.createElement('input');
		hours.setAttribute('name', 'group-hours-0');
		hours.setAttribute('style', 'width: 40px');
		p.appendChild(hours);
		var unit = document.createElement('select');
		unit.setAttribute('name', 'group-time-unit-0');
		unit.setAttribute('style', 'width: 100px');
		var units = ['hour', 'day', 'week'];
		for (var idx = 0; idx < units.length; ++idx) {
			var opt = document.createElement('option');
			opt.setAttribute('value', units[idx]);
			opt.appendChild(document.createTextNode(units[idx] + 's'));
			unit.appendChild(opt);
		}
		p.appendChild(unit);
		p.appendChild(document.createTextNode(' after registration, prompt for: '));
		var fields = document.createElement('ul');
		fields.setAttribute('style', 'margin-top: 0; margin-left: 40px; margin-bottom: 0');
		var fieldLi = document.createElement('li');
		fields.appendChild(fieldLi);
		var fieldSel = document.createElement('select');
		fieldSel.setAttribute('name', 'group-cols-0[]');
		fieldSel.setAttribute('style', 'width: 300px');
		var opt = document.createElement('option');
		opt.appendChild(document.createTextNode('Select profile field...'));
		fieldSel.appendChild(opt);
		for (var k in possibleCols) {
			if (possibleCols.hasOwnProperty(k)) {
				opt = document.createElement('option');
				opt.setAttribute('value', k);
				opt.appendChild(document.createTextNode(possibleCols[k]));
				fieldSel.appendChild(opt);
			}
		}
		var fieldRm = document.createElement('button');
		fieldRm.appendChild(document.createTextNode('Remove field'));
		fieldRm.onclick = function() {
			fields.removeChild(fieldLi);
		};
		fieldLi.appendChild(fieldSel);
		fieldLi.appendChild(fieldRm);
		p.appendChild(fields);

		var af = document.createElement('button');
		af.setAttribute('class', 'add-field');
		af.appendChild(document.createTextNode('Add field'));
		af.setAttribute('style', 'margin-left: 40px');
		p.appendChild(af);

		var rm = document.createElement('button');
		rm.setAttribute('style', 'margin-bottom: 30px');
		rm.appendChild(document.createTextNode('Remove group'));
		rm.onclick = function() {
			ol.removeChild(li);
			renumberGroups();
		};
		li.appendChild(rm);
		renumberGroups();
	}
</script>

<?php
	$this->view('_submenu', 'registration')
	     ->display();
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm" id="item-form">
	<fieldset class="adminform">
		<legend><span>Incremental Registration Options</span></legend>

		<div class="input-wrap">
			<label for="field-popover">Pop-over text</label>
			<textarea name="popover" id="field-popover" rows="10"><?php echo $this->escape($popoverText); ?></textarea>
		</div>

		<div class="input-wrap">
			<label for="field-award-per">Award per field completed</label>
			<input type="text" name="award-per" id="field-award-per" value="<?php echo str_replace('"', '&quot;', $awardPer); ?>" />
		</div>

		<div class="input-wrap">
			<label for="field-test-group">Test group (name or id number)</label>
			<input type="text" name="test-group" id="field-test-group" value="<?php echo str_replace('"', '&quot;', $testGroup); ?>" />
		</div>

		<fieldset>
			<legend>Field groups</legend>

			<p>Packages of profile information to prompt starting some time after registration</p>
			<ol id="reg-groups" style="margin:0 0 0 30px;">
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
				<li class="reg-group">
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
								<button onclick="this.parentNode.parentNode.removeChild(this.parentNode); return false">Remove field</button>
							</li>
							<?php endforeach; ?>
						</ul>
						<button class="add-field" style="margin-left: 40px;" onclick="addField(this, <?php echo $idx; ?>); return false;">Add field</button>
					</p>
					<button onclick="this.parentNode.parentNode.removeChild(this.parentNode); renumberGroups(); return false;" style="margin-bottom: 30px">Remove group</button>
				</li>
				<?php endforeach; ?>
			</ol>
			<p>
				<button onclick="addGroup(this); return false;">Add group</button>
			</p>
		</fieldset>

		<fieldset>
			<legend>Recurrence</legend>

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
					<button onclick="this.parentNode.parentNode.removeChild(this.parentNode); return false">Remove recurrence</button>
				</li>
				<?php
					endforeach;
				?>
			</ol>
			<p>
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
					rm.appendChild(document.createTextNode('Remove field'));
					rm.onclick = function() {
						ol.removeChild(li);
						return false;
					};
					li.appendChild(rm);
					ol.appendChild(li);
				})(this); return false;">Add recurrence</button>
			</p>
			<p>
				After reaching the end of this list: <br />
				<input type="radio" name="repeat-type" checked="checked" /> repeat prompting indefinitely using the last delay listed between attempts<br />
				<input type="radio" name="repeat-type" /> stop prompting
			</p>
		</fieldset>
	</fieldset>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo Html::input('token'); ?>
</form>
