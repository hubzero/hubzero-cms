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

Toolbar::title( Lang::txt( 'MEMBERS' ).': Manage Points', 'user.png' );
Toolbar::save( 'process_batch', 'Process Batch' );
Toolbar::cancel();

?>

<?php
	$this->view('_submenu')
	     ->display();
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm" id="item-form">
	<div class="grid">
		<div class="col span7">
			<fieldset class="adminform">
				<legend><span>Process batch transaction</span></legend>

				<div class="grid">
					<div class="col span6">
						<div class="input-wrap">
							<label for="type">Transaction Type:</label>
							<select name="transaction[type]" id="type">
								<option>deposit</option>
								<option>withdraw</option>
							</select>
						</div>
					</div>
					<div class="col span6">
						<div class="input-wrap">
							<label for="amount">Amount:</label>
							<input type="text" name="transaction[amount]" id="amount" maxlength="11" value="" />
						</div>
					</div>
				</div>

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
		<div class="col span5">
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
	</div>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="process_batch" />

	<?php echo Html::input('token'); ?>
</form>