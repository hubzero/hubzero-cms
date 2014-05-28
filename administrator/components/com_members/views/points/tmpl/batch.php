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
defined('_JEXEC') or die( 'Restricted access' );

JToolBarHelper::title( JText::_( 'MEMBERS' ).': Manage Points', 'user.png' );
JToolBarHelper::save( 'process_batch', 'Process Batch' );
JToolBarHelper::cancel();

?>

<?php
	$this->view('_submenu')
	     ->display();
?>

<form action="index.php" method="post" name="adminForm" id="item-form">
	<div class="col width-60 fltlft">
		<fieldset class="adminform">
			<legend><span>Process batch transaction</span></legend>

			<div class="col width-50 fltlft">
				<div class="input-wrap">
					<label for="type">Transaction Type:</label>
					<select name="transaction[type]" id="type">
						<option>deposit</option>
						<option>withdraw</option>
					</select>
				</div>
			</div>
			<div class="col width-50 fltrt">
				<div class="input-wrap">
					<label for="amount">Amount:</label>
					<input type="text" name="transaction[amount]" id="amount" maxlength="11" value="" />
				</div>
			</div>
			<div class="clr"></div>

			<div class="input-wrap">
				<label for="description">Description:</label>
				<input type="text" name="transaction[description]" id="description"  maxlength="250" value="" />
			</div>
			<div class="input-wrap" data-hint="Enter a comma-separated list of userids.">
				<label for="users">User list</label>
				<textarea name="transaction[users]" id="users" rows="10" cols="50"></textarea>
				<span class="hint">Enter a comma-separated list of userids.</span>
			</div>
		</fieldset>
	</div>
	<div class="col width-40 fltrt">
		<fieldset class="adminform">
			<legend><span>Transaction log details</span></legend>

			<div class="input-wrap" data-hint="E.g. answers, survey, etc.">
				<label for="com">Category / Component</label>
				<input type="text" name="log[com]" id="com" maxlength="250" value="" />
				<span class="hint">E.g. answers, survey, etc.</span>
			</div>
			<div class="input-wrap" data-hint="E.g. royalty, setup, etc.">
				<label for="action">Action type</label>
				<input type="text" name="log[action]" id="action" maxlength="250" value="" />
				<span class="hint">E.g. royalty, setup, etc.</span>
			</div>
			<div class="input-wrap">
				<label for="ref">Reference id (optional)</label>
				<input type="text" name="log[ref]" id="ref" maxlength="250" value="" />
			</div>
		</fieldset>
	</div>
	<div class="clr"></div>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="process_batch" />

	<?php echo JHTML::_( 'form.token' ); ?>
</form>