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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

use \Hubzero\Utility\Sanitize;

// No direct access.
defined('_HZEXEC_') or die();

$no_html = Request::getInt('no_html', 0);

if (!$no_html)
{
	$this->css();
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>
</header><!-- / #content-header -->

<section class="main section">
<?php } ?>
	<?php if ($this->report) { ?>
		<?php if ($this->getError()) { ?>
			<p class="error"><?php echo $this->getError(); ?></p>
		<?php } ?>
		<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=reportabuse'); ?>" method="post" id="hubForm<?php if ($no_html) { echo '-ajax'; } ?>">
			<?php if (!$no_html) { ?>
			<div class="explaination">
				<p><?php echo Lang::txt('COM_SUPPORT_REPORT_ABUSE_EXPLANATION'); ?></p>
				<p><?php echo Lang::txt('COM_SUPPORT_REPORT_ABUSE_DESCRIPTION_HINT'); ?></p>
			</div>
			<?php } ?>
			<fieldset>
				<legend><?php echo Lang::txt('COM_SUPPORT_REPORT_ABUSE'); ?></legend>

				<?php if (!$no_html) { ?>
				<div class="field-wrap">
					<div class="abuseitem">
						<h4><?php
							$name = Lang::txt('COM_SUPPORT_ANONYMOUS');
							if ($this->report->anon == 0)
							{
								$user = User::getInstance($this->report->author);
								$name = Lang::txt('COM_SUPPORT_UNKNOWN');
								if (is_object($user))
								{
									$name = $user->get('name');
								}
							}

							echo ($this->report->href) ? '<a href="' . $this->report->href . '">': '';
							echo ucfirst($this->cat) . ' by ';
							echo ($this->report->anon != 0) ? Lang::txt('COM_SUPPORT_REPORT_ABUSE_ANONYMOUS') : $name;
							echo ($this->report->href) ? '</a>': '';
						?></h4>
						<?php echo ($this->report->subject) ? '<p><strong>'.stripslashes($this->report->subject).'</strong></p>' : ''; ?>
						<blockquote cite="<?php echo ($this->report->anon != 0) ? Lang::txt('COM_SUPPORT_ANONYMOUS') : $name; ?>">
							<p><?php echo Sanitize::html($this->report->text); ?></p>
						</blockquote>
					</div>
				</div>
				<?php } ?>

				<p class="multiple-option">
					<label class="option" for="subject1"><input type="radio" class="option" name="subject" id="subject1" value="<?php echo Lang::txt('COM_SUPPORT_REPORT_ABUSE_OFFENSIVE'); ?>" checked="checked" /> <?php echo Lang::txt('COM_SUPPORT_REPORT_ABUSE_OFFENSIVE'); ?></label>
					<label class="option" for="subject2"><input type="radio" class="option" name="subject" id="subject2" value="<?php echo Lang::txt('COM_SUPPORT_REPORT_ABUSE_STUPID'); ?>" /> <?php echo Lang::txt('COM_SUPPORT_REPORT_ABUSE_STUPID'); ?></label>
					<label class="option" for="subject3"><input type="radio" class="option" name="subject" id="subject3" value="<?php echo Lang::txt('COM_SUPPORT_REPORT_ABUSE_SPAM'); ?>" /> <?php echo Lang::txt('COM_SUPPORT_REPORT_ABUSE_SPAM'); ?></label>
					<label class="option" for="subject4"><input type="radio" class="option" name="subject" id="subject4" value="<?php echo Lang::txt('COM_SUPPORT_REPORT_ABUSE_OTHER'); ?>" /> <?php echo Lang::txt('COM_SUPPORT_REPORT_ABUSE_OTHER'); ?></label>
				</p>

				<input type="hidden" name="option" value="<?php echo $this->escape($this->option); ?>" />
				<input type="hidden" name="controller" value="<?php echo $this->escape($this->controller); ?>" />
				<input type="hidden" name="task" value="save" />
				<input type="hidden" name="category" value="<?php echo $this->escape($this->cat); ?>" />
				<input type="hidden" name="referenceid" value="<?php echo $this->escape($this->refid); ?>" />
				<input type="hidden" name="link" value="<?php echo $this->escape($this->report->href); ?>" />
				<input type="hidden" name="no_html" value="<?php echo $no_html; ?>" />

				<?php echo Html::input('token'); ?>

				<label for="field-report">
					<?php echo Lang::txt('COM_SUPPORT_REPORT_ABUSE_DESCRIPTION'); ?>
					<textarea name="report" id="field-report" rows="10" cols="50"></textarea>
				</label>
			</fieldset>
			<p class="submit">
				<input type="submit" class="btn btn-danger" value="<?php echo Lang::txt('COM_SUPPORT_SUBMIT'); ?>" />
			</p>
		</form>
		<div class="clear"></div>
	<?php } else { ?>
		<?php if ($this->getError()) { ?>
			<p class="error"><?php echo $this->getError(); ?></p>
		<?php } else { ?>
			<p class="warning"><?php echo Lang::txt('COM_SUPPORT_ERROR_NO_INFO_ON_REPORTED_ITEM'); ?></p>
		<?php } ?>
	<?php } ?>
<?php if (!$no_html) { ?>
</section><!-- / .main section -->
<?php } ?>