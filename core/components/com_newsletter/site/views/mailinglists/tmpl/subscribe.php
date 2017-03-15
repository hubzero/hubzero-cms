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

$mylistIds = array();

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
			<?php if (count($this->mylists) > 0) : ?>
				<fieldset>
					<legend><?php echo Lang::txt('COM_NEWSLETTER_MAILINGLISTS_MYLISTS'); ?></legend>
					<?php foreach ($this->mylists as $mylist) : ?>
						<?php $mylistIds[] = $mylist->id; ?>
						<?php if ($mylist->status != 'removed') : ?>
							<label>
								<input type="checkbox" name="lists[]" value="<?php echo $mylist->id; ?>" <?php echo ($mylist->status == 'active' || $mylist->status == 'inactive') ? 'checked="checked"' : ''; ?> />
								<strong><?php echo $mylist->name; ?></strong>
								<?php
									if ($mylist->status == 'active' || $mylist->status == 'inactive')
									{
										if (!$mylist->confirmed)
										{
											echo ' - <span title="' . Lang::txt('COM_NEWSLETTER_MAILINGLISTS_NOTCONFIRMED_TOOLTIP') . '" class="unconfirmed tooltips">' . Lang::txt('COM_NEWSLETTER_MAILINGLISTS_NOTCONFIRMED') . '</span> <span class="unconfirmed-link">(<a href="'.Route::url('index.php?option=com_newsletter&task=resendconfirmation&mid='.$mylist->id).'" class="">' . Lang::txt('COM_NEWSLETTER_MAILINGLISTS_CONFIRMLINK_TEXT') . '</a>)</span>';
										}
									}
									else if ($mylist->status == 'unsubscribed')
									{
										echo ' - <span class="unsubscribed">' . Lang::txt('COM_NEWSLETTER_MAILINGLISTS_UNSUBSCRIBED') . '</span>';
									}
								?>
								<span class="desc">
									<?php echo ($mylist->description) ? nl2br($mylist->description) : Lang::txt('COM_NEWSLETTER_MAILINGLISTS_LIST_NODESCRIPTION'); ?>
								</span>
							</label>
						<?php endif; ?>
					<?php endforeach; ?>
				</fieldset>
			<?php endif; ?>

			<?php if (count($this->alllists) > 0) : ?>
				<fieldset>
					<legend><?php echo Lang::txt('COM_NEWSLETTER_MAILINGLISTS_PUBLICLISTS'); ?></legend>
					<?php foreach ($this->alllists as $list) : ?>
						<?php
						if (in_array($list->id, $mylistIds))
						{
							continue;
						}
						?>
						<label>
							<input type="checkbox" name="lists[]" value="<?php echo $list->id; ?>" />
							<strong><?php echo $list->name; ?></strong>
							<span class="desc"><?php echo ($list->description) ? nl2br($list->description) : Lang::txt('COM_NEWSLETTER_MAILINGLISTS_LIST_NODESCRIPTION'); ?></span>
						</label>
					<?php endforeach; ?>
				</fieldset>
			<?php endif; ?>
			<?php if (count($this->mylists) > 0 || count($this->alllists) > 0) : ?>
				<p class="submit">
					<input type="submit" class="btn btn-success" value="<?php echo Lang::txt('COM_NEWSLETTER_MAILINGLISTS_SAVE'); ?>">
				</p>
			<?php else: ?>
				<p class="info">
					<?php echo Lang::txt('COM_NEWSLETTER_MAILINGLISTS_NONE'); ?>
				</p>
			<?php endif; ?>
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
			<input type="hidden" name="task" value="domultisubscribe" />
		</form>
	</div>
</section>