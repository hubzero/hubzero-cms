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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css()
     ->css('providers.css', 'com_users')
     ->js()
     ->js('jquery.hoverIntent', 'system');
?>

<h3 class="section-header"><?php echo Lang::txt('PLG_MEMBERS_ACCOUNT_SET_LOCAL_PASSWORD'); ?></h3>

<?php if (isset($this->notifications) && count($this->notifications) > 0) {
	foreach ($this->notifications as $notification) { ?>
	<p class="<?php echo $notification['type']; ?>"><?php echo $this->escape($notification['message']); ?></p>
	<?php } // close foreach
} // close if count ?>

<?php if ($this->getError()) : ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php endif; ?>

<div id="members-account-section">
	<form action="<?php echo Route::url('index.php?option=' . $this->option .
										'&id=' . $this->id .
										'&active=account' .
										'&task=setlocalpass'); ?>" method="post">
		<fieldset>
			<legend><?php echo Lang::txt('PLG_MEMBERS_ACCOUNT_SET_LOCAL_PASSWORD'); ?></legend>

			<div class="clear"></div>

			<div class="fieldset-grouping">
				<p class="error" id="section-edit-errors"></p>
			</div>

			<div id="password-group"<?php echo (count($this->password_rules) > 0) ? ' class="split-left"' : ""; ?>>
				<div class="fieldset-grouping">
					<label for="password1"><?php echo Lang::txt('PLG_MEMBERS_ACCOUNT_PASSWORD'); ?>:</label>
					<input id="password1" name="password1" type="password" />
				</div>
				<div class="fieldset-grouping">
					<label for="password2"><?php echo Lang::txt('PLG_MEMBERS_ACCOUNT_VERIFY_PASSWORD'); ?>:</label>
					<input id="password2" name="password2" type="password" />
				</div>
			</div>

			<?php
				if (count($this->password_rules) > 0)
				{
					echo '<div id="passrules-container" class="setlocal">';
					echo '<div id="passrules-subcontainer">';
					echo '<h5>Password Rules</h5>';
					echo '<ul id="passrules">';
					foreach ($this->password_rules as $rule)
					{
						if (!empty($rule))
						{
							if (!empty($this->change) && is_array($this->change))
							{
								$err = in_array($rule, $this->change);
							}
							else
							{
								$err = '';
							}
							$mclass = ($err)  ? ' class="error"' : ' class="empty"';
							echo "<li $mclass>".$rule."</li>";
						}
					}
					if (!empty($this->change) && is_array($this->change))
					{
						foreach ($this->change as $msg)
						{
							if (!in_array($msg, $this->password_rules))
							{
								echo '<li class="error">'.$msg."</li>";
							}
						}
					}
					echo "</ul>";
					echo "</div>";
					echo "</div>";
				}
			?>

		</fieldset>

		<div class="clear"></div>
		<p class="submit">
			<input type="hidden" name="change" value="1" />
			<input type="submit" value="<?php echo Lang::txt('PLG_MEMBERS_ACCOUNT_SUBMIT'); ?>" id="password-change-save" />
			<input type="hidden" name="no_html" id="pass_no_html" value="0" />
			<input type="hidden" name="redirect" id="pass_redirect" value="1" />
		</p>
		<?php echo Html::input('token'); ?>
	</form>
</div>
<div class="clear"></div>