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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css()
     ->js();
?>

<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<div id="content-header-extra">
		<ul>
			<li>
				<a class="btn icon-browse" href="<?php echo Route::url('index.php?option=com_newsletter'); ?>">
					<?php echo Lang::txt('COM_NEWSLETTER_BROWSE'); ?>
				</a>
			</li>
		</ul>
	</div>
</header>

<section class="main section">
	<?php
		if ($this->getError())
		{
			echo '<p class="error">' . $this->getError() . '</p>';
		}
	?>
	<div class="subscribe">
		<form action="index.php" method="post" id="hubForm">
			<fieldset>
				<legend><?php echo Lang::txt('COM_NEWSLETTER_MAILINGLISTS_UNSUBSCRIBE'); ?></legend>
				<p><?php echo Lang::txt('COM_NEWSLETTER_MAILINGLISTS_UNSUBSCRIBE_DESC'); ?></p>

				<p>
					<strong><?php echo $this->mailinglist->name; ?></strong><br />
					<span><?php echo $this->mailinglist->description; ?></span>
					<input type="hidden" name="t" value="<?php echo Request::getVar('t', '') ?>" />
					<input type="hidden" name="e" value="<?php echo Request::getVar('e', ''); ?>" />
				</p>

				<?php if ($this->mailinglist->id == '-1' && User::get('guest') == 1) : ?>
					<ol>
						<li>
							<?php if (User::isGuest()) : ?>
								<a href="login?return=<?php echo base64_encode( $_SERVER['REQUEST_URI'] ); ?>">
									<?php echo Lang::txt('COM_NEWSLETTER_MAILINGLISTS_LOGIN_TO_UNSUBSCRIBE'); ?>
								</a>
							<?php else : ?>
								<span class="complete">
									<?php echo Lang::txt('COM_NEWSLETTER_MAILINGLISTS_LOGGEDIN_AS', User::get('username')); ?>
								</span>
							<?php endif; ?>
						</li>
					</ol>
				<?php else : ?>
					<label><?php echo Lang::txt('COM_NEWSLETTER_UNSUBSCRIBE_REASON'); ?>
						<select name="reason" id="reason">
							<option value=""><?php echo Lang::txt('COM_NEWSLETTER_UNSUBSCRIBE_REASON_DEFAULT'); ?></option>
							<option value="Too many emails"><?php echo Lang::txt('COM_NEWSLETTER_UNSUBSCRIBE_REASON_TOOMANY'); ?></option>
							<option value="Content isn't relevant to me"><?php echo Lang::txt('COM_NEWSLETTER_UNSUBSCRIBE_REASON_NOTRELEVANT'); ?></option>
							<option value="I don't remember signing up"><?php echo Lang::txt('COM_NEWSLETTER_UNSUBSCRIBE_REASON_NOTSIGNEDUP'); ?></option>
							<option value="Privacy concerns"><?php echo Lang::txt('COM_NEWSLETTER_UNSUBSCRIBE_REASON_PRIVACY'); ?></option>
							<option value="Other"><?php echo Lang::txt('COM_NEWSLETTER_UNSUBSCRIBE_REASON_OTHER'); ?></option>
						</select>
					</label>

					<label>
						<textarea rows="4" name="reason-alt" id="reason-alt" placeholder="<?php echo Lang::txt('COM_NEWSLETTER_UNSUBSCRIBE_REASON_OTHER_OTHER'); ?>"></textarea>
					</label>
				<?php endif; ?>
			</fieldset>
			<?php if (!User::isGuest() || $this->mailinglist->id != '-1') : ?>
				<p class="submit">
					<input type="submit" class="btn btn-success" value="<?php echo Lang::txt('COM_NEWSLETTER_UNSUBSCRIBE'); ?>">
				</p>
			<?php endif; ?>
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
			<input type="hidden" name="task" value="dounsubscribe" />
		</form>
	</div>
</section>