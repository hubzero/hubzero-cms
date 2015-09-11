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

// No direct access.
defined('_HZEXEC_') or die();

$base = rtrim(Request::base(true), '/');

$this->css('create.css')
     ->js('create.js');
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<div id="content-header-extra">
		<p>
			<a class="icon-add add btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=draft'); ?>">
				<?php echo Lang::txt('COM_CONTRIBUTE_NEW_SUBMISSION'); ?>
			</a>
		</p>
	</div><!-- / #content-header -->
</header><!-- / #content-header -->

<section class="main section">
	<?php
		$this->view('steps')
		     ->set('option', $this->option)
		     ->set('step', $this->step)
		     ->set('steps', $this->steps)
		     ->set('id', $this->id)
		     ->set('resource', $this->row)
		     ->set('progress', $this->progress)
		     ->display();
	?>
<?php if ($this->getError()) { ?>
	<p class="warning"><?php echo $this->getError(); ?></p>
<?php } ?>
	<form action="<?php echo Route::url('index.php?option=' . $this->option . '&task=draft&step=' . $this->next_step . '&id=' . $this->id); ?>" method="post" id="hubForm">
		<div class="explaination">
			<h4><?php echo Lang::txt('COM_CONTRIBUTE_ATTACH_WHAT_ARE_ATTACHMENTS'); ?></h4>
			<p><?php echo Lang::txt('COM_CONTRIBUTE_ATTACH_EXPLANATION'); ?></p>
			<h4><?php echo Lang::txt('COM_CONTRIBUTE_ACCESS'); ?></h4>
			<p>
				<strong><?php echo Lang::txt('COM_CONTRIBUTE_ACCESS_PUBLIC'); ?></strong> = <?php echo Lang::txt('COM_CONTRIBUTE_ACCESS_PUBLIC_EXPLANATION'); ?><br />
				<strong><?php echo Lang::txt('COM_CONTRIBUTE_ACCESS_REGISTERED'); ?></strong> = <?php echo Lang::txt('COM_CONTRIBUTE_ACCESS_REGISTERED_EXPLANATION'); ?>
			</p>
		</div>
		<fieldset>
			<legend><?php echo Lang::txt('COM_CONTRIBUTE_ATTACH_ATTACHMENTS'); ?></legend>

			<div class="field-wrap">
				<div class="asset-uploader">

					<div class="grid">
						<div class="col span-half">
							<div id="ajax-uploader" data-action="index.php?option=com_resources&amp;no_html=1&amp;controller=attachments&amp;task=save&amp;pid=<?php echo $this->id; ?>" data-list="index.php?option=com_resources&amp;no_html=1&amp;controller=attachments&amp;pid=<?php echo $this->id; ?>">
							</div>
							<script src="<?php echo $base; ?>/core/assets/js/jquery.fileuploader.js"></script>
							<script src="<?php echo $base; ?>/core/components/com_resources/site/assets/js/fileupload.js"></script>
						</div><!-- / .col span-half -->
						<div class="col span-half omega">
							<div id="link-adder" data-action="index.php?option=com_resources&amp;controller=attachments&amp;no_html=1&amp;task=create&amp;pid=<?php echo $this->id; ?>&amp;url=" data-list="index.php?option=com_resources&amp;controller=attachments&amp;no_html=1&amp;pid=<?php echo $this->id; ?>">
							</div>
						</div><!-- / .col span-half omega -->
					</div>

					<iframe width="100%" height="500" frameborder="0" name="attaches" id="attaches" src="index.php?option=<?php echo $this->option; ?>&amp;controller=attachments&amp;id=<?php echo $this->id; ?>&amp;tmpl=component"></iframe>
				</div><!-- / .asset-uploader -->
			</div><!-- / .field-wrap -->

			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
			<input type="hidden" name="task" value="<?php echo $this->task; ?>" />
			<input type="hidden" name="step" value="<?php echo $this->next_step; ?>" />
			<input type="hidden" name="id" value="<?php echo $this->id; ?>" />
		</fieldset><div class="clear"></div>
		<div class="submit">
			<input class="btn btn-success" type="submit" value="<?php echo Lang::txt('COM_CONTRIBUTE_NEXT'); ?>" />
		</div>
	</form>
</section><!-- / .main section -->
